<?php

namespace App\Livewire;

use App\Models\Amenity;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\State;
use Livewire\Attributes\On;
use Livewire\Component;

class PropertyFilters extends Component
{
    public ?string $keyword = null;

    public ?string $listingType = null;

    public ?int $propertyType = null;

    public ?string $state = null;

    public ?string $city = null;

    public ?int $bedrooms = null;

    public ?int $bathrooms = null;

    public ?float $priceMin = null;

    public ?float $priceMax = null;

    /** @var list<int> */
    public array $amenities = [];

    public string $sort = 'newest';

    public function mount(): void
    {
        $this->hydrateFromQuery();
    }

    public function updated($property): void
    {
        if ($property === 'propertyType' && $this->propertyType) {
            $this->city = null;
        }

        $this->apply();
    }

    public function updatedAmenities(): void
    {
        $this->apply();
    }

    public function apply(): void
    {
        $this->dispatch('apply-filters', filters: $this->filters());
    }

    public function resetFilters(): void
    {
        $this->keyword = null;
        $this->listingType = null;
        $this->propertyType = null;
        $this->state = null;
        $this->city = null;
        $this->bedrooms = null;
        $this->bathrooms = null;
        $this->priceMin = null;
        $this->priceMax = null;
        $this->amenities = [];
        $this->sort = 'newest';

        $this->apply();
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return array_filter([
            'keyword' => $this->keyword,
            'listing_type' => $this->listingType,
            'property_type' => $this->propertyType,
            'state' => $this->state,
            'city' => $this->city,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
            'amenities' => $this->amenities,
            'sort' => $this->sort !== 'newest' ? $this->sort : null,
        ], fn ($value) => $value !== null && $value !== '' && $value !== [] && $value !== [0]);
    }

    private function hydrateFromQuery(): void
    {
        $query = request()->query();

        $this->keyword = $query['keyword'] ?? $query['q'] ?? null;
        $this->listingType = $query['listing_type'] ?? null;
        $this->propertyType = isset($query['property_type']) ? (int) $query['property_type'] : null;
        $this->state = $query['state'] ?? null;
        $this->city = $query['city'] ?? null;
        $this->bedrooms = isset($query['bedrooms']) ? (int) $query['bedrooms'] : null;
        $this->bathrooms = isset($query['bathrooms']) ? (int) $query['bathrooms'] : null;
        $this->priceMin = isset($query['price_min']) ? (float) $query['price_min'] : null;
        $this->priceMax = isset($query['price_max']) ? (float) $query['price_max'] : null;
        $this->amenities = array_map('intval', (array) ($query['amenities'] ?? []));
        $this->sort = $query['sort'] ?? 'newest';
    }

    #[On('filters-hydrated')]
    public function hydrate(array $filters): void
    {
        $this->keyword = $filters['keyword'] ?? null;
        $this->listingType = $filters['listing_type'] ?? null;
        $this->propertyType = isset($filters['property_type']) ? (int) $filters['property_type'] : null;
        $this->state = $filters['state'] ?? null;
        $this->city = $filters['city'] ?? null;
        $this->bedrooms = isset($filters['bedrooms']) ? (int) $filters['bedrooms'] : null;
        $this->bathrooms = isset($filters['bathrooms']) ? (int) $filters['bathrooms'] : null;
        $this->priceMin = isset($filters['price_min']) ? (float) $filters['price_min'] : null;
        $this->priceMax = isset($filters['price_max']) ? (float) $filters['price_max'] : null;
        $this->amenities = array_map('intval', (array) ($filters['amenities'] ?? []));
        $this->sort = $filters['sort'] ?? 'newest';
    }

    public function render()
    {
        return view('livewire.property-filters', [
            'types' => PropertyType::where('is_active', true)->orderBy('sort_order')->get(),
            'states' => State::where('is_active', true)->orderBy('name')->get(),
            'cities' => City::where('is_active', true)
                ->when($this->state, fn ($q) => $q->whereHas('state', fn ($s) => $s->where('name', $this->state)))
                ->orderBy('name')
                ->get(['cities.id', 'cities.name', 'cities.state_id']),
            'amenityList' => Amenity::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
