<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\TourRequest;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics): View
    {
        return view('admin.dashboard', [
            'stats' => $analytics->adminStats(),
            'pendingProperties' => Property::where('is_approved', false)
                ->with(['agent', 'images', 'type'])
                ->latest()
                ->take(6)
                ->get(),
            'recentInquiries' => Inquiry::with(['property', 'user'])->latest()->take(5)->get(),
            'recentTours' => TourRequest::with(['property', 'user', 'agent'])->latest()->take(5)->get(),
            'recentMessages' => ContactMessage::latest()->take(5)->get(),
            'verifiedAgents' => User::role('agent')
                ->whereHas('agentProfile', fn ($q) => $q->whereNull('verified_at'))
                ->with('agentProfile')
                ->take(5)
                ->get(),
        ]);
    }
}
