<aside class="space-y-5 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Filters</h2>

        <button type="button" wire:click="resetFilters"
                class="text-xs font-medium text-brand-700 hover:underline dark:text-brand-400">
            Reset all
        </button>
    </div>

    <div>
        <label for="keyword" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Keyword</label>
        <input id="keyword" type="text" wire:model.live.debounce.400ms="keyword"
               placeholder="e.g. 3 bedroom in Lekki"
               class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
    </div>

    <div>
        <span class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Listing type</span>
        <div class="flex gap-2">
            <button type="button" wire:click="$set('listingType', null)"
                    class="rounded-lg border px-3 py-1.5 text-sm {{ $listingType === null ? 'border-brand-700 bg-brand-700 text-white' : 'border-gray-300 text-gray-600 dark:border-gray-700 dark:text-gray-300' }}">
                Any
            </button>
            <button type="button" wire:click="$set('listingType', 'sale')"
                    class="rounded-lg border px-3 py-1.5 text-sm {{ $listingType === 'sale' ? 'border-brand-700 bg-brand-700 text-white' : 'border-gray-300 text-gray-600 dark:border-gray-700 dark:text-gray-300' }}">
                For sale
            </button>
            <button type="button" wire:click="$set('listingType', 'rent')"
                    class="rounded-lg border px-3 py-1.5 text-sm {{ $listingType === 'rent' ? 'border-brand-700 bg-brand-700 text-white' : 'border-gray-300 text-gray-600 dark:border-gray-700 dark:text-gray-300' }}">
                For rent
            </button>
        </div>
    </div>

    <div>
        <label for="property_type" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Property type</label>
        <select id="property_type" wire:model.live="propertyType"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
            <option value="">Any type</option>
            @foreach ($types as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="state" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">State</label>
        <select id="state" wire:model.live="state"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
            <option value="">All states</option>
            @foreach ($states as $item)
                <option value="{{ $item->name }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="city" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">City</label>
        <select id="city" wire:model.live="city"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
            <option value="">All cities</option>
            @foreach ($cities as $item)
                <option value="{{ $item->name }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="bedrooms" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Beds (min)</label>
            <select id="bedrooms" wire:model.live="bedrooms"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                <option value="">Any</option>
                @foreach (range(1, 8) as $n)
                    <option value="{{ $n }}">{{ $n }}+</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="bathrooms" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Baths (min)</label>
            <select id="bathrooms" wire:model.live="bathrooms"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                <option value="">Any</option>
                @foreach (range(1, 6) as $n)
                    <option value="{{ $n }}">{{ $n }}+</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="price_min" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Min price</label>
            <input id="price_min" type="number" min="0" wire:model.live.debounce.600ms="priceMin" placeholder="0"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
        </div>

        <div>
            <label for="price_max" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Max price</label>
            <input id="price_max" type="number" min="0" wire:model.live.debounce.600ms="priceMax" placeholder="Any"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
        </div>
    </div>

    <div>
        <span class="mb-2 block text-xs font-medium text-gray-600 dark:text-gray-300">Amenities</span>
        <div class="max-h-48 space-y-1.5 overflow-y-auto pr-1">
            @foreach ($amenityList as $amenity)
                <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="checkbox" wire:model.live="amenities" value="{{ $amenity->id }}"
                           class="rounded border-gray-300 text-brand-700 focus:ring-brand-600">
                    {{ $amenity->name }}
                </label>
            @endforeach
        </div>
    </div>
</aside>
