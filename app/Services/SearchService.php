<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Collection;

/**
 * Keyword search integration (Laravel Scout).
 *
 * Uses the configured Scout driver - the "database" driver works out of the box,
 * Meilisearch can be enabled via SCOUT_DRIVER=meilisearch without code changes.
 */
class SearchService
{
    /**
     * Resolve ids matching a keyword through Scout, or null when no keyword given.
     *
     * @return array<int, int>|null
     */
    public function keywordIds(?string $keyword): ?array
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return null;
        }

        return Property::search($keyword)
            ->take(500)
            ->get()
            ->pluck('id')
            ->all();
    }

    /**
     * Titles matching a keyword (used by the search box autosuggest).
     *
     * @return Collection<int, Property>
     */
    public function suggest(string $keyword, int $limit = 6): Collection
    {
        $ids = $this->keywordIds($keyword);

        if ($ids === null) {
            return collect();
        }

        return Property::query()
            ->approved()
            ->published()
            ->whereIn('id', array_slice($ids, 0, $limit * 3))
            ->with('images')
            ->take($limit)
            ->get();
    }

    /**
     * Normalize raw request input into a canonical filter array.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function normalizeFilters(array $input): array
    {
        $clean = [];

        $stringKeys = ['keyword', 'listing_type', 'property_type', 'city', 'state', 'status', 'sort'];
        foreach ($stringKeys as $key) {
            if (isset($input[$key]) && is_scalar($input[$key]) && trim((string) $input[$key]) !== '') {
                $clean[$key] = trim((string) $input[$key]);
            }
        }

        $numericKeys = ['price_min', 'price_max', 'bedrooms', 'bathrooms', 'city_id', 'type'];
        foreach ($numericKeys as $key) {
            if (isset($input[$key]) && is_numeric($input[$key])) {
                $clean[$key] = (float) $input[$key];
            }
        }

        if (isset($input['amenities']) && is_array($input['amenities'])) {
            $ids = array_values(array_filter(array_map('intval', $input['amenities'])));
            if ($ids !== []) {
                $clean['amenities'] = $ids;
            }
        }

        return $clean;
    }
}
