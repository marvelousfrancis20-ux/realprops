<x-panel.layout :panel="'Admin'" :title="'Agent review'">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                            {{ $agent->user?->initials() }}
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $agent->user?->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $agent->user?->email }} &middot; {{ $agent->user?->phone }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if ($agent->isVerified())
                                    <x-badge badge="green" label="Verified agent" />
                                @else
                                    <x-badge badge="amber" label="Pending verification" />
                                @endif
                                @if ($agent->user?->isSuspended())
                                    <x-badge badge="red" label="Suspended" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 pt-5 text-sm sm:grid-cols-4 dark:border-gray-800">
                    <div><dt class="text-gray-500">Agency</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $agent->agency_name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">License no.</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $agent->license_number ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Experience</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $agent->years_experience ?? 0 }} yrs</dd></div>
                    <div><dt class="text-gray-500">Verified on</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $agent->verified_at?->format('M j, Y') ?? '—' }}</dd></div>
                </dl>

                @if ($agent->bio)
                    <p class="mt-5 border-t border-gray-100 pt-4 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">{{ $agent->bio }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Listings ({{ $stats['listings'] }})</h3>
                    <span class="text-xs text-gray-500">{{ $stats['live'] }} live</span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @forelse ($properties as $property)
                        <a href="{{ route('admin.properties.show', $property) }}" class="group">
                            <img src="{{ $property->imageUrl() }}" alt="{{ $property->imageAlt() }}"
                                 class="h-28 w-full rounded-lg object-cover transition group-hover:opacity-90">
                            <p class="mt-1 truncate text-xs font-medium text-gray-700 dark:text-gray-300">{{ $property->title }}</p>
                            <p class="text-xs text-gray-500">{{ $property->formattedPrice() }}</p>
                        </a>
                    @empty
                        <p class="col-span-3 py-6 text-center text-sm text-gray-500">This agent has no listings yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Verification documents</h3>

                <div class="mt-3 space-y-2 text-sm">
                    @if ($agent->id_document_path)
                        <a href="{{ $agent->id_document_path }}" target="_blank" rel="noopener"
                           class="block rounded-lg bg-gray-50 px-3 py-2 font-medium text-brand-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-brand-400">
                            Government ID ↗
                        </a>
                    @else
                        <p class="rounded-lg bg-gray-50 px-3 py-2 text-gray-500 dark:bg-gray-800">No ID uploaded.</p>
                    @endif

                    @if ($agent->license_document_path)
                        <a href="{{ $agent->license_document_path }}" target="_blank" rel="noopener"
                           class="block rounded-lg bg-gray-50 px-3 py-2 font-medium text-brand-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-brand-400">
                            License document ↗
                        </a>
                    @else
                        <p class="rounded-lg bg-gray-50 px-3 py-2 text-gray-500 dark:bg-gray-800">No license uploaded.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Activity</h3>
                <dl class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between"><dt class="text-gray-500">Total listings</dt><dd class="font-semibold">{{ $stats['listings'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Live listings</dt><dd class="font-semibold">{{ $stats['live'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Inquiries received</dt><dd class="font-semibold">{{ $stats['inquiries'] }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                @if ($agent->isVerified())
                    <form method="POST" action="{{ route('admin.agents.unverify', $agent) }}"
                          onsubmit="return confirm('Remove verification from this agent?')">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-md border border-amber-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 hover:bg-amber-50 dark:border-amber-800 dark:text-amber-400 dark:hover:bg-amber-900/30">
                            Remove verification
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.agents.verify', $agent) }}">
                        @csrf
                        <x-primary-button class="w-full justify-center">Verify agent</x-primary-button>
                    </form>
                @endif

                <a href="{{ route('admin.agents.index') }}" class="mt-3 block text-center text-sm font-medium text-gray-500 hover:text-brand-700">
                    &larr; Back to agents
                </a>
            </div>
        </aside>
    </div>
</x-panel.layout>
