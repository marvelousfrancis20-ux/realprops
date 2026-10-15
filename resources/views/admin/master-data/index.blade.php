<x-panel.layout :panel="'Admin'" :title="$config['label'].'s'">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.'.$resource.'.index') }}" class="flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search {{ strtolower($config['label']) }}s"
                   class="w-56 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            <x-secondary-button>Search</x-secondary-button>
        </form>

        <a href="{{ route('admin.'.$resource.'.create') }}"
           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            + Add {{ $config['label'] }}
        </a>
    </div>

    <div class="mb-5 flex flex-wrap gap-2 text-sm">
        @foreach (['amenities' => 'Amenities', 'types' => 'Property types', 'states' => 'States', 'cities' => 'Cities', 'categories' => 'Categories', 'tags' => 'Tags'] as $key => $label)
            <a href="{{ route('admin.'.$key.'.index') }}"
               class="rounded-full px-3 py-1.5 font-medium {{ $resource === $key
                   ? 'bg-brand-700 text-white'
                   : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    @foreach ($config['columns'] as $label)
                        <th class="px-4 py-3">{{ $label }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        @foreach ($config['columns'] as $key => $label)
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                @if ($key === 'state')
                                    {{ $row->state?->name ?? '—' }}
                                @elseif ($key === 'is_active')
                                    <x-badge :badge="$row->is_active ? 'green' : 'gray'" :label="$row->is_active ? 'Yes' : 'No'" />
                                @elseif (str_ends_with($key, '_count'))
                                    {{ $row->{$key} ?? 0 }}
                                @else
                                    {{ $row->{$key} ?? '—' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('admin.'.$resource.'.edit', $row->id) }}" class="text-brand-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.'.$resource.'.destroy', $row->id) }}"
                                      onsubmit="return confirm({{ json_encode('Delete '.$config['label'].' "'.($row->name ?? $row->id).'"?') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($config['columns']) + 1 }}" class="px-4 py-10 text-center text-sm text-gray-500">
                            Nothing here yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $rows->links() }}
    </div>
</x-panel.layout>
