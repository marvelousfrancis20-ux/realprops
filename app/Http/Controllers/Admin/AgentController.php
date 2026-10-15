<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = AgentProfile::query()->with('user')->withCount('properties')->latest();

        if ($status === 'verified') {
            $query->whereNotNull('verified_at');
        } elseif ($status === 'pending') {
            $query->whereNull('verified_at');
        }

        return view('admin.agents.index', [
            'agents' => $query->paginate(15)->withQueryString(),
            'counts' => [
                'all' => AgentProfile::count(),
                'verified' => AgentProfile::whereNotNull('verified_at')->count(),
                'pending' => AgentProfile::whereNull('verified_at')->count(),
            ],
            'activeStatus' => $status,
        ]);
    }

    public function show(AgentProfile $agent): View
    {
        return view('admin.agents.show', [
            'agent' => $agent->load('user'),
            'properties' => $agent->properties()->with('images')->latest()->take(9)->get(),
            'stats' => [
                'listings' => $agent->properties()->count(),
                'live' => $agent->properties()->where('is_approved', true)->count(),
                'inquiries' => $agent->user->receivedInquiries()->count(),
            ],
        ]);
    }

    public function verify(Request $request, AgentProfile $agent): RedirectResponse
    {
        $this->authorize('verify', $agent);

        $agent->update([
            'verified_at' => now(),
            'verified_by' => (string) $request->user()->id,
        ]);

        activity()->performedOn($agent)->causedBy($request->user())->log('verified agent');

        return back()->with('status', $agent->user->name.' has been verified.');
    }

    public function unverify(Request $request, AgentProfile $agent): RedirectResponse
    {
        $this->authorize('verify', $agent);

        $agent->update([
            'verified_at' => null,
            'verified_by' => null,
        ]);

        activity()->performedOn($agent)->causedBy($request->user())->log('unverified agent');

        return back()->with('status', 'Agent verification removed.');
    }
}
