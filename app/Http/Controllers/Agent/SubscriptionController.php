<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\AnalyticsService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function index(Request $request, AnalyticsService $analytics): View
    {
        $user = $request->user();

        return view('agent.subscription', [
            'plans' => $analytics->plans(),
            'currentPlan' => $this->payments->currentPlan($user),
            'subscription' => $user->subscriptions()->latest()->first(),
            'listingCount' => $user->properties()->count(),
            'listingLimit' => $user->listingLimit(),
            'stripeConfigured' => $this->payments->isConfigured(),
        ]);
    }

    public function checkout(Request $request, Plan $plan): RedirectResponse
    {
        abort_unless($plan->is_active && ! $plan->isFree(), 404);

        try {
            $url = $this->payments->checkout(
                $request->user(),
                $plan,
                route('agent.subscription.index', ['checkout' => 'success']),
                route('agent.subscription.index', ['checkout' => 'cancelled']),
            );
        } catch (\Throwable $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()->away($url);
    }

    public function portal(Request $request): RedirectResponse
    {
        try {
            $url = $this->payments->billingPortal($request->user(), route('agent.subscription.index'));
        } catch (\Throwable $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()->away($url);
    }
}
