<x-panel.layout :panel="'Admin'" :title="'Properties'">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('admin.properties.index') }}"
               class="rounded-full px-3 py-1.5 font-medium {{ ! $activeStatus ? 'bg-brand-700 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                All <span class="ml-1 text-xs opacity-70">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('admin.properties.index', ['approval' => 'pending']) }}"
               class="rounded-full px-3 py-1.5 font-medium {{ $activeStatus === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                Pending <span class="ml-1 text-xs opacity-70">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('admin.properties.index', ['approval' => 'approved']) }}"
               class="rounded-full px-3 py-1.5 font-medium {{ $activeStatus === 'approved' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                Approved <span class="ml-1 text-xs opacity-70">{{ $counts['approved'] }}</span>
            </a>
            <span class="rounded-full bg-accent-50 px-3 py-1.5 font-medium text-accent-700 ring-1 ring-accent-200 dark:bg-accent-900/30 dark:text-accent-300 dark:ring-accent-900">
                Featured <span class="ml-1 text-xs opacity-70">{{ $counts['featured'] }}</span>
            </span>
        </div>

        <form method="GET" action="{{ route('admin.properties.index') }}" class="flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search title, city or slug"
                   class="w-56 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            <x-secondary-button>Search</x-secondary-button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Property</th>
                    <th class="px-4 py-3">Agent</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Approval</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Views</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($properties as $property)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $property->imageUrl() }}" alt="" class="h-12 w-16 rounded-lg object-cover">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.properties.show', $property) }}"
                                       class="block max-w-[16rem] truncate font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                        {{ $property->title }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $property->city }} &middot; {{ $property->type?->name }}</p>
                                </div>
                                @if ($property->is_featured)
                                    <x-badge badge="teal" label="Featured" />
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $property->agent?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $property->formattedPrice() }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="$property->is_approved ? 'green' : 'amber'" :label="$property->is_approved ? 'Approved' : 'Pending'" />
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :badge="match ($property->status->value) {
                                'available' => 'green', 'pending' => 'amber', 'sold' => 'red', 'rented' => 'blue', default => 'gray',
                            }" :label="$property->status->label()" />
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ number_format($property->views_count) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('admin.properties.show', $property) }}" class="text-brand-700 hover:underline">View</a>

                                @unless ($property->is_approved)
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:underline">Approve</button>
                                    </form>
                                @endunless

                                <form method="POST" action="{{ route('admin.properties.feature', $property) }}">
                                    @csrf
                                    <button type="submit" class="text-accent-600 hover:underline">
                                        {{ $property->is_featured ? 'Unfeature' : 'Feature' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.properties.destroy', $property) }}"
                                      onsubmit="return confirm('Delete this listing permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">No properties match this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $properties->links() }}
    </div>
</x-panel.layout>
