<x-site.layout :title="'Verified real estate agents — Real Props'">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Agent directory</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Work with verified agents who know the market.
        </p>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($agents as $agent)
                <a href="{{ route('agents.show', $agent->id) }}"
                   class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:border-brand-600 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-xl font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($agent->name, 0, 1)) }}
                        </span>

                        <div>
                            <h2 class="font-semibold text-gray-900 group-hover:text-brand-700 dark:text-gray-100">{{ $agent->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $agent->agentProfile?->agency_name ?? 'Independent agent' }}
                            </p>
                        </div>
                    </div>

                    @if ($agent->agentProfile?->bio)
                        <p class="mt-4 line-clamp-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ $agent->agentProfile->bio }}
                        </p>
                    @endif

                    <div class="mt-4 flex gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <span><strong class="text-gray-900 dark:text-gray-100">{{ number_format($agent->total_listings) }}</strong> listings</span>
                        @if ($agent->agentProfile?->years_experience)
                            <span><strong class="text-gray-900 dark:text-gray-100">{{ $agent->agentProfile->years_experience }}</strong> yrs experience</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $agents->links() }}
        </div>
    </div>
</x-site.layout>
