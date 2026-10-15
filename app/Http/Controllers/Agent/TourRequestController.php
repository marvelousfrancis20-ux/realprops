<?php

namespace App\Http\Controllers\Agent;

use App\Enums\TourStatus;
use App\Http\Controllers\Controller;
use App\Models\TourRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = TourRequest::where('agent_id', $request->user()->id)
            ->with(['property.images', 'user'])
            ->orderBy('scheduled_at');

        if (filled($status)) {
            $query->where('status', $status);
        }

        return view('agent.tours.index', [
            'tours' => $query->paginate(15)->withQueryString(),
            'counts' => [
                'all' => TourRequest::where('agent_id', $request->user()->id)->count(),
                'requested' => TourRequest::where('agent_id', $request->user()->id)->where('status', TourStatus::Requested)->count(),
                'confirmed' => TourRequest::where('agent_id', $request->user()->id)->where('status', TourStatus::Confirmed)->count(),
                'completed' => TourRequest::where('agent_id', $request->user()->id)->where('status', TourStatus::Completed)->count(),
                'cancelled' => TourRequest::where('agent_id', $request->user()->id)->where('status', TourStatus::Cancelled)->count(),
            ],
            'activeStatus' => $status,
        ]);
    }

    public function update(Request $request, TourRequest $tour): RedirectResponse
    {
        $this->authorize('update', $tour);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(TourStatus::cases(), 'value'))],
            'agent_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $tour->update([
            'status' => $data['status'],
            'agent_notes' => $data['agent_notes'] ?? $tour->agent_notes,
        ]);

        return back()->with('status', 'Tour request marked as '.$data['status'].'.');
    }
}
