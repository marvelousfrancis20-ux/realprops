<x-panel.layout :panel="'Agent'" :title="'Subscription'">
    @if (request()->query('checkout') === 'success')
        <div class="mb-5 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
            Checkout completed. Your plan will activate as soon as Stripe confirms the payment.
        </div>
    @elseif (request()->query('checkout') === 'cancelled')
        <div class="mb-5 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
            Checkout was cancelled — you have not been charged.
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Current plan</p>
                <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $currentPlan?->name ?? 'Free' }}
                    @if ($currentPlan)
                        <span class="text-sm font-medium text-gray-500">/ {{ $currentPlan->billing_interval }}</span>
                    @endif
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $listingLimit === 0 ? 'Unlimited listings' : "{$listingCount} of {$listingLimit} listings used" }}
                </p>
            </div>

            @if ($subscription)
                <form method="POST" action="{{ route('agent.subscription.portal') }}">
                    @csrf
                    <x-secondary-button>Manage billing</x-secondary-button>
                </form>
            @endif
        </div>

        @if ($listingLimit > 0)
            <div class="mt-4 h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                <div class="h-2 rounded-full bg-brand-700" style="width: {{ min(100, ($listingCount / max(1, $listingLimit)) * 100) }}%"></div>
            </div>
        @endif
    </div>

    @if (! $stripeConfigured)
        <div class="mt-5 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
            Stripe is not configured yet. Add your <code>STRIPE_KEY</code> and <code>STRIPE_SECRET</code> keys to <code>.env</code> to enable paid plans.
        </div>
    @endif

    <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($plans as $plan)
            @php
                $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
            @endphp
            <div class="relative flex flex-col rounded-2xl border {{ $isCurrent ? 'border-brand-600 ring-2 ring-brand-600/30' : 'border-gray-200' }} bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                @if ($isCurrent)
                    <span class="absolute -top-3 start-6 rounded-full bg-brand-700 px-3 py-0.5 text-xs font-semibold text-white">Current</span>
                @elseif ($plan->allows_featured)
                    <span class="absolute -top-3 start-6 rounded-full bg-accent-500 px-3 py-0.5 text-xs font-semibold text-white">Popular</span>
                @endif

                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $plan->name }}</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $plan->priceLabel() }}
                    <span class="text-sm font-medium text-gray-500">/ {{ $plan->billing_interval }}</span>
                </p>
                <p class="mt-2 text-sm text-gray-500">{{ $plan->description }}</p>

                <ul class="mt-4 flex-1 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    @foreach ((array) $plan->features as $feature)
                        <li class="flex gap-2"><span class="text-brand-600">✓</span> {{ $feature }}</li>
                    @endforeach
                    <li class="flex gap-2"><span class="text-brand-600">✓</span> {{ $plan->isUnlimited() ? 'Unlimited listings' : $plan->listing_limit.' listings' }}</li>
                </ul>

                @if ($isCurrent)
                    <span class="mt-6 block rounded-lg bg-gray-100 px-4 py-2.5 text-center text-sm font-semibold text-gray-500 dark:bg-gray-800">
                        Active plan
                    </span>
                @elseif ($plan->isFree())
                    <a href="{{ route('agent.properties.create') }}"
                       class="mt-6 block rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Current fallback
                    </a>
                @else
                    <form method="POST" action="{{ route('agent.subscription.checkout', $plan) }}" class="mt-6">
                        @csrf
                        <x-primary-button class="w-full justify-center" :disabled="! $stripeConfigured">
                            Upgrade to {{ $plan->name }}
                        </x-primary-button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <p class="mt-6 text-xs text-gray-500">
        Payments are processed by Stripe in test mode. Use card <code>4242 4242 4242 4242</code> with any future expiry and CVC.
    </p>
</x-panel.layout>
