<x-panel.layout :panel="'Admin'" :title="'Subscription plans'">
    <div class="mb-5 flex items-center justify-between gap-3">
        <p class="text-sm text-gray-500">Plans control listing limits, featured listings and Stripe billing.</p>
        <a href="{{ route('admin.plans.create') }}" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">+ New plan</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Plan</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Listings</th>
                    <th class="px-4 py-3">Featured</th>
                    <th class="px-4 py-3">Stripe price</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($plans as $plan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $plan->name }}</div>
                            <div class="text-xs text-gray-500">{{ $plan->slug ?: '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            {{ $plan->isFree() ? 'Free' : $plan->priceLabel() }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            {{ $plan->isUnlimited() ? 'Unlimited' : $plan->listing_limit }}
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :badge="$plan->allows_featured ? 'green' : 'gray'" :label="$plan->allows_featured ? 'Yes' : 'No'" />
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $plan->stripe_price_id ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="$plan->is_active ? 'green' : 'gray'" :label="$plan->is_active ? 'Active' : 'Hidden'" />
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="text-brand-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                                      onsubmit="return confirm({{ json_encode('Delete plan "'.$plan->name.'"?') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">No plans yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-panel.layout>
