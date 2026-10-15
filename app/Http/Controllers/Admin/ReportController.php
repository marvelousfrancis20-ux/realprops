<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Inquiry;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Property;
use App\Models\TourRequest;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(AnalyticsService $analytics): View
    {
        return view('admin.reports', [
            'stats' => $analytics->adminStats(),
            'topAgents' => Property::query()
                ->selectRaw('agent_id, count(*) as listings, sum(views_count) as views')
                ->whereNotNull('agent_id')
                ->groupBy('agent_id')
                ->orderByDesc('listings')
                ->limit(10)
                ->with('agent:id,name')
                ->get(),
            'totals' => [
                'users' => User::count(),
                'agents' => User::role('agent')->count(),
                'properties' => Property::count(),
                'live' => Property::where('is_approved', true)->count(),
                'pending' => Property::where('is_approved', false)->count(),
                'inquiries' => Inquiry::count(),
                'tours' => TourRequest::count(),
                'posts' => Post::count(),
                'messages' => ContactMessage::count(),
                'plans' => Plan::count(),
            ],
        ]);
    }
}
