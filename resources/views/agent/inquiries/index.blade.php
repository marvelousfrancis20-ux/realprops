<x-panel.layout :panel="'Agent'" :title="'Inquiries'">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            @foreach (['all', 'new', 'read', 'replied', 'archived'] as $tab)
                <a href="{{ request()->fullUrlWithQuery($tab === 'all' ? ['status' => null] : ['status' => $tab]) }}"
                   class="rounded-full px-3 py-1.5 font-medium {{ ($activeStatus ?? null) === $tab || ($tab === 'all' && ! $activeStatus)
                       ? 'bg-brand-700 text-white'
                       : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                    {{ \Illuminate\Support\Str::ucfirst($tab) }}
                    <span class="ml-1 text-xs opacity-70">{{ $counts[$tab] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Property</th>
                    <th class="px-4 py-3">Received</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($inquiries as $inquiry)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3">
                            <a href="{{ route('agent.inquiries.show', $inquiry) }}" class="font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                {{ $inquiry->name }}
                            </a>
                            <p class="text-xs text-gray-500">{{ $inquiry->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            <a href="{{ route('properties.show', $inquiry->property) }}" class="hover:text-brand-700">
                                {{ \Illuminate\Support\Str::limit($inquiry->property?->title ?? 'Deleted listing', 35) }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $inquiry->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="match ($inquiry->status->value) {
                                'new' => 'amber', 'read' => 'blue', 'replied' => 'green', default => 'gray',
                            }" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('agent.inquiries.show', $inquiry) }}" class="font-semibold text-brand-700 hover:underline">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">
                            No inquiries in this view yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $inquiries->links() }}
    </div>
</x-panel.layout>
