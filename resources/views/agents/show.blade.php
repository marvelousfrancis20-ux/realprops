<x-site.layout :title="$agent->name.' — Real Props'">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <x-breadcrumbs :items="[
            ['label' => 'Agents', 'url' => route('agents.index')],
            ['label' => $agent->name],
        ]" />

        <div class="rounded-2xl border border-gray-200 bg-white p-8 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <span class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-brand-100 text-3xl font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($agent->name, 0, 1)) }}
                </span>

                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $agent->name }}</h1>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">
                        {{ $agent->agentProfile?->agency_name ?? 'Independent agent' }}
                        @if ($agent->isVerifiedAgent())
                            · <span class="font-semibold text-brand-700 dark:text-brand-400">Verified agent</span>
                        @endif
                    </p>

                    @if ($agent->agentProfile?->bio)
                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            {{ $agent->agentProfile->bio }}
                        </p>
                    @endif
                </div>

                <div class="flex gap-3">
                    @if ($agent->phone)
                        <a href="tel:{{ $agent->phone }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:border-brand-600 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                            Call
                        </a>
                    @endif
                    @if ($agent->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $agent->whatsapp) }}" target="_blank" rel="noopener"
                           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                            WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 pt-6 sm:grid-cols-3 dark:border-gray-800">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($agent->total_listings) }}</p>
                    <p class="text-xs text-gray-500">Live listings</p>
                </div>
                @if ($agent->agentProfile?->years_experience)
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $agent->agentProfile->years_experience }}</p>
                        <p class="text-xs text-gray-500">Years experience</p>
                    </div>
                @endif
                @if ($agent->agentProfile?->license_number)
                    <div>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $agent->agentProfile->license_number }}</p>
                        <p class="text-xs text-gray-500">License</p>
                    </div>
                @endif
            </div>
        </div>

        <h2 class="mt-10 text-xl font-bold text-gray-900 dark:text-gray-100">Listings by {{ $agent->name }}</h2>

        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($properties as $property)
                <x-property-card :property="$property" :show-agent="false" />
            @empty
                <p class="col-span-full text-sm text-gray-500">No live listings right now.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    </div>
</x-site.layout>
