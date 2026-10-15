<x-panel.layout :panel="'Property details'" :menu="[
    ['label' => 'Dashboard', 'href' => route('agent.dashboard'), 'active' => 'agent.dashboard', 'icon' => '🏠'],
    ['label' => 'My properties', 'href' => route('agent.properties.index'), 'active' => 'agent.properties.*', 'icon' => '🏢'],
    ['label' => 'Add property', 'href' => route('agent.properties.create'), 'active' => 'agent.properties.create', 'icon' => '➕'],
    ['label' => 'Inquiries', 'href' => route('agent.inquiries.index'), 'active' => 'agent.inquiries.*', 'icon' => '✉️'],
    ['label' => 'Tour requests', 'href' => route('agent.tours.index'), 'active' => 'agent.tours.*', 'icon' => '📅'],
    ['label' => 'Analytics', 'href' => route('agent.analytics'), 'active' => 'agent.analytics', 'icon' => '📊'],
    ['label' => 'Subscription', 'href' => route('agent.subscription.index'), 'active' => 'agent.subscription.*', 'icon' => '💳'],
    ['label' => 'Profile', 'href' => route('agent.profile.edit'), 'active' => 'agent.profile.*', 'icon' => '👤'],
]">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-badge :badge="$property->is_approved ? 'green' : 'amber'" :label="$property->is_approved ? 'Live' : 'Pending approval'" />
                            <x-badge :badge="$property->is_featured ? 'teal' : 'gray'" :label="$property->is_featured ? 'Featured' : 'Standard'" />
                            <x-badge :badge="match ($property->status->value) {
                                'available' => 'green', 'pending' => 'amber', 'sold' => 'red', 'rented' => 'blue', default => 'gray',
                            }" :label="$property->status->label()" />
                        </div>
                        <h2 class="mt-3 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $property->title }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $property->locationLine() }}</p>
                        <p class="mt-3 text-2xl font-bold text-brand-700">{{ $property->formattedPrice() }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('agent.properties.edit', $property) }}"
                           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                            Edit listing
                        </a>
                        @if ($property->is_approved)
                            <a href="{{ route('properties.show', $property) }}" target="_blank" rel="noopener"
                               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                View public page ↗
                            </a>
                        @endif
                    </div>
                </div>

                @unless ($property->is_approved)
                    <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-900/20 dark:text-amber-300">
                        This listing is still under review. It will appear in search results once an admin approves it.
                    </div>
                @endunless

                <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 pt-5 text-sm dark:border-gray-800 sm:grid-cols-4">
                    <div><dt class="text-gray-500">Views</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ number_format($viewCount) }}</dd></div>
                    <div><dt class="text-gray-500">Inquiries</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ number_format($inquiryCount) }}</dd></div>
                    <div><dt class="text-gray-500">Bedrooms</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $property->bedrooms ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Bathrooms</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $property->bathrooms ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Toilets</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $property->toilets ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Size</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ number_format($property->square_meters ?? 0) }} m²</dd></div>
                    <div><dt class="text-gray-500">Type</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $property->type?->name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Listing</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $property->listing_type->label() }}</dd></div>
                </dl>

                <div class="mt-5 prose-sm max-w-none text-sm text-gray-600 dark:text-gray-300">
                    {!! nl2br(e(\Illuminate\Support\Str::limit(strip_tags($property->description), 900))) !!}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Images ({{ $property->images->count() }})</h3>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @forelse ($property->images as $image)
                        <figure class="relative">
                            <img src="{{ $image->image_url }}" alt="{{ $image->alt ?: $property->title }}" class="h-28 w-full rounded-lg object-cover">
                            @if ($image->is_cover)
                                <figcaption class="absolute start-1 top-1 rounded bg-brand-700 px-1.5 py-0.5 text-[10px] font-semibold text-white">Cover</figcaption>
                            @endif
                        </figure>
                    @empty
                        <p class="col-span-full py-6 text-center text-sm text-gray-500">No images uploaded yet.</p>
                    @endforelse
                </div>

                @if ($property->amenities->isNotEmpty())
                    <h3 class="mt-6 text-sm font-semibold text-gray-900 dark:text-gray-100">Amenities</h3>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($property->amenities as $amenity)
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $amenity->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent inquiries</h3>
                    <a href="{{ route('agent.inquiries.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">View all</a>
                </div>

                <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($property->inquiries as $inquiry)
                        <div class="py-3">
                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ route('agent.inquiries.show', $inquiry) }}" class="text-sm font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                    {{ $inquiry->name }} &lt;{{ $inquiry->email }}&gt;
                                </a>
                                <x-badge :badge="match ($inquiry->status->value) {
                                    'new' => 'amber', 'read' => 'blue', 'replied' => 'green', default => 'gray',
                                }" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                            </div>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $inquiry->message }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $inquiry->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">No inquiries for this listing yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 text-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Meta</h3>
                <dl class="mt-3 space-y-2 text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Slug</dt><dd class="truncate">{{ $property->slug }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Published</dt><dd>{{ $property->published_at?->format('M j, Y') ?? 'Not published' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Created</dt><dd>{{ $property->created_at->format('M j, Y') }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Updated</dt><dd>{{ $property->updated_at->diffForHumans() }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Danger zone</h3>
                <p class="mt-2 text-xs text-gray-500">Deleting a listing permanently removes it, including its images and inquiries.</p>

                <form method="POST" action="{{ route('agent.properties.destroy', $property) }}" class="mt-4"
                      onsubmit="return confirm({{ json_encode('Delete "'.$property->title.'" permanently?') }})">
                    @csrf
                    @method('DELETE')
                    <x-danger-button class="w-full justify-center">Delete listing</x-danger-button>
                </form>
            </div>
        </aside>
    </div>
</x-panel.layout>
