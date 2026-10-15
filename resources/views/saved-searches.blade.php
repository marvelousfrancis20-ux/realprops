<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Saved searches</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            We will e-mail you when new listings match these filters.
        </p>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($savedSearches as $search)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $search->name }}</p>

                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach (array_filter((array) $search->filters) as $key => $value)
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        {{ \Illuminate\Support\Str::headline($key) }}: {{ \Illuminate\Support\Str::limit((string) (is_array($value) ? implode(', ', $value) : $value), 30) }}
                                    </span>
                                @endforeach
                            </div>

                            <p class="mt-2 text-xs text-gray-400">
                                {{ $search->matches_count }} matches so far ·
                                Last alert {{ $search->last_notified_at?->diffForHumans() ?? 'never' }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('properties.index', (array) $search->filters) }}"
                               class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-600 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                                Run
                            </a>

                            <form method="POST" action="{{ route('saved-searches.toggle', $search) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $search->notify ? 'bg-brand-700 text-white' : 'border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-gray-200' }}">
                                    {{ $search->notify ? 'Alerts on' : 'Alerts off' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('saved-searches.destroy', $search) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-rose-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-gray-300 p-12 text-center text-sm text-gray-500 dark:border-gray-700">
                    No saved searches yet. Run a search and save it to get alerts.
                </p>
            @endforelse
        </div>
    </div>
</x-app-layout>
