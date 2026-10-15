<?php

namespace App\Http\Controllers;

use App\Models\User;

class AgentPublicController extends Controller
{
    public function index()
    {
        $agents = User::role('agent')
            ->where('is_suspended', false)
            ->with(['agentProfile', 'properties' => fn ($q) => $q->approved()->published()->latest()->limit(6)])
            ->withCount(['properties as total_listings' => fn ($q) => $q->approved()->published()])
            ->withCount(['receivedInquiries as total_inquiries'])
            ->orderBy('total_listings', 'desc')
            ->paginate(12);

        return view('agents.index', ['agents' => $agents]);
    }

    public function show(int $user)
    {
        $agent = User::role('agent')
            ->with(['agentProfile', 'properties' => fn ($q) => $q->approved()->published()->with('images')->latest()->limit(12)])
            ->withCount(['properties as total_listings' => fn ($q) => $q->approved()->published()])
            ->findOrFail($user);

        return view('agents.show', [
            'agent' => $agent,
            'properties' => $agent->properties()->approved()->published()->with(['images', 'type'])->latest()->paginate(9),
        ]);
    }
}
