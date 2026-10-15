<x-panel.layout :panel="'Agent'" :title="'Tour requests'">
    <div class="mb-5 flex flex-wrap gap-2 text-sm">
        @foreach (['all', 'requested', 'confirmed', 'completed', 'cancelled'] as $tab)
            <a href="{{ request()->fullUrlWithQuery($tab === 'all' ? ['status' => null] : ['status' => $tab]) }}"
               class="rounded-full px-3 py-1.5 font-medium {{ ($activeStatus ?? null) === $tab || ($tab === 'all' && ! $activeStatus)
                   ? 'bg-brand-700 text-white'
                   : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                {{ \Illuminate\Support\Str::ucfirst($tab) }}
                <span class="ml-1 text-xs opacity-70">{{ $counts[$tab] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($tours as $tour)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex gap-4">
                        <img src="{{ $tour->property?->imageUrl() }}" alt=""
                             class="h-16 w-20 rounded-lg object-cover">

                        <div class="min-w-0">
                            <a href="{{ route('properties.show', $tour->property) }}"
                               class="font-semibold text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                {{ $tour->property?->title ?? 'Deleted listing' }}
                            </a>
                            <p class="mt-0.5 text-sm text-gray-500">
                                {{ $tour->user?->name }} &middot; {{ $tour->user?->email }}
                            </p>
                            <p class="mt-1 text-sm font-medium text-brand-700">
                                📅 {{ $tour->scheduled_at?->format('l, F j, Y \a\t g:i A') }}
                            </p>
                            @if ($tour->notes)
                                <p class="mt-2 text-sm text-gray-500">“{{ $tour->notes }}”</p>
                            @endif
                        </div>
                    </div>

                    <x-badge :badge="match ($tour->status->value) {
                        'requested' => 'amber', 'confirmed' => 'green', 'completed' => 'teal', 'cancelled' => 'red', default => 'gray',
                    }" :label="\Illuminate\Support\Str::ucfirst($tour->status->value)" />
                </div>

                <form method="POST" action="{{ route('agent.tours.update', $tour) }}"
                      class="mt-4 grid gap-3 border-t border-gray-100 pt-4 sm:grid-cols-[160px_1fr_auto] dark:border-gray-800">
                    @csrf
                    @method('PATCH')

                    <select name="status"
                            class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (['requested', 'confirmed', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected($tour->status->value === $status)>
                                {{ \Illuminate\Support\Str::ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

                    <input type="text" name="agent_notes" value="{{ old('agent_notes', $tour->agent_notes) }}"
                           placeholder="Internal notes (optional)"
                           class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">

                    <x-primary-button class="justify-center">Save</x-primary-button>
                </form>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center dark:border-gray-700 dark:bg-gray-900">
                <p class="text-4xl">📅</p>
                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tour requests yet</p>
                <p class="mt-1 text-sm text-gray-500">Scheduled visits from buyers will appear here.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $tours->links() }}
    </div>
</x-panel.layout>
