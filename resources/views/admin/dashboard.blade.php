<x-panel.layout :panel="'Admin'" :title="'Dashboard'">
    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total users</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['totals']['users_all']) }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['totals']['users'] }} new this year</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Agents</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totals']['agents'] }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['totals']['admins'] }} admins</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Listings</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['totals']['listings']) }}</p>
        </div>
        <a href="{{ route('admin.properties.index', ['approval' => 'pending']) }}"
           class="rounded-2xl border border-amber-200 bg-amber-50 p-5 transition hover:border-amber-300 dark:border-amber-900/50 dark:bg-amber-900/20">
            <p class="text-sm text-amber-700 dark:text-amber-300">Pending approvals</p>
            <p class="mt-1 text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $stats['totals']['pending_approvals'] }}</p>
            <p class="mt-1 text-xs text-amber-600">Review queue &rarr;</p>
        </a>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inquiries (month)</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totals']['inquiries_this_month'] }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['totals']['tours_this_month'] }} tours booked</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Revenue</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($stats['totals']['revenue'], 2) }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['totals']['messages'] }} contact messages</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Listings per month</h2>
            <div class="mt-4">
                <x-chart-canvas id="adminListingsChart"
                                type="bar"
                                :label="'Listings'"
                                :labels="array_column($stats['charts']['listings_per_month'], 'label')"
                                :values="array_column($stats['charts']['listings_per_month'], 'value')"
                                :height="260" />
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Inquiries per month</h2>
            <div class="mt-4">
                <x-chart-canvas id="adminInquiriesChart"
                                type="line"
                                :label="'Inquiries'"
                                :labels="array_column($stats['charts']['inquiries_per_month'], 'label')"
                                :values="array_column($stats['charts']['inquiries_per_month'], 'value')"
                                :color="'#F59E0B'"
                                :height="260" />
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Pending approvals --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Awaiting approval</h2>
                <a href="{{ route('admin.properties.index', ['approval' => 'pending']) }}" class="text-xs font-semibold text-brand-700 hover:underline">View all</a>
            </div>

            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($pendingProperties as $property)
                    <div class="flex items-center gap-3 py-3">
                        <img src="{{ $property->imageUrl() }}" alt="" class="h-12 w-16 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.properties.show', $property) }}" class="block truncate text-sm font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                {{ $property->title }}
                            </a>
                            <p class="text-xs text-gray-500">{{ $property->agent?->name }} &middot; {{ $property->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-500">Nothing waiting for approval ðŸŽ‰</p>
                @endforelse
            </div>
        </div>

        {{-- Latest inquiries --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Latest inquiries</h2>

            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentInquiries as $inquiry)
                    <div class="py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $inquiry->name }}</p>
                            <x-badge :badge="match ($inquiry->status->value) {
                                'new' => 'amber', 'read' => 'blue', 'replied' => 'green', default => 'gray',
                            }" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                        </div>
                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $inquiry->property?->title }} &middot; {{ $inquiry->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-500">No inquiries yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Agents to verify --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Agents to verify</h2>
                <a href="{{ route('admin.agents.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-brand-700 hover:underline">View all</a>
            </div>

            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($verifiedAgents as $user)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.agents.show', $user->agentProfile) }}" class="block truncate text-sm font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                {{ $user->name }}
                            </a>
                            <p class="text-xs text-gray-500">{{ $user->agentProfile?->agency_name ?? 'No agency name' }}</p>
                        </div>
                        <x-badge badge="amber" label="Pending" />
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-500">All agents are verified.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Secondary rows --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Upcoming tours</h2>
            </div>
            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentTours as $tour)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $tour->property?->title }}</p>
                            <p class="text-xs text-gray-500">{{ $tour->user?->name }} &middot; {{ $tour->scheduled_at?->format('M j, g:i A') }}</p>
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

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Contact messages</h2>
                <a href="{{ route('admin.contact-messages.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">View all</a>
            </div>
            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentMessages as $message)
                    <a href="{{ route('admin.contact-messages.show', $message) }}" class="block py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $message->name }}</p>
                            <x-badge :badge="$message->status->value === 'new' ? 'amber' : 'gray'" :label="\Illuminate\Support\Str::ucfirst($message->status->value)" />
                        </div>
                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $message->subject }} &middot; {{ $message->created_at->diffForHumans() }}</p>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-gray-500">No messages.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-panel.layout>
