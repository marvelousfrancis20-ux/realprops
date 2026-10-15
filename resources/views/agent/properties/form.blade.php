<x-panel.layout :panel="$property->exists ? 'Edit property' : 'Add property'" :menu="[
    ['label' => 'Dashboard', 'href' => route('agent.dashboard'), 'active' => 'agent.dashboard', 'icon' => '🏠'],
    ['label' => 'My properties', 'href' => route('agent.properties.index'), 'active' => 'agent.properties.*', 'icon' => '🏢'],
    ['label' => 'Add property', 'href' => route('agent.properties.create'), 'active' => 'agent.properties.create', 'icon' => '➕'],
    ['label' => 'Inquiries', 'href' => route('agent.inquiries.index'), 'active' => 'agent.inquiries.*', 'icon' => '✉️'],
    ['label' => 'Tour requests', 'href' => route('agent.tours.index'), 'active' => 'agent.tours.*', 'icon' => '📅'],
    ['label' => 'Analytics', 'href' => route('agent.analytics'), 'active' => 'agent.analytics', 'icon' => '📊'],
    ['label' => 'Subscription', 'href' => route('agent.subscription.index'), 'active' => 'agent.subscription.*', 'icon' => '💳'],
    ['label' => 'Profile', 'href' => route('agent.profile.edit'), 'active' => 'agent.profile.*', 'icon' => '👤'],
]">
    @if ($listingLimit > 0 && $listingCount >= $listingLimit && ! $property->exists)
        <div class="mb-5 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-200">
            You have reached your plan limit ({{ $listingCount }}/{{ $listingLimit }} listings).
            <a href="{{ route('agent.subscription.index') }}" class="font-semibold underline">Upgrade your plan</a> to publish more.
        </div>
    @endif

    <form method="POST"
          action="{{ $property->exists ? route('agent.properties.update', $property) : route('agent.properties.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if ($property->exists) @method('PUT') @endif

        {{-- Basics --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Basics</h2>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="title" value="Title *" />
                    <x-text-input id="title" name="title" :value="old('title', $property->title)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="description" value="Description *" />
                    <textarea id="description" name="description" rows="6" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">{{ old('description', $property->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="price" value="Price *" />
                    <x-text-input id="price" type="number" step="0.01" name="price" :value="old('price', $property->price)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('price')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="currency" value="Currency *" />
                    <select id="currency" name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                        @foreach (['NGN'] as $code)
                            <option value="{{ $code }}" @selected(old('currency', $property->currency) === $code)>{{ $code }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="listing_type" value="Listing type *" />
                    <select id="listing_type" name="listing_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                        @foreach (\App\Enums\ListingType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(old('listing_type', $property->listing_type) === $type->value)>
                                {{ $type->value === 'sale' ? 'For sale' : ($type->value === 'rent' ? 'For rent' : \Illuminate\Support\Str::headline($type->value)) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('listing_type')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="property_type_id" value="Property type *" />
                    <select id="property_type_id" name="property_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950" required>
                        <option value="">Select a type</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected(old('property_type_id', $property->property_type_id) === $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('property_type_id')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="status" value="Availability status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                        @foreach (\App\Enums\PropertyStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $property->status) === $status->value)>
                                {{ \Illuminate\Support\Str::headline($status->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Details</h2>

            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <x-input-label for="bedrooms" value="Bedrooms *" />
                    <x-text-input id="bedrooms" type="number" name="bedrooms" :value="old('bedrooms', $property->bedrooms)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('bedrooms')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="bathrooms" value="Bathrooms *" />
                    <x-text-input id="bathrooms" type="number" name="bathrooms" :value="old('bathrooms', $property->bathrooms)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('bathrooms')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="toilets" value="Toilets *" />
                    <x-text-input id="toilets" type="number" name="toilets" :value="old('toilets', $property->toilets)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('toilets')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="square_meters" value="Size (m²)" />
                    <x-text-input id="square_meters" type="number" name="square_meters" :value="old('square_meters', $property->square_meters)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('square_meters')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="year_built" value="Year built" />
                    <x-text-input id="year_built" type="number" name="year_built" :value="old('year_built', $property->year_built)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('year_built')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="video_url" value="Video URL" />
                    <x-text-input id="video_url" type="url" name="video_url" :value="old('video_url', $property->video_url)" class="mt-1 block w-full" placeholder="https://..." />
                    <x-input-error :messages="$errors->get('video_url')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="virtual_tour_url" value="Virtual tour URL" />
                    <x-text-input id="virtual_tour_url" type="url" name="virtual_tour_url" :value="old('virtual_tour_url', $property->virtual_tour_url)" class="mt-1 block w-full" placeholder="https://..." />
                    <x-input-error :messages="$errors->get('virtual_tour_url')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Location --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Location</h2>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="address" value="Address *" />
                    <x-text-input id="address" name="address" :value="old('address', $property->address)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="city" value="City *" />
                    <x-text-input id="city" name="city" :value="old('city', $property->city)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('city')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="state" value="State *" />
                    <x-text-input id="state" name="state" :value="old('state', $property->state)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('state')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="latitude" value="Latitude" />
                    <x-text-input id="latitude" type="number" step="any" name="latitude" :value="old('latitude', $property->latitude)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('latitude')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="longitude" value="Longitude" />
                    <x-text-input id="longitude" type="number" step="any" name="longitude" :value="old('longitude', $property->longitude)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('longitude')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Amenities --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Amenities</h2>

            <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($amenities as $amenity)
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:border-brand-500 dark:border-gray-800 dark:text-gray-200">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                               @checked(in_array($amenity->id, old('amenities', $property->amenities->pluck('id')->all())))
                               class="rounded border-gray-300 text-brand-700 focus:ring-brand-600">
                        {{ $amenity->name }}
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('amenities')" class="mt-2" />
        </div>

        {{-- Images --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Photos</h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload up to 20 photos (JPEG, PNG, WebP or AVIF, max 5 MB each).</p>

            @if ($property->exists && $property->images->isNotEmpty())
                <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-5">
                    @foreach ($property->images as $image)
                        <div class="relative">
                            <img src="{{ $image->image_url }}" alt="{{ $image->alt }}" class="h-24 w-full rounded-lg object-cover">
                            @if ($image->is_cover)
                                <span class="absolute left-1 top-1 rounded bg-brand-700 px-1.5 py-0.5 text-[10px] font-semibold text-white">Cover</span>
                            @endif
                            <label class="mt-1 flex items-center gap-1 text-[11px] text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                                Remove
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/avif"
                       class="block w-full text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-brand-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-800 dark:text-gray-300">
                <x-input-error :messages="$errors->get('images')" class="mt-1" />
                @foreach ($errors->get('images.*') as $messages)
                    @foreach ($messages as $message)
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- SEO --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">SEO (optional)</h2>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="meta_title" value="Meta title" />
                    <x-text-input id="meta_title" name="meta_title" :value="old('meta_title', $property->meta_title)" class="mt-1 block w-full" />
                </div>

                <div>
                    <x-input-label for="meta_description" value="Meta description" />
                    <x-text-input id="meta_description" name="meta_description" :value="old('meta_description', $property->meta_description)" class="mt-1 block w-full" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ $property->exists ? 'Save changes' : 'Publish listing' }}</x-primary-button>

            @if ($property->exists)
                <a href="{{ route('agent.properties.show', $property) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300">
                    Cancel
                </a>
            @endif
        </div>
    </form>
</x-panel.layout>
