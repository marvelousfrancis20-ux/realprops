<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use RuntimeException;

class PaymentService
{
    public function __construct()
    {
        // Stripe test-mode credentials come from STRIPE_KEY / STRIPE_SECRET.
    }

    public function isConfigured(): bool
    {
        return filled(config('services.stripe.key')) && filled(config('services.stripe.secret'));
    }

    /**
     * Start a Stripe Checkout session for a premium plan.
     */
    public function checkout(User $user, Plan $plan, string $successUrl, string $cancelUrl): string
    {
        if (! $plan->stripe_price_id) {
            throw new RuntimeException('This plan cannot be purchased online yet.');
        }

        if (! $this->isConfigured()) {
            throw new RuntimeException('Stripe is not configured. Add STRIPE_KEY and STRIPE_SECRET to your .env file.');
        }

        $subscription = $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

        return $subscription instanceof RedirectResponse
            ? $subscription->getTargetUrl()
            : (string) $subscription;
    }

    public function billingPortal(User $user, string $returnUrl): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Stripe is not configured. Add STRIPE_KEY and STRIPE_SECRET to your .env file.');
        }

        $response = $user->redirectToBillingPortal($returnUrl);

        return $response->getTargetUrl();
    }

    /**
     * Attach our local plan to a Stripe subscription record.
     */
    public function syncPlan(Subscription $subscription): void
    {
        $plan = Plan::query()->where('stripe_price_id', $subscription->stripe_price)->first();

        if ($plan && $subscription->plan_id !== $plan->id) {
            $subscription->forceFill(['plan_id' => $plan->id])->save();
        }
    }

    public function currentPlan(User $user): ?Plan
    {
        $subscription = $user->subscriptions()
            ->where('stripe_status', 'active')
            ->whereNotNull('plan_id')
            ->with('plan')
            ->first();

        return $subscription?->plan;
    }

    /**
     * Lifetime value derived from active subscriptions (test-mode friendly).
     */
    public function totalRevenue(): float
    {
        return (float) Subscription::query()
            ->where('stripe_status', 'active')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->sum('plans.price');
    }

    public function handleWebhook(object $payload): void
    {
        Log::info('Stripe webhook received', [
            'type' => $payload->type ?? null,
        ]);
    }

    public static function useCustomModels(): void
    {
        Cashier::useSubscriptionModel(Subscription::class);
    }
}
