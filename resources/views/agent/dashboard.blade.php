<x-panel.layout :panel="'Agent'" :menu="[
    ['label' => 'Dashboard', 'href' => route('agent.dashboard'), 'active' => 'agent.dashboard', 'icon' => 'ðŸ '],
    ['label' => 'My properties', 'href' => route('agent.properties.index'), 'active' => 'agent.properties.*', 'icon' => 'ðŸ¢'],
    ['label' => 'Add property', 'href' => route('agent.properties.create'), 'active' => 'agent.properties.create', 'icon' => 'âž•'],
    ['label' => 'Inquiries', 'href' => route('agent.inquiries.index'), 'active' => 'agent.inquiries.*', 'icon' => 'âœ‰ï¸'],
    ['label' => 'Tour requests', 'href' => route('agent.tours.index'), 'active' => 'agent.tours.*', 'icon' => 'ðŸ“…'],
    ['label' => 'Analytics', 'href' => route('agent.analytics'), 'active' => 'agent.analytics', 'icon' => 'ðŸ“Š'],
    ['label' => 'Subscription', 'href' => route('agent.subscription.index'), 'active' => 'agent.subscription.*', 'icon' => 'ðŸ’³'],
    ['label' => 'Profile', 'href' => route('agent.profile.edit'), 'active' => 'agent.profile.*', 'icon' => 'ðŸ‘¤'],
]">
    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total listings</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totals']['listings'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total views</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['totals']['views'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inquiries</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totals']['inquiries'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Conversion rate</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totals']['conversion_rate'] ?? 0 }}%</p>
        </div>
    </div>

    {{-- Plan usage --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Current plan: {{ $currentPlan?->name ?? 'Free' }}
                </p>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ $listingLimit === 0 ? 'Unlimited listings' : "You have used {$listingCount} of {$listingLimit} listings" }}
                </p>
            </div>

            <a href="{{ route('agent.subscription.index') }}"
               class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                {{ $currentPlan ? 'Manage plan' : 'Upgrade' }}
            </a>
        </div>

        @if ($listingLimit > 0)
            <div class="mt-3 h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                <div class="h-2 rounded-full bg-brand-700" style="width: {{ min(100, ($listingCount / max(1, $listingLimit)) * 100) }}%"></div>
            </div>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Recent listings --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent listings</h2>
                <a href="{{ route('agent.properties.index') }}" class="text-xs font-semibold text-brand-700 hover:underline dark:text-brand-400">View all</a>
            </div>

            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($listings as $property)
                    <a href="{{ route('agent.properties.show', $property) }}" class="flex items-center gap-3 py-3">
                        <img src="{{ $property->imageUrl() }}" alt="" class="h-12 w-12 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $property->title }}</p>
                            <p class="text-xs text-gray-500">{{ $property->formattedPrice() }}</p>
                        </div>
                        <x-badge :badge="$property->is_approved ? 'green' : 'amber'" :label="$property->is_approved ? 'Live' : 'Pending'" />
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-gray-500">No listings yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent inquiries --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent inquiries</h2>
                <a href="{{ route('agent.inquiries.index') }}" class="text-xs font-semibold text-brand-700 hover:underline dark:text-brand-400">View all</a>
            </div>

            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentInquiries as $inquiry)
                    <a href="{{ route('agent.inquiries.show', $inquiry) }}" class="block py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $inquiry->name }}</p>
                            <x-badge :badge="match ($inquiry->status->value) {
                                'new' => 'amber', 'read' => 'blue', 'replied' => 'green', default => 'gray',
                            }" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                        </div>
                        <p class="mt-0.5 truncate text-xs text-gray-500">
                            {{ $inquiry->property?->title }} Â· {{ $inquiry->created_at->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-gray-500">No inquiries yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent tours --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Upcoming tours</h2>
            <a href="{{ route('agent.tours.index') }}" class="text-xs font-semibold text-brand-700 hover:underline dark:text-brand-400">View all</a>
        </div>

        <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($recentTours as $tour)
                <div class="flex items-center justify-between gap-3 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $tour->property?->title }}</p>
                        <p class="text-xs text-gray-500">{{ $tour->user?->name }} Â· {{ $tour->scheduled_at?->format('M j, g:i A') }}</p>
                    </div>
                    <x-badge :badge="match ($tour->status->value) {
                        'requested' => 'amber', 'confirmed' => 'green', 'completed' => 'teal', 'cancelled' => 'red', default => 'gray',
                    }" :label="\Illuminate\Support\Str::ucfirst($tour->status->value)" />
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500">No tour requests.</p>
            @endforelse
        </div>
    </div>
</x-panel.layout>
