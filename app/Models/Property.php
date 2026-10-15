<?php

namespace App\Models;

use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Models\Concerns\HasSlug;
use App\Support\PhotoPool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Property extends Model
{
    use HasFactory, HasSlug, LogsActivity, Searchable, SoftDeletes;

    /** @var array<int, string|null> */
    protected static array $typeNames = [];

    /** @var list<string> */
    protected $fillable = [
        'agent_id',
        'property_type_id',
        'city_id',
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'listing_type',
        'status',
        'bedrooms',
        'bathrooms',
        'toilets',
        'square_meters',
        'year_built',
        'is_featured',
        'is_approved',
        'rejected_reason',
        'featured_until',
        'address',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'video_url',
        'virtual_tour_url',
        'meta_title',
        'meta_description',
        'og_image',
        'views_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'latitude' => 'float',
            'longitude' => 'float',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'toilets' => 'integer',
            'square_meters' => 'integer',
            'year_built' => 'integer',
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
            'views_count' => 'integer',
            'published_at' => 'datetime',
            'listing_type' => ListingType::class,
            'status' => PropertyStatus::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title', 'price', 'status', 'is_approved', 'is_featured', 'agent_id',
            ])
            ->logOnlyDirty();
    }

    public function slugSource(): string
    {
        return 'title';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function tourRequests(): HasMany
    {
        return $this->hasMany(TourRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('is_approved', false);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', PropertyStatus::Available);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeForListingType(Builder $query, ListingType|string|null $type): Builder
    {
        if ($type === null || $type === '') {
            return $query;
        }

        return $query->where('listing_type', $type instanceof ListingType ? $type->value : $type);
    }

    public function scopeForPropertyType(Builder $query, int|string|null $type): Builder
    {
        if ($type === null || $type === '') {
            return $query;
        }

        return $query->where('property_type_id', $type);
    }

    public function scopeInCity(Builder $query, ?string $city): Builder
    {
        if (blank($city)) {
            return $query;
        }

        return $query->where('city', $city);
    }

    public function scopeInState(Builder $query, ?string $state): Builder
    {
        if (blank($state)) {
            return $query;
        }

        return $query->where('state', $state);
    }

    public function scopePriceBetween(Builder $query, $min, $max): Builder
    {
        if (filled($min)) {
            $query->where('price', '>=', (float) $min);
        }

        if (filled($max)) {
            $query->where('price', '<=', (float) $max);
        }

        return $query;
    }

    public function scopeForAgent(Builder $query, User|int $agent): Builder
    {
        return $query->where('agent_id', $agent instanceof User ? $agent->id : $agent);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / accessors
    |--------------------------------------------------------------------------
    */

    public function coverImage(): ?PropertyImage
    {
        return $this->images->firstWhere('is_cover', true) ?? $this->images->first();
    }

    public function imageUrl(): string
    {
        $cover = $this->coverImage();

        return $cover?->image_url ?? $this->photoUrl();
    }

    public function photoPool(): string
    {
        return PhotoPool::poolFor($this->title)
            ?? PhotoPool::poolFor($this->propertyTypeName())
            ?? 'house';
    }

    public function photoUrl(int $index = 0): string
    {
        return PhotoPool::url($this->photoPool(), $this->id ?? (string) $this->title, $index);
    }

    private function propertyTypeName(): ?string
    {
        $typeId = $this->property_type_id;

        if ($typeId === null) {
            return null;
        }

        if (! array_key_exists($typeId, static::$typeNames)) {
            static::$typeNames[$typeId] = PropertyType::query()->find($typeId)?->name;
        }

        return static::$typeNames[$typeId];
    }

    public function coverUrl(): ?string
    {
        return $this->coverImage()?->image_url;
    }

    public function imageAlt(string $fallback = ''): string
    {
        return $this->coverImage()?->alt
            ?: ($fallback !== '' ? $fallback : Str::limit($this->title ?? '', 60));
    }

    public function galleryUrls(): array
    {
        $urls = $this->images->map(fn (PropertyImage $image) => $image->image_url)->filter()->values()->all();

        return $urls ?: [$this->imageUrl()];
    }

    public function formattedPrice(): string
    {
        return self::currencySymbol($this->currency).number_format((float) $this->price, 0)
            .$this->priceSuffix();
    }

    public function priceSuffix(): string
    {
        $type = $this->listing_type instanceof ListingType ? $this->listing_type : ListingType::from((string) $this->listing_type);

        return match ($type) {
            ListingType::Rent => '/month',
            ListingType::ShortLet => '/night',
            default => '',
        };
    }

    public static function currencySymbol(string $currency): string
    {
        return match (strtoupper($currency)) {
            'NGN', '₦' => '₦',
            'USD', '$' => '$',
            'GBP', '£' => '£',
            'EUR', '€' => '€',
            'GHS' => '₵',
            'KES' => 'KSh ',
            'ZAR' => 'R ',
            default => $currency.' ',
        };
    }

    public function locationLine(): string
    {
        return collect([$this->address, $this->city, $this->state])->filter()->implode(', ');
    }

    public function isAvailable(): bool
    {
        return $this->status === PropertyStatus::Available;
    }

    public function isRental(): bool
    {
        return $this->listing_type instanceof ListingType
            ? $this->listing_type->isRental()
            : ListingType::from((string) $this->listing_type)->isRental();
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function related(int $limit = 4)
    {
        return static::query()
            ->approved()
            ->available()
            ->where('id', '!=', $this->id)
            ->where(function (Builder $query) {
                $query->where('city', $this->city)
                    ->orWhere('property_type_id', $this->property_type_id);
            })
            ->with(['images', 'agent', 'type'])
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Scout
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => strip_tags((string) $this->description),
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'listing_type' => $this->listing_type?->value ?? '',
            'status' => $this->status?->value ?? '',
            'property_type_id' => $this->property_type_id,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'is_approved' => (bool) $this->is_approved,
            'is_featured' => (bool) $this->is_featured,
            'agent_id' => $this->agent_id,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->is_approved && $this->deleted_at === null;
    }
}
