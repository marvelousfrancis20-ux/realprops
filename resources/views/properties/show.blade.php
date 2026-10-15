<x-site.layout :title="$property->meta_title ?: $property->title.' — Real Props'" :description="$property->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($property->description), 150)">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-breadcrumbs :items="[
            ['label' => 'Properties', 'url' => route('properties.index')],
            ['label' => $property->title],
        ]" />

        {{-- Gallery --}}
        <div class="grid gap-3 lg:grid-cols-[2fr_1fr]">
            <div class="relative aspect-[16/10] overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                <img src="{{ $property->imageUrl() }}" alt="{{ $property->imageAlt() }}" class="h-full w-full object-cover">
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-1">
                @foreach (array_slice($property->galleryUrls(), 1, 3) as $url)
                    <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                        <img src="{{ $url }}" alt="{{ $property->title }}" class="h-full w-full object-cover" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_380px]">
            <div>
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-brand-700 px-3 py-1 text-xs font-semibold text-white capitalize">
                                For {{ $property->listing_type }}
                            </span>

                            @if ($property->is_featured)
                                <span class="rounded-full bg-accent-500 px-3 py-1 text-xs font-semibold text-white">Featured</span>
                            @endif

                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold capitalize text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                {{ $property->status }}
                            </span>
                        </div>

                        <h1 class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $property->title }}</h1>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $property->locationLine() }}</p>
                    </div>

                    <div class="text-right">
                        <p class="text-3xl font-extrabold text-brand-700 dark:text-brand-400">{{ $property->formattedPrice() }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ number_format($property->views_count) }} views</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl border border-gray-200 p-4 text-center dark:border-gray-800">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $property->bedrooms }}</p>
                        <p class="text-xs text-gray-500">Bedrooms</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-4 text-center dark:border-gray-800">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $property->bathrooms }}</p>
                        <p class="text-xs text-gray-500">Bathrooms</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-4 text-center dark:border-gray-800">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $property->toilets }}</p>
                        <p class="text-xs text-gray-500">Toilets</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-4 text-center dark:border-gray-800">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $property->square_meters ? number_format($property->square_meters).' m²' : '—' }}
                        </p>
                        <p class="text-xs text-gray-500">Size</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Description</h2>
                    <div class="prose prose-gray mt-3 max-w-none text-sm leading-relaxed text-gray-600 dark:prose-invert dark:text-gray-300">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </div>

                @if ($property->amenities->isNotEmpty())
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Amenities</h2>
                        <ul class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($property->amenities as $amenity)
                                <li class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">
                                    <span>{{ $amenity->icon ?: '✓' }}</span>
                                    {{ $amenity->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($property->latitude && $property->longitude)
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Location</h2>
                        <div id="property-map" class="mt-3 h-80 rounded-2xl border border-gray-200 dark:border-gray-800"
                             data-lat="{{ $property->latitude }}" data-lng="{{ $property->longitude }}"
                             data-label="{{ e($property->title) }}"></div>
                    </div>
                @endif

                @if ($similar->isNotEmpty())
                    <div class="mt-12">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Similar properties</h2>
                        <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($similar as $item)
                                <x-property-card :property="$item" />
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($recentlyViewed->isNotEmpty())
                    <div class="mt-12">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recently viewed</h2>
                        <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($recentlyViewed as $item)
                                <x-property-card :property="$item" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="space-y-5">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Contact the agent</h3>

                    @if ($property->agent)
                        <div class="mt-4 flex items-center gap-3">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($property->agent->name, 0, 1)) }}
                            </span>
                            <div>
                                <a href="{{ route('agents.show', $property->agent->id) }}" class="font-semibold text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                    {{ $property->agent->name }}
                                </a>
                                <p class="text-xs text-gray-500">
                                    {{ $property->agent->agentProfile?->agency_name ?? 'Independent agent' }}
                                    @if ($property->agent->isVerifiedAgent())
                                        · <span class="text-brand-700 dark:text-brand-400">Verified</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            @if ($property->agent->phone)
                                <a href="tel:{{ $property->agent->phone }}"
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 hover:border-brand-600 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                                    Call
                                </a>
                            @endif
                            @if ($property->agent->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $property->agent->whatsapp) }}" target="_blank" rel="noopener"
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 hover:border-brand-600 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <livewire:inquiry-form :property="$property" :key="'inq-'.$property->id" />

                <livewire:inquiry-form :property="$property" :is-tour="true" :key="'tour-'.$property->id" />

                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <livewire:favorite-button :property="$property" :key="'fav-detail-'.$property->id" />
                    <livewire:compare-button :property="$property" :key="'cmp-'.$property->id" />
                </div>
            </aside>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (function () {
                var el = document.getElementById('property-map');
                if (!el || typeof L === 'undefined') return;

                var lat = parseFloat(el.dataset.lat);
                var lng = parseFloat(el.dataset.lng);

                var map = L.map(el).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map).bindPopup(el.dataset.label);
            })();
        </script>
    @endpush
</x-site.layout>
