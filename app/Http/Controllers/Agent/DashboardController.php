<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\TourRequest;
use App\Services\AnalyticsService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $user = $request->user();

        return view('agent.dashboard', [
            'stats' => $analytics->agentStats($user),
            'recentInquiries' => $user->receivedInquiries()->with('property.images')->latest()->take(5)->get(),
            'recentTours' => TourRequest::where('agent_id', $user->id)
                ->with(['property.images', 'user'])
                ->latest()
                ->take(5)
                ->get(),
            'listings' => $user->properties()->with('images')->latest()->take(5)->get(),
            'currentPlan' => app(PaymentService::class)->currentPlan($user),
            'listingLimit' => $user->listingLimit(),
            'listingCount' => $user->properties()->count(),
        ]);
    }

    public function analytics(Request $request, AnalyticsService $analytics)
    {
        return view('agent.analytics', [
            'stats' => $analytics->agentStats($request->user()),
        ]);
    }
}
