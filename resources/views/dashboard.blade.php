<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Welcome back, {{ explode(' ', auth()->user()->name)[0] }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Your saved homes, inquiries and tour requests in one place.
        </p>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Quick actions --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('properties.index') }}" class="rounded-2xl border border-gray-200 bg-white p-5 hover:border-brand-600 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Browse properties</p>
                    <p class="mt-1 text-xs text-gray-500">Search the full marketplace</p>
                </a>
                <a href="{{ route('favorites.index') }}" class="rounded-2xl border border-gray-200 bg-white p-5 hover:border-brand-600 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Saved homes</p>
                    <p class="mt-1 text-xs text-gray-500">{{ auth()->user()->favorites()->count() }} saved</p>
                </a>
                <a href="{{ route('inquiries.index') }}" class="rounded-2xl border border-gray-200 bg-white p-5 hover:border-brand-600 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">My inquiries</p>
                    <p class="mt-1 text-xs text-gray-500">{{ auth()->user()->inquiries()->count() }} sent</p>
                </a>
                <a href="{{ route('saved-searches.index') }}" class="rounded-2xl border border-gray-200 bg-white p-5 hover:border-brand-600 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Saved searches</p>
                    <p class="mt-1 text-xs text-gray-500">Get e-mail alerts</p>
                </a>
            </div>

            {{-- Favorites --}}
            <section>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Saved homes</h3>
                    <a href="{{ route('favorites.index') }}" class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400">View all</a>
                </div>

                <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($favorites as $favorite)
                        @if ($favorite->property)
                            <x-property-card :property="$favorite->property" />
                        @endif
                    @empty
                        <p class="col-span-full rounded-xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 dark:border-gray-700">
                            You have not saved any homes yet. Tap the heart on any listing to save it.
                        </p>
                    @endforelse
                </div>
            </section>

            {{-- Recently viewed --}}
            @if ($recentlyViewed->isNotEmpty())
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recently viewed</h3>

                    <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($recentlyViewed as $item)
                            <x-property-card :property="$item" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Recent inquiries --}}
            <section>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent inquiries</h3>
                    <a href="{{ route('inquiries.index') }}" class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400">View all</a>
                </div>

                <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    @forelse ($inquiries as $inquiry)
                        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 last:border-0 dark:border-gray-800">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $inquiry->property?->title ?? 'Deleted listing' }}
                                </p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">
                                    {{ \Illuminate\Support\Str::limit($inquiry->message, 80) }}
                                </p>
                            </div>

                            <x-badge :badge="match ($inquiry->status->value) {
                                'new' => 'amber',
                                'read' => 'blue',
                                'replied' => 'green',
                                default => 'gray',
                            }" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                        </div>
                    @empty
                        <p class="p-8 text-center text-sm text-gray-500">No inquiries sent yet.</p>
                    @endforelse
                </div>
            </section>

            {{-- Tour requests --}}
            <section>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tour requests</h3>

                <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    @forelse ($tours as $tour)
                        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 last:border-0 dark:border-gray-800">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $tour->property?->title ?? 'Deleted listing' }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ $tour->scheduled_at?->format('D, M j, Y \a\t g:i A') }}
                                </p>
                            </div>

                            <x-badge :badge="match ($tour->status->value) {
                                'requested' => 'amber',
                                'confirmed' => 'green',
                                'completed' => 'teal',
                                'cancelled' => 'red',
                                default => 'gray',
                            }" :label="\Illuminate\Support\Str::ucfirst($tour->status->value)" />
                        </div>
                    @empty
                        <p class="p-8 text-center text-sm text-gray-500">No tour requests yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
