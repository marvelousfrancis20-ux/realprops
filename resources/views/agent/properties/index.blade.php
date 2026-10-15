<x-panel.layout :panel="'My properties'" :menu="[
    ['label' => 'Dashboard', 'href' => route('agent.dashboard'), 'active' => 'agent.dashboard', 'icon' => '🏠'],
    ['label' => 'My properties', 'href' => route('agent.properties.index'), 'active' => 'agent.properties.*', 'icon' => '🏢'],
    ['label' => 'Add property', 'href' => route('agent.properties.create'), 'active' => 'agent.properties.create', 'icon' => '➕'],
    ['label' => 'Inquiries', 'href' => route('agent.inquiries.index'), 'active' => 'agent.inquiries.*', 'icon' => '✉️'],
    ['label' => 'Tour requests', 'href' => route('agent.tours.index'), 'active' => 'agent.tours.*', 'icon' => '📅'],
    ['label' => 'Analytics', 'href' => route('agent.analytics'), 'active' => 'agent.analytics', 'icon' => '📊'],
    ['label' => 'Subscription', 'href' => route('agent.subscription.index'), 'active' => 'agent.subscription.*', 'icon' => '💳'],
    ['label' => 'Profile', 'href' => route('agent.profile.edit'), 'active' => 'agent.profile.*', 'icon' => '👤'],
]">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-4 text-sm">
            <span class="text-gray-500 dark:text-gray-400">Total: <strong class="text-gray-900 dark:text-gray-100">{{ $statusCounts['total'] }}</strong></span>
            <span class="text-gray-500 dark:text-gray-400">Live: <strong class="text-emerald-600">{{ $statusCounts['approved'] }}</strong></span>
            <span class="text-gray-500 dark:text-gray-400">Pending: <strong class="text-amber-600">{{ $statusCounts['pending'] }}</strong></span>
        </div>

        <a href="{{ route('agent.properties.create') }}"
           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            + Add property
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Property</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Views</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($properties as $property)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $property->imageUrl() }}" alt="" class="h-11 w-11 rounded-lg object-cover">
                                <div class="min-w-0">
                                    <a href="{{ route('agent.properties.show', $property) }}" class="block truncate font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                        {{ $property->title }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $property->city }}, {{ $property->state }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $property->formattedPrice() }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="$property->is_approved ? 'green' : 'amber'" :label="$property->is_approved ? 'Live' : 'Pending'" />
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ number_format($property->views_count) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 text-xs font-semibold">
                                <a href="{{ route('agent.properties.show', $property) }}" class="text-brand-700 hover:underline dark:text-brand-400">View</a>
                                <a href="{{ route('agent.properties.edit', $property) }}" class="text-sky-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('agent.properties.destroy', $property) }}" onsubmit="return confirm('Delete this listing?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                            No properties yet. <a href="{{ route('agent.properties.create') }}" class="font-semibold text-brand-700 hover:underline">Add your first listing</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $properties->links() }}
    </div>
</x-panel.layout>
