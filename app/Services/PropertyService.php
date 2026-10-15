<?php

namespace App\Services;

use App\Enums\PropertyStatus;
use App\Events\PropertyPublished;
use App\Events\PropertyStatusChanged;
use App\Models\Property;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PropertyService
{
    public function __construct(
        private readonly PropertyRepositoryInterface $properties,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, User $agent): Property
    {
        $this->guardListingLimit($agent);

        $autoApprove = (bool) Setting::get('listings.auto_approve', $agent->isAdmin());

        $property = new Property($this->attributes($data));
        $property->agent_id = $agent->id;
        $property->status = PropertyStatus::from($data['status'] ?? PropertyStatus::Available->value);
        $property->is_approved = $autoApprove;
        $property->rejected_reason = null;
        $property->published_at = $autoApprove ? now() : null;
        $property->save();

        $this->syncAmenities($property, $data['amenities'] ?? []);
        $this->addImages(
            $property,
            $data['images'] ?? [],
            $data['cover_index'] ?? null,
            $data['image_alts'] ?? []
        );

        activity()
            ->performedOn($property)
            ->causedBy($agent)
            ->log('created property');

        if ($property->is_approved) {
            event(new PropertyPublished($property));
        }

        return $property;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Property $property, array $data): Property
    {
        $property->fill($this->attributes($data));

        if (isset($data['status'])) {
            $property->status = PropertyStatus::from($data['status']);
        }

        $property->save();

        $this->syncAmenities($property, $data['amenities'] ?? []);

        if (! empty($data['remove_image_ids']) && is_array($data['remove_image_ids'])) {
            $this->removeImages($property, $data['remove_image_ids']);
        }

        $this->addImages(
            $property,
            $data['images'] ?? [],
            $data['cover_index'] ?? null,
            $data['image_alts'] ?? []
        );

        $property->searchable();

        return $property;
    }

    public function delete(Property $property): void
    {
        foreach ($property->images as $image) {
            if (filled($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $property->delete();
    }

    public function approve(Property $property, ?User $actor = null): Property
    {
        $property->forceFill([
            'is_approved' => true,
            'rejected_reason' => null,
            'published_at' => $property->published_at ?? now(),
        ])->save();

        $property->searchable();

        event(new PropertyStatusChanged($property, 'approved', $actor));
        event(new PropertyPublished($property));

        return $property;
    }

    public function reject(Property $property, ?string $reason, ?User $actor = null): Property
    {
        $property->forceFill([
            'is_approved' => false,
            'rejected_reason' => $reason,
        ])->save();

        $property->searchable();

        event(new PropertyStatusChanged($property, 'rejected', $actor, $reason));

        return $property;
    }

    public function feature(Property $property, bool $featured, ?User $actor = null, ?string $until = null): Property
    {
        $property->forceFill([
            'is_featured' => $featured,
            'featured_until' => $featured ? ($until ?: now()->addMonth()->toDateString()) : null,
        ])->save();

        $property->searchable();

        event(new PropertyStatusChanged($property, $featured ? 'featured' : 'unfeatured', $actor));

        return $property;
    }

    public function incrementViews(Property $property): void
    {
        $this->properties->incrementViews($property);
    }

    /**
     * @param  list<mixed>  $ids
     */
    public function syncAmenities(Property $property, array $ids): void
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));

        $property->amenities()->sync($ids);
    }

    /**
     * @param  mixed  $files
     * @param  array<mixed>  $alts
     */
    public function addImages(Property $property, $files, ?int $coverIndex, array $alts): void
    {
        if (! is_array($files)) {
            return;
        }

        $files = array_values(array_filter($files, fn ($file) => $file instanceof UploadedFile));

        if ($files === []) {
            return;
        }

        $existingCount = $property->images()->count();

        foreach ($files as $index => $file) {
            $stored = $file->store('properties/'.$property->id, 'public');

            $property->images()->create([
                'path' => $stored,
                'url' => null,
                'alt' => $alts[$index] ?? null,
                'sort_order' => $existingCount + $index,
                'is_cover' => ($coverIndex === null ? $index === 0 : $coverIndex === $index)
                    && $existingCount === 0,
            ]);
        }

        if ($coverIndex !== null) {
            $this->setCover($property, $existingCount + $coverIndex);
        }
    }

    /**
     * @param  list<int|string>  $ids
     */
    public function removeImages(Property $property, array $ids): void
    {
        $images = $property->images()->whereIn('id', array_map('intval', $ids))->get();

        foreach ($images as $image) {
            if (filled($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
        }
    }

    public function setCover(Property $property, int $imageId): void
    {
        $property->images()->update(['is_cover' => false]);
        $property->images()->whereKey($imageId)->update(['is_cover' => true]);
    }

    private function guardListingLimit(User $agent): void
    {
        if ($agent->isAdmin()) {
            return;
        }

        if (! $agent->canPublishProperty()) {
            $limit = $agent->listingLimit();

            throw ValidationException::withMessages([
                'plan' => 'Your current plan allows '.$limit.' listing(s). Upgrade your subscription to publish more.',
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data): array
    {
        return [
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'currency' => strtoupper($data['currency'] ?? 'NGN'),
            'listing_type' => $data['listing_type'],
            'property_type_id' => $data['property_type_id'],
            'bedrooms' => $data['bedrooms'] ?? 0,
            'bathrooms' => $data['bathrooms'] ?? 0,
            'toilets' => $data['toilets'] ?? 0,
            'square_meters' => $data['square_meters'] ?? null,
            'year_built' => $data['year_built'] ?? null,
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'] ?? 'Nigeria',
            'city_id' => $data['city_id'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'virtual_tour_url' => $data['virtual_tour_url'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ];
    }
}
