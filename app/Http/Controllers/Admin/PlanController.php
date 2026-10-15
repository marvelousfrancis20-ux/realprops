<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::orderBy('sort_order')->orderBy('price')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.plans.form', [
            'plan' => new Plan([
                'currency' => 'NGN',
                'billing_interval' => 'month',
                'is_active' => true,
                'listing_limit' => 0,
                'allows_featured' => false,
                'features' => [],
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $plan = new Plan;
        $plan->forceFill($this->validated($request, $plan));
        $plan->save();

        return redirect()
            ->route('admin.plans.edit', $plan)
            ->with('status', 'Plan created.');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.form', ['plan' => $plan]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $plan->forceFill($this->validated($request, $plan));
        $plan->save();

        return redirect()
            ->route('admin.plans.edit', $plan)
            ->with('status', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('admin.plans.index')->with('status', 'Plan deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, Plan $plan): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:plans,slug'.($plan->exists ? ','.$plan->id : '')],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_interval' => ['required', 'in:month,year'],
            'stripe_price_id' => ['nullable', 'string', 'max:255'],
            'listing_limit' => ['required', 'integer', 'min:0', 'max:10000'],
            'allows_featured' => ['sometimes', 'boolean'],
            'features' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $data['price'] = number_format((float) $data['price'], 2, '.', '');
        $data['allows_featured'] = (bool) ($data['allows_featured'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $features = array_values(array_filter(array_map('trim', explode("\n", (string) ($data['features'] ?? '')))));
        unset($data['features']);
        $data['features'] = $features;

        if (blank($data['slug'] ?? null)) {
            unset($data['slug']);
        }

        return $data;
    }
}
