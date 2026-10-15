@php
    $isEdit = $plan->exists;
    $action = $isEdit ? route('admin.plans.update', $plan) : route('admin.plans.store');
    $features = old('features', is_array($plan->features) ? implode("\n", $plan->features) : ($plan->features ?? ''));
@endphp

<x-panel.layout :panel="'Admin'" :title="($isEdit ? 'Edit plan' : 'New plan')">
    <form method="POST" action="{{ $action }}" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $plan->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="slug" value="Slug" />
                        <x-text-input id="slug" name="slug" class="mt-1 block w-full" :value="old('slug', $plan->slug)" placeholder="auto" />
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-5">
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $plan->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Features (one per line)</h3>
                <textarea id="features" name="features" rows="7"
                          class="mt-3 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ $features }}</textarea>
                <x-input-error :messages="$errors->get('features')" class="mt-2" />
            </div>
        </div>

        <aside class="space-y-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Billing</h3>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="price" value="Price" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $plan->price ?? '0')" required />
                    </div>
                    <div>
                        <x-input-label for="currency" value="Currency" />
                        <x-text-input id="currency" name="currency" class="mt-1 block w-full uppercase" :value="old('currency', $plan->currency ?? 'NGN')" maxlength="3" required />
                    </div>
                </div>
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                <x-input-error :messages="$errors->get('currency')" />

                <div class="mt-4">
                    <x-input-label for="billing_interval" value="Billing interval" />
                    <select id="billing_interval" name="billing_interval"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (['month' => 'Per month', 'year' => 'Per year'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('billing_interval', $plan->billing_interval ?? 'month') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('billing_interval')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="stripe_price_id" value="Stripe price ID" />
                    <x-text-input id="stripe_price_id" name="stripe_price_id" class="mt-1 block w-full font-mono text-xs" :value="old('stripe_price_id', $plan->stripe_price_id)" placeholder="price_..." />
                    <x-input-error :messages="$errors->get('stripe_price_id')" class="mt-2" />
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Limits</h3>

                <div class="mt-4">
                    <x-input-label for="listing_limit" value="Listing limit" />
                    <x-text-input id="listing_limit" name="listing_limit" type="number" min="0" class="mt-1 block w-full" :value="old('listing_limit', $plan->listing_limit ?? 0)" />
                    <p class="mt-1 text-xs text-gray-500">0 = unlimited listings.</p>
                    <x-input-error :messages="$errors->get('listing_limit')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="sort_order" value="Sort order" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="old('sort_order', $plan->sort_order ?? 0)" />
                </div>

                <label class="mt-4 flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                    <input type="hidden" name="allows_featured" value="0">
                    <input type="checkbox" name="allows_featured" value="1" @checked(old('allows_featured', $plan->allows_featured ?? false))
                           class="rounded border-gray-300 text-brand-700 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                    Allows featured listings
                </label>

                <label class="mt-3 flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true))
                           class="rounded border-gray-300 text-brand-700 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                    Active
                </label>
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>{{ $isEdit ? 'Save changes' : 'Create plan' }}</x-primary-button>
                <a href="{{ route('admin.plans.index') }}" class="text-sm font-medium text-gray-500 hover:text-brand-700">Cancel</a>
            </div>
        </aside>
    </form>
</x-panel.layout>
