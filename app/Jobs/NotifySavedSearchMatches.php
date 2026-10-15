<?php

namespace App\Jobs;

use App\Models\Property;
use App\Models\SavedSearch;
use App\Notifications\SavedSearchMatchNotification;
use App\Services\SearchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Checks a saved search for newly published matches and notifies the owner.
 */
class NotifySavedSearchMatches implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public readonly int $savedSearchId,
        public readonly int $propertyId,
    ) {}

    public function handle(): void
    {
        $savedSearch = SavedSearch::query()->find($this->savedSearchId);
        $property = Property::query()->find($this->propertyId);

        if (! $savedSearch || ! $property || ! $savedSearch->notify || ! $savedSearch->user) {
            return;
        }

        if (! $this->matches($property, $savedSearch->filters ?? [])) {
            return;
        }

        $savedSearch->user->notify(new SavedSearchMatchNotification(
            $savedSearch,
            collect([$property])
        ));

        $savedSearch->increment('matches_count');
        $savedSearch->forceFill(['last_notified_at' => now()])->saveQuietly();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function matches(Property $property, array $filters): bool
    {
        if (filled($filters['keyword'] ?? null)) {
            $ids = app(SearchService::class)->keywordIds($filters['keyword']);

            if ($ids !== null && ! in_array($property->id, $ids, true)) {
                return false;
            }
        }

        if (filled($filters['listing_type'] ?? null) && $property->listing_type->value !== $filters['listing_type']) {
            return false;
        }

        if (filled($filters['city'] ?? null) && $property->city !== $filters['city']) {
            return false;
        }

        if (filled($filters['state'] ?? null) && $property->state !== $filters['state']) {
            return false;
        }

        if (isset($filters['property_type']) && (int) $filters['property_type'] !== (int) $property->property_type_id) {
            return false;
        }

        if (isset($filters['bedrooms']) && $property->bedrooms < (int) $filters['bedrooms']) {
            return false;
        }

        if (isset($filters['bathrooms']) && $property->bathrooms < (int) $filters['bathrooms']) {
            return false;
        }

        if (isset($filters['price_min']) && $property->price < (float) $filters['price_min']) {
            return false;
        }

        if (isset($filters['price_max']) && $property->price > (float) $filters['price_max']) {
            return false;
        }

        if (! empty($filters['amenities'])) {
            $propertyAmenityIds = $property->amenities()->pluck('amenities.id')->all();

            if (array_diff($filters['amenities'], $propertyAmenityIds) !== []) {
                return false;
            }
        }

        return true;
    }
}
