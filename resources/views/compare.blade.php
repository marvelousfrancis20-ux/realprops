<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Compare properties</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ count($ids) }} of {{ $max }} properties selected.
        </p>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if ($properties->isEmpty())
                <p class="rounded-xl border border-dashed border-gray-300 p-12 text-center text-sm text-gray-500 dark:border-gray-700">
                    Nothing to compare yet. Tap the compare button on any listing to add it here.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 pe-4 text-start font-semibold text-gray-900 dark:text-gray-100">Feature</th>

                                @foreach ($properties as $property)
                                    <th class="px-4 py-3 text-start align-bottom">
                                        <div class="relative">
                                            <img src="{{ $property->imageUrl() }}" alt="{{ $property->title }}"
                                                 class="mb-2 h-28 w-full rounded-xl object-cover">
                                            <a href="{{ route('properties.show', $property) }}"
                                               class="line-clamp-2 text-xs font-semibold text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                                {{ $property->title }}
                                            </a>
                                            <form method="POST" action="{{ route('compare.remove', $property) }}" class="mt-1">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-[11px] font-medium text-rose-600 hover:underline">Remove</button>
                                            </form>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php
                                $rows = [
                                    'Price' => fn ($p) => $p->formattedPrice(),
                                    'Type' => fn ($p) => $p->type?->name ?? '—',
                                    'Listing' => fn ($p) => $p->listing_type?->label() ?? '—',
                                    'Bedrooms' => fn ($p) => $p->bedrooms,
                                    'Bathrooms' => fn ($p) => $p->bathrooms,
                                    'Toilets' => fn ($p) => $p->toilets,
                                    'Size' => fn ($p) => $p->square_meters ? number_format($p->square_meters).' m²' : '—',
                                    'Year built' => fn ($p) => $p->year_built ?? '—',
                                    'Location' => fn ($p) => $p->locationLine(),
                                    'Views' => fn ($p) => number_format($p->views_count),
                                    'Agent' => fn ($p) => $p->agent?->name ?? '—',
                                ];
                            @endphp

                            @foreach ($rows as $label => $value)
                                <tr>
                                    <td class="py-3 pe-4 font-medium text-gray-500 dark:text-gray-400">{{ $label }}</td>

                                    @foreach ($properties as $property)
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                            {{ $value($property) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach

                            <tr>
                                <td class="py-3 pe-4 font-medium text-gray-500 dark:text-gray-400">Amenities</td>

                                @foreach ($properties as $property)
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $property->amenities->pluck('name')->take(6)->implode(', ') ?: '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="{{ route('compare.clear') }}" class="mt-6">
                    @csrf @method('DELETE')
                    <x-danger-button>Clear all</x-danger-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
