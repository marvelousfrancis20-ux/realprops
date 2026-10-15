<x-panel.layout :panel="'Admin'" :title="'Contact messages'">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            @foreach (['' => 'All', 'new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'closed' => 'Closed'] as $key => $label)
                @php $active = (string) ($activeStatus ?? '') === (string) $key; @endphp
                <a href="{{ $key === '' ? route('admin.contact-messages.index') : route('admin.contact-messages.index', ['status' => $key]) }}"
                   class="rounded-full px-3 py-1.5 font-medium {{ $active
                       ? 'bg-brand-700 text-white'
                       : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs opacity-70">{{ $counts[$key === '' ? 'all' : $key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">From</th>
                    <th class="px-4 py-3">Subject</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Received</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($messages as $message)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $message->name }}</div>
                            <a href="mailto:{{ $message->email }}" class="text-xs text-brand-700 hover:underline">{{ $message->email }}</a>
                            @if ($message->phone)
                                <div class="text-xs text-gray-500">{{ $message->phone }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $message->subject ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="$message->status->value === 'new' ? 'blue' : ($message->status->value === 'replied' ? 'green' : 'gray')" :label="$message->status->label()" />
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $message->created_at->format('M j, Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('admin.contact-messages.show', $message) }}" class="text-brand-700 hover:underline">Open</a>
                                <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
                                      onsubmit="return confirm({{ json_encode('Delete this message?') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">No messages.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $messages->links() }}
    </div>
</x-panel.layout>
