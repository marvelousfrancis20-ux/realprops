<?php

namespace App\Repositories\Contracts;

use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface PropertyRepositoryInterface
{
    /**
     * Paginated, filtered, sorted public listing.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    /**
     * Public listing for the API (approved + published only).
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginateApproved(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function paginateFeatured(int $perPage = 12): LengthAwarePaginator;

    /**
     * Query builder with filters applied (used by map view + counts).
     *
     * @param  array<string, mixed>  $filters
     */
    public function query(array $filters = []): Builder;

    public function findBySlug(string $slug): Property;

    public function featured(int $limit = 8): Collection;

    public function latest(int $limit = 8): Collection;

    public function mostViewed(int $limit = 8): Collection;

    public function byAgent(User $agent, array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function similar(Property $property, int $limit = 4): Collection;

    public function incrementViews(Property $property): void;
}
