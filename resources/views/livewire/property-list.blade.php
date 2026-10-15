<div class="grid gap-8 lg:grid-cols-[280px_1fr]">
    <div class="hidden lg:block">
        <livewire:property-filters />
    </div>

    <div>
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $properties->total() }} {{ \Illuminate\Support\Str::plural('property', $properties->total()) }} found
                @if ($activeFilterCount > 0)
                    <span class="ml-1 rounded-full bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                        {{ $activeFilterCount }} filter{{ $activeFilterCount > 1 ? 's' : '' }} applied
                    </span>
                @endif
            </p>

            <div class="flex items-center gap-3">
                <label for="sort" class="text-sm text-gray-500 dark:text-gray-400">Sort</label>
                <select id="sort" wire:model.live="sort"
                        class="rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                    <option value="newest">Newest</option>
                    <option value="price_asc">Price: low to high</option>
                    <option value="price_desc">Price: high to low</option>
                    <option value="most_viewed">Most viewed</option>
                    <option value="bedrooms">Most bedrooms</option>
                </select>

                <div class="inline-flex rounded-lg border border-gray-300 p-0.5 dark:border-gray-700" role="group" aria-label="View mode">
                    <button type="button" wire:click="$set('view', 'grid')" wire:key="view-grid"
                            aria-pressed="{{ $view === 'grid' }}"
                            class="{{ $view === 'grid' ? 'bg-brand-700 text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200' }} rounded-md px-3 py-1 text-sm font-medium">
                        Grid
                    </button>
                    <button type="button" wire:click="$set('view', 'map')" wire:key="view-map"
                            aria-pressed="{{ $view === 'map' }}"
                            class="{{ $view === 'map' ? 'bg-brand-700 text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200' }} rounded-md px-3 py-1 text-sm font-medium">
                        Map
                    </button>
                </div>
            </div>
        </div>

        {{-- Skeleton loaders while any Livewire request is in flight --}}
        <div wire:loading wire:target="sort,view,page,keyword,listingType,propertyType,state,city,bedrooms,bathrooms,priceMin,priceMax,amenities,applyFilters"
             class="grid animate-pulse gap-5 sm:grid-cols-2 xl:grid-cols-3" aria-hidden="true">
            @foreach (range(1, 6) as $i)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="h-44 bg-gray-200 dark:bg-gray-800"></div>
                    <div class="space-y-3 p-4">
                        <div class="h-4 w-1/3 rounded bg-gray-200 dark:bg-gray-800"></div>
                        <div class="h-4 w-3/4 rounded bg-gray-200 dark:bg-gray-800"></div>
                        <div class="h-4 w-1/2 rounded bg-gray-200 dark:bg-gray-800"></div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($properties->isEmpty())
            <div wire:loading.remove class="rounded-2xl border border-dashed border-gray-300 p-12 text-center dark:border-gray-700">
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">No properties match your filters</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try broadening your search or resetting the filters.</p>
            </div>
        @elseif ($view === 'map')
            <div wire:loading.remove wire:key="map-{{ md5((string) $mapPoints->toJson() . $sort) }}"
                 x-data
                 x-init="
                     const el = $refs.map;
                     const map = L.map(el).setView([9.082, 8.6753], 6);
                     L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                         attribution: '&copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot;>OpenStreetMap</a> contributors',
                         maxZoom: 19,
                     }).addTo(map);
                     const pts = {{ $mapPoints->toJson() }};
                     const bounds = [];
                     pts.forEach(function (p) {
                         L.marker([p.lat, p.lng]).addTo(map)
                             .bindPopup('<a href=&quot;/properties/' + p.slug + '&quot;><strong>' + p.title + '</strong><br>' + p.price_label + '</a>');
                         bounds.push([p.lat, p.lng]);
                     });
                     if (bounds.length) {
                         map.fitBounds(L.latLngBounds(bounds), { padding: [48, 48] });
                     } else {
                         map.setView([9.082, 8.6753], 6);
                     }
                 "
                 class="relative">
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <div x-ref="map" class="h-[560px] w-full rounded-2xl border border-gray-200 dark:border-gray-800" role="region" aria-label="Map of property results"></div>
                @if ($mapPoints->isEmpty())
                    <p class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400">
                        None of the current results have map coordinates. Try Grid view.
                    </p>
                @endif
            </div>
        @else
            <div wire:loading.remove class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($properties as $property)
                    <x-property-card :property="$property" wire:key="prop-{{ $property->id }}" />
                @endforeach
            </div>

            <div wire:loading.remove class="mt-8">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div>
