<x-panel.layout :panel="'Admin'" :title="'Property details'">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-badge :badge="$property->is_approved ? 'green' : 'amber'" :label="$property->is_approved ? 'Approved' : 'Pending approval'" />
                            <x-badge :badge="$property->is_featured ? 'teal' : 'gray'" :label="$property->is_featured ? 'Featured' : 'Standard'" />
                            <x-badge :badge="match ($property->status->value) {
                                'available' => 'green', 'pending' => 'amber', 'sold' => 'red', 'rented' => 'blue', default => 'gray',
                            }" :label="$property->status->label()" />
                        </div>
                        <h2 class="mt-3 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $property->title }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $property->locationLine() }}</p>
                        <p class="mt-3 text-2xl font-bold text-brand-700">{{ $property->formattedPrice() }}</p>
                    </div>

                    <a href="{{ route('properties.show', $property) }}" target="_blank" rel="noopener"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        View public page ↗
                    </a>
                </div>

                <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 pt-5 text-sm dark:border-gray-800 sm:grid-cols-4">
                    <div><dt class="text-gray-500">Bedrooms</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $property->bedrooms ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Bathrooms</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $property->bathrooms ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Size</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ number_format($property->square_meters ?? 0) }} m²</dd></div>
                    <div><dt class="text-gray-500">Views</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ number_format($property->views_count) }}</dd></div>
                </dl>

                <div class="mt-5 prose-sm max-w-none text-sm text-gray-600 dark:text-gray-300">
                    {!! nl2br(e(\Illuminate\Support\Str::limit(strip_tags($property->description), 900))) !!}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Images ({{ $property->images->count() }})</h3>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($property->images as $image)
                        <figure class="relative">
                            <img src="{{ $image->image_url }}" alt="{{ $image->alt ?: $property->title }}" class="h-28 w-full rounded-lg object-cover">
                            @if ($image->is_cover)
                                <figcaption class="absolute start-1 top-1 rounded bg-brand-700 px-1.5 py-0.5 text-[10px] font-semibold text-white">Cover</figcaption>
                            @endif
                        </figure>
                    @endforeach
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
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Inquiries ({{ $property->inquiries->count() }})</h3>
                </div>
                <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($property->inquiries as $inquiry)
                        <div class="py-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $inquiry->name }} &lt;{{ $inquiry->email }}&gt;</p>
                                <x-badge :badge="match ($inquiry->status->value) {
                                    'new' => 'amber', 'read' => 'blue', 'replied' => 'green', default => 'gray',
                                }" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                            </div>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $inquiry->message }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $inquiry->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">No inquiries for this listing.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Moderation</h3>

                @unless ($property->is_approved)
                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="mt-3">
                        @csrf
                        <x-primary-button class="w-full justify-center">Approve listing</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="mt-2"
                          onsubmit="event.preventDefault(); var reason = prompt('Why is this listing being rejected?'); if (reason === null) return; this.reason.value = reason; this.submit();">
                        @csrf
                        <input type="hidden" name="reason">
                        <button type="submit"
                                class="w-full rounded-md border border-rose-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-rose-600 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 dark:hover:bg-rose-900/30">
                            Reject with reason
                        </button>
                    </form>
                @else
                    <p class="mt-2 text-sm text-gray-500">This listing is live on the site.</p>
                @endunless

                <form method="POST" action="{{ route('admin.properties.feature', $property) }}" class="mt-3">
                    @csrf
                    <x-secondary-button class="w-full justify-center">
                        {{ $property->is_featured ? 'Remove featured badge' : 'Mark as featured' }}
                    </x-secondary-button>
                </form>

                <form method="POST" action="{{ route('admin.properties.destroy', $property) }}" class="mt-3"
                      onsubmit="return confirm('Delete this listing permanently?')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button class="w-full justify-center">Delete listing</x-danger-button>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Agent</h3>
                <a href="{{ route('admin.agents.show', $property->agent->agentProfile) }}" class="mt-3 flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                        {{ $property->agent?->initials() }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $property->agent?->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ $property->agent?->email }}</p>
                    </div>
                </a>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 text-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Meta</h3>
                <dl class="mt-3 space-y-2 text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Slug</dt><dd class="truncate">{{ $property->slug }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Type</dt><dd>{{ $property->type?->name }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Listing</dt><dd>{{ $property->listing_type->label() }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Published</dt><dd>{{ $property->published_at?->format('M j, Y') ?? 'Not published' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Created</dt><dd>{{ $property->created_at->format('M j, Y') }}</dd></div>
                </dl>
            </div>
        </aside>
    </div>
</x-panel.layout>
