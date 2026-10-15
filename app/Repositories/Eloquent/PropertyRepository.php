<?php

namespace App\Repositories\Eloquent;

use App\Models\Property;
use App\Models\User;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\SearchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PropertyRepository implements PropertyRepositoryInterface
{
    public function __construct(private readonly SearchService $search) {}

    public function paginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->query($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateApproved(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->paginate($filters, $perPage);
    }

    public function paginateFeatured(int $perPage = 12): LengthAwarePaginator
    {
        return Property::query()
            ->approved()
            ->available()
            ->featured()
            ->published()
            ->with(['images', 'agent.agentProfile', 'type'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function query(array $filters = []): Builder
    {
        $query = Property::query()
            ->approved()
            ->published()
            ->with(['images', 'agent.agentProfile', 'type']);

        $keywordIds = $this->search->keywordIds($filters['keyword'] ?? null);

        if ($keywordIds !== null) {
            $query->whereIn('id', $keywordIds);
        }

        if (! empty($filters['listing_type'])) {
            $query->where('listing_type', $filters['listing_type']);
        }

        if (! empty($filters['property_type'])) {
            $query->where('property_type_id', (int) $filters['property_type']);
        }

        if (! empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (! empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['bedrooms'])) {
            $query->where('bedrooms', '>=', (int) $filters['bedrooms']);
        }

        if (isset($filters['bathrooms'])) {
            $query->where('bathrooms', '>=', (int) $filters['bathrooms']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        if (! empty($filters['amenities']) && is_array($filters['amenities'])) {
            $query->whereHas('amenities', function (Builder $amenityQuery) use ($filters) {
                $amenityQuery->whereIn('amenities.id', $filters['amenities']);
            });
        }

        // Featured listings always surface first, then the requested sort.
        $query->orderByDesc('is_featured');

        [$sortColumn, $sortDirection] = match ($filters['sort'] ?? 'newest') {
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'most_viewed' => ['views_count', 'desc'],
            'bedrooms' => ['bedrooms', 'desc'],
            default => ['published_at', 'desc'],
        };

        $query->orderBy($sortColumn, $sortDirection);

        return $query;
    }

    public function findBySlug(string $slug): Property
    {
        return Property::query()
            ->where('slug', $slug)
            ->with([
                'images', 'amenities', 'type', 'city',
                'agent.agentProfile', 'inquiries', 'favorites',
            ])
            ->firstOrFail();
    }

    public function featured(int $limit = 8): Collection
    {
        return Property::query()
            ->approved()
            ->available()
            ->featured()
            ->published()
            ->with(['images', 'agent.agentProfile', 'type'])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    public function latest(int $limit = 8): Collection
    {
        return Property::query()
            ->approved()
            ->published()
            ->with(['images', 'agent.agentProfile', 'type'])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    public function mostViewed(int $limit = 8): Collection
    {
        return Property::query()
            ->approved()
            ->available()
            ->published()
            ->with(['images', 'agent.agentProfile', 'type'])
            ->orderByDesc('views_count')
            ->take($limit)
            ->get();
    }

    public function byAgent(User $agent, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $agent->properties()->with(['images', 'type']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['q'])) {
            $query->where(function (Builder $inner) use ($filters) {
                $inner->where('title', 'like', '%'.$filters['q'].'%')
                    ->orWhere('city', 'like', '%'.$filters['q'].'%');
            });
        }

        if (! empty($filters['approval'])) {
            $query->where('is_approved', $filters['approval'] === 'approved');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function similar(Property $property, int $limit = 4): Collection
    {
        return $property->related($limit);
    }

    public function incrementViews(Property $property): void
    {
        Property::query()->whereKey($property->id)->increment('views_count');
    }
}
