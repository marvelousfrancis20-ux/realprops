@props(['property', 'showAgent' => true])

<article class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
    <a href="{{ route('properties.show', $property) }}" class="block">
        <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-800">
            @if ($property->coverUrl())
                <img src="{{ $property->coverUrl() }}"
                     alt="{{ $property->imageAlt() }}"
                     class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                     loading="lazy">
            @else
                <div class="flex h-full items-center justify-center text-gray-400">No photo</div>
            @endif

            <div class="absolute left-3 top-3 flex gap-2">
                @if ($property->is_featured)
                    <span class="rounded-full bg-accent-500 px-2.5 py-1 text-xs font-semibold text-white">Featured</span>
                @endif
                <span class="rounded-full bg-brand-700 px-2.5 py-1 text-xs font-semibold text-white capitalize">
                    For {{ $property->listing_type }}
                </span>
            </div>
        </div>
    </a>

    <div class="p-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-lg font-bold text-brand-700 dark:text-brand-400">{{ $property->formattedPrice() }}</p>
                <a href="{{ route('properties.show', $property) }}" class="mt-1 line-clamp-2 text-sm font-semibold text-gray-900 hover:text-brand-700 dark:text-gray-100">
                    {{ $property->title }}
                </a>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ $property->city }}, {{ $property->state }}
                </p>
            </div>

            <livewire:favorite-button :property="$property" :key="'fav-'.$property->id" />
        </div>

        <div class="mt-3 flex items-center gap-4 border-t border-gray-100 pt-3 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400">
            <span>{{ $property->bedrooms }} bed</span>
            <span>{{ $property->bathrooms }} bath</span>
            @if ($property->square_meters)
                <span>{{ number_format($property->square_meters) }} m²</span>
            @endif
            <span class="ml-auto capitalize">{{ $property->status }}</span>
        </div>

        @if ($showAgent && $property->agent)
            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($property->agent->name, 0, 1)) }}
                </span>
                {{ $property->agent->name }}
            </div>
        @endif
    </div>
</article>
