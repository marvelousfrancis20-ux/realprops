<x-site.layout :title="'Properties for sale and rent — Real Props'">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                @if (($filters['listing_type'] ?? null) === 'rent')
                    Properties for rent
                @elseif (($filters['listing_type'] ?? null) === 'sale')
                    Properties for sale
                @else
                    All properties
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Use the filters to narrow down by price, location, bedrooms and amenities.
            </p>
        </div>

        <livewire:property-list />
    </div>
</x-site.layout>
