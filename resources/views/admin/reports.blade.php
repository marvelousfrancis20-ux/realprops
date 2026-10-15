<x-panel.layout :panel="'Admin'" :title="'Reports'">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            'users' => ['Users', $totals['users']],
            'agents' => ['Agents', $totals['agents']],
            'properties' => ['Listings', $totals['properties']],
            'live' => ['Live listings', $totals['live']],
            'pending' => ['Pending', $totals['pending']],
            'inquiries' => ['Inquiries', $totals['inquiries']],
            'tours' => ['Tours', $totals['tours']],
            'posts' => ['Posts', $totals['posts']],
            'messages' => ['Messages', $totals['messages']],
            'plans' => ['Plans', $totals['plans']],
        ] as $key => [$label, $value])
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Listings created per month</h2>
            <div class="mt-4">
                <x-chart-canvas id="reportListingsChart"
                                type="bar"
                                :label="'Listings'"
                                :labels="array_column($stats['charts']['listings_per_month'], 'label')"
                                :values="array_column($stats['charts']['listings_per_month'], 'value')"
                                :height="280" />
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Inquiries per month</h2>
            <div class="mt-4">
                <x-chart-canvas id="reportInquiriesChart"
                                type="line"
                                :label="'Inquiries'"
                                :labels="array_column($stats['charts']['inquiries_per_month'], 'label')"
                                :values="array_column($stats['charts']['inquiries_per_month'], 'value')"
                                :color="'#F59E0B'"
                                :height="280" />
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Revenue (last 6 months)</h2>
            <div class="mt-4">
                <x-chart-canvas id="reportRevenueChart"
                                type="line"
                                :label="'Revenue'"
                                :labels="array_column($stats['charts']['revenue_per_month'], 'label')"
                                :values="array_column($stats['charts']['revenue_per_month'], 'value')"
                                :color="'#0F766E'"
                                :currency="true"
                                :height="280" />
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Listings by property type</h2>
            <div class="mt-4">
                <x-chart-canvas id="reportTypeChart"
                                type="doughnut"
                                :label="'Listings'"
                                :labels="array_column($stats['charts']['listings_by_type'], 'label')"
                                :values="array_column($stats['charts']['listings_by_type'], 'value')"
                                :color="'#0F766E'"
                                :height="280" />
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Top agents by listings</h2>
                <a href="{{ route('admin.agents.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">All agents</a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <tr>
                            <th class="py-3 pe-4">Agent</th>
                            <th class="px-4 py-3">Listings</th>
                            <th class="px-4 py-3">Views</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($topAgents as $row)
                            <tr>
                                <td class="py-3 pe-4 font-medium text-gray-900 dark:text-gray-100">{{ $row->agent?->name ?? 'Removed agent' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $row->listings }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ number_format($row->views) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-8 text-center text-gray-500">No listings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Highest earning agents</h2>
            </div>

            <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($stats['top_agents'] as $agent)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <a href="{{ route('admin.users.show', $agent) }}" class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $agent->name }}</p>
                            <p class="text-xs text-gray-500">{{ $agent->properties_count }} listings &middot; {{ $agent->received_inquiries_count }} inquiries</p>
                        </a>
                        <x-badge badge="teal" :label="$agent->properties_count.' listings'" />
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-gray-500">No agents yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-panel.layout>
