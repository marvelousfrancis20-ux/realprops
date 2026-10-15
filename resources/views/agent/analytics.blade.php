<x-panel.layout :panel="'Agent'" :title="'Analytics'">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Listings</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totals']['listings'] }}</p>
            <p class="mt-1 text-xs text-emerald-600">{{ $stats['totals']['live'] }} live &middot; {{ $stats['totals']['pending'] }} pending</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total views</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['totals']['views']) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inquiries</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['totals']['inquiries']) }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['totals']['tours'] }} tour requests</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Conversion rate</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totals']['conversion_rate'] }}%</p>
            <p class="mt-1 text-xs text-gray-500">views &rarr; inquiries</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Views per listing</h2>
            <div class="mt-4">
                <x-chart-canvas id="agentViewsChart"
                                type="bar"
                                :label="'Views'"
                                :labels="array_column($stats['charts']['views_by_listing'], 'label')"
                                :values="array_column($stats['charts']['views_by_listing'], 'value')"
                                :horizontal="true"
                                :height="320" />
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Inquiries &amp; tours (last 6 months)</h2>
            <div class="mt-4">
                <x-chart-canvas id="agentInquiriesChart"
                                type="line"
                                :label="'Inquiries'"
                                :labels="array_column($stats['charts']['inquiries_by_month'], 'label')"
                                :values="array_column($stats['charts']['inquiries_by_month'], 'value')"
                                :color="'#F59E0B'"
                                :height="320" />
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Top performing listings</h2>
            <a href="{{ route('agent.properties.index') }}" class="text-xs font-semibold text-brand-700 hover:underline dark:text-brand-400">Manage listings</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="py-3 pe-4">Listing</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">Views</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($stats['top_listings'] as $property)
                        <tr>
                            <td class="py-3 pe-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $property->imageUrl() }}" alt="" class="h-10 w-14 rounded object-cover">
                                    <a href="{{ route('agent.properties.show', $property) }}" class="font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                        {{ \Illuminate\Support\Str::limit($property->title, 40) }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $property->formattedPrice() }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ number_format($property->views_count) }}</td>
                            <td class="px-4 py-3">
                                <x-badge :badge="$property->is_approved ? 'green' : 'amber'" :label="$property->is_approved ? 'Live' : 'Pending'" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-gray-500">No listings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-panel.layout>
