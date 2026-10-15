<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\Inquiry;
use App\Models\Plan;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\TourRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Cashier\Subscription;

/**
 * Aggregates dashboard statistics for agent + admin analytics (Chart.js data).
 */
class AnalyticsService
{
    /**
     * @return array<string, mixed>
     */
    public function agentStats(User $agent): array
    {
        $properties = $agent->properties();

        $totalListings = (clone $properties)->count();
        $liveListings = (clone $properties)->where('is_approved', true)->count();
        $pendingListings = (clone $properties)->where('is_approved', false)->count();
        $totalViews = (int) (clone $properties)->sum('views_count');
        $totalInquiries = $agent->receivedInquiries()->count();
        $totalTours = TourRequest::where('agent_id', $agent->id)->count();
        $conversion = $totalViews > 0 ? round(($totalInquiries / $totalViews) * 100, 1) : 0.0;

        $viewsByListing = (clone $properties)
            ->orderByDesc('views_count')
            ->take(8)
            ->get(['id', 'title', 'views_count'])
            ->map(fn (Property $property) => [
                'label' => str($property->title)->limit(22),
                'value' => (int) $property->views_count,
            ])
            ->values()
            ->all();

        $inquiriesByMonth = $this->lastMonths(6, function (Carbon $start, Carbon $end) use ($agent) {
            return $agent->receivedInquiries()
                ->whereBetween('created_at', [$start, $end])
                ->count();
        });

        $toursByMonth = $this->lastMonths(6, function (Carbon $start, Carbon $end) use ($agent) {
            return TourRequest::where('agent_id', $agent->id)
                ->whereBetween('created_at', [$start, $end])
                ->count();
        });

        return [
            'totals' => [
                'listings' => $totalListings,
                'live' => $liveListings,
                'pending' => $pendingListings,
                'views' => $totalViews,
                'inquiries' => $totalInquiries,
                'tours' => $totalTours,
                'conversion_rate' => $conversion,
            ],
            'charts' => [
                'views_by_listing' => $viewsByListing,
                'inquiries_by_month' => $inquiriesByMonth,
                'tours_by_month' => $toursByMonth,
            ],
            'top_listings' => $agent->properties()
                ->with('images')
                ->orderByDesc('views_count')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function adminStats(): array
    {
        $now = now();

        return [
            'totals' => [
                'users' => User::where('created_at', '>=', $now->copy()->startOfYear())->count(),
                'users_all' => User::count(),
                'agents' => User::role('agent')->count(),
                'admins' => User::role('admin')->count(),
                'listings' => Property::count(),
                'pending_approvals' => Property::where('is_approved', false)->count(),
                'inquiries_this_month' => Inquiry::where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                'tours_this_month' => TourRequest::where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                'messages' => ContactMessage::count(),
                'revenue' => app(PaymentService::class)->totalRevenue(),
            ],
            'charts' => [
                'listings_per_month' => $this->lastMonths(12, function (Carbon $start, Carbon $end) {
                    return Property::whereBetween('created_at', [$start, $end])->count();
                }),
                'inquiries_per_month' => $this->lastMonths(12, function (Carbon $start, Carbon $end) {
                    return Inquiry::whereBetween('created_at', [$start, $end])->count();
                }),
                'revenue_per_month' => $this->lastMonths(6, function (Carbon $start, Carbon $end) {
                    return round((float) Subscription::where('stripe_status', 'active')
                        ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                        ->whereBetween('subscriptions.created_at', [$start, $end])
                        ->sum('plans.price'), 2);
                }),
                'listings_by_type' => Property::query()
                    ->selectRaw('property_type_id, count(*) as total')
                    ->groupBy('property_type_id')
                    ->orderByDesc('total')
                    ->take(6)
                    ->get()
                    ->map(fn ($row) => [
                        'label' => PropertyType::find($row->property_type_id)?->name ?? 'Unknown',
                        'value' => (int) $row->total,
                    ])
                    ->values()
                    ->all(),
            ],
            'top_agents' => User::role('agent')
                ->withCount(['properties', 'receivedInquiries'])
                ->orderByDesc('properties_count')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Labels + values for the last $months months (oldest first).
     *
     * @param  callable(Carbon, Carbon): mixed  $resolver
     * @return list<array{label: string, value: mixed}>
     */
    private function lastMonths(int $months, callable $resolver): array
    {
        $points = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end = $start->copy()->endOfMonth();

            $points[] = [
                'label' => $start->format('M'),
                'value' => $resolver($start, $end),
            ];
        }

        return $points;
    }

    /**
     * @return Collection<int, Plan>
     */
    public function plans(): Collection
    {
        return Plan::query()->where('is_active', true)->orderBy('sort_order')->get();
    }
}
