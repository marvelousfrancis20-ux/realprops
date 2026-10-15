<x-panel.layout :panel="'Admin'" :title="'Activity log'">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('admin.activity-log') }}"
               class="rounded-full px-3 py-1.5 font-medium {{ ! $activeEvent
                   ? 'bg-brand-700 text-white'
                   : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                All
            </a>
            @foreach ($events as $event)
                <a href="{{ route('admin.activity-log', ['event' => $event]) }}"
                   class="rounded-full px-3 py-1.5 font-medium {{ $activeEvent === $event
                       ? 'bg-brand-700 text-white'
                       : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                    {{ $event }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.activity-log') }}" class="flex gap-2">
            @if ($activeEvent)
                <input type="hidden" name="event" value="{{ $activeEvent }}">
            @endif
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search description or subject"
                   class="w-64 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            <x-secondary-button>Search</x-secondary-button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">When</th>
                    <th class="px-4 py-3">Event</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Subject</th>
                    <th class="px-4 py-3">By</th>
                    <th class="px-4 py-3">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($activities as $activity)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3 text-gray-500">{{ $activity->created_at->format('M j, Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="$activity->event === 'created' ? 'green' : ($activity->event === 'deleted' ? 'red' : 'amber')" :label="$activity->event" />
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $activity->description }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            @if ($activity->subject_type)
                                {{ class_basename($activity->subject_type) }}#{{ $activity->subject_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $activity->causer?->name ?? 'System' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $activity->ip ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">No activity recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $activities->links() }}
    </div>
</x-panel.layout>
