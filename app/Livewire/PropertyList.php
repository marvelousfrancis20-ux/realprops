<?php

namespace App\Livewire;

use App\Repositories\Contracts\PropertyRepositoryInterface;
use Livewire\Attributes\On;
use Livewire\Attributes\Url as UrlAttribute;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyList extends Component
{
    use WithPagination;

    #[UrlAttribute]
    public ?string $keyword = null;

    #[UrlAttribute]
    public ?string $listingType = null;

    #[UrlAttribute]
    public ?int $propertyType = null;

    #[UrlAttribute]
    public ?string $state = null;

    #[UrlAttribute]
    public ?string $city = null;

    #[UrlAttribute]
    public ?int $bedrooms = null;

    #[UrlAttribute]
    public ?int $bathrooms = null;

    #[UrlAttribute]
    public ?float $priceMin = null;

    #[UrlAttribute]
    public ?float $priceMax = null;

    /** @var list<int> */
    #[UrlAttribute]
    public array $amenities = [];

    #[UrlAttribute]
    public string $sort = 'newest';

    #[UrlAttribute]
    public string $view = 'grid';

    public function updated($property): void
    {
        if (str_starts_with((string) $property, 'filters.')) {
            return;
        }

        $this->resetPage();
    }

    #[On('apply-filters')]
    public function applyFilters(array $filters): void
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

        $this->resetPage();
    }

    public function render(PropertyRepositoryInterface $repository)
    {
        $filters = array_filter([
            'keyword' => $this->keyword,
            'listing_type' => $this->listingType,
            'property_type' => $this->propertyType,
            'state' => $this->state,
            'city' => $this->city,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
            'amenities' => $this->amenities ?: null,
            'sort' => $this->sort,
        ], fn ($value) => $value !== null && $value !== '' && $value !== []);

        $properties = $repository->paginate($filters, 12);

        return view('livewire.property-list', [
            'properties' => $properties,
            'mapPoints' => $properties->getCollection()
                ->filter(fn ($p) => $p->latitude !== null && $p->longitude !== null)
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'title' => $p->title,
                    'lat' => (float) $p->latitude,
                    'lng' => (float) $p->longitude,
                    'price_label' => $p->formattedPrice(),
                ])
                ->values(),
            'activeFilterCount' => count(array_filter([
                $this->keyword,
                $this->listingType,
                $this->propertyType,
                $this->state,
                $this->city,
                $this->bedrooms,
                $this->bathrooms,
                $this->priceMin,
                $this->priceMax,
                $this->amenities ? 1 : null,
            ])),
        ]);
    }
}
