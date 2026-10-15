<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\AnalyticsService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $user = $request->user();

        $recentIds = $request->session()->get('recently_viewed', []);
        $recentlyViewed = $recentIds
            ? Property::query()
                ->whereIn('id', $recentIds)
                ->where('is_approved', true)
                ->where('status', 'available')
                ->with('images')
                ->get()
                ->sortBy(fn (Property $p) => array_search($p->id, $recentIds))
                ->values()
            : collect();

        return view('dashboard', [
            'favorites' => $user->favorites()->with('property.images')->latest()->take(6)->get(),
            'inquiries' => $user->inquiries()->with('property')->latest()->take(5)->get(),
            'tours' => $user->tourRequests()->with('property.images')->latest()->take(5)->get(),
            'savedSearches' => $user->savedSearches()->latest()->take(5)->get(),
            'recentlyViewed' => $recentlyViewed,
            'plans' => $analytics->plans(),
            'currentPlan' => app(PaymentService::class)->currentPlan($user),
        ]);
    }
}
