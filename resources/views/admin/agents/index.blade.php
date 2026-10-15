<x-panel.layout :panel="'Admin'" :title="'Agents'">
    <div class="mb-5 flex flex-wrap gap-2 text-sm">
        @foreach (['all' => 'All agents', 'verified' => 'Verified', 'pending' => 'Pending'] as $key => $label)
            <a href="{{ $key === 'all' ? route('admin.agents.index') : route('admin.agents.index', ['status' => $key]) }}"
               class="rounded-full px-3 py-1.5 font-medium {{ ($activeStatus ?? null) === $key || ($key === 'all' && ! $activeStatus)
                   ? 'bg-brand-700 text-white'
                   : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                {{ $label }}
                <span class="ml-1 text-xs opacity-70">{{ $counts[$key] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Agent</th>
                    <th class="px-4 py-3">Agency</th>
                    <th class="px-4 py-3">License</th>
                    <th class="px-4 py-3">Listings</th>
                    <th class="px-4 py-3">Verified</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($agents as $profile)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.agents.show', $profile) }}" class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                                    {{ $profile->user?->initials() }}
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate font-medium text-gray-900 dark:text-gray-100">{{ $profile->user?->name }}</span>
                                    <span class="block truncate text-xs text-gray-500">{{ $profile->user?->email }}</span>
                                </span>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $profile->agency_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $profile->license_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $profile->properties_count }}</td>
                        <td class="px-4 py-3">
                            @if ($profile->isVerified())
                                <x-badge badge="green" label="Verified" />
                            @else
                                <x-badge badge="amber" label="Pending" />
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('admin.agents.show', $profile) }}" class="text-brand-700 hover:underline">Review</a>

                                @unless ($profile->isVerified())
                                    <form method="POST" action="{{ route('admin.agents.verify', $profile) }}">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:underline">Verify</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.agents.unverify', $profile) }}">
                                        @csrf
                                        <button type="submit" class="text-amber-600 hover:underline">Unverify</button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">No agents found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $agents->links() }}
    </div>
</x-panel.layout>
