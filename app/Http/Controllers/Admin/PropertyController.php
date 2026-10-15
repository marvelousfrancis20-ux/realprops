<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(private readonly PropertyService $service) {}

    public function index(Request $request): View
    {
        $status = $request->query('approval');

        $query = Property::query()
            ->with(['agent', 'images', 'type'])
            ->withCount('inquiries')
            ->latest();

        if ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif (filled($request->query('q'))) {
            $q = $request->query('q');
            $query->where(fn ($inner) => $inner
                ->where('title', 'like', '%'.$q.'%')
                ->orWhere('city', 'like', '%'.$q.'%')
                ->orWhere('slug', 'like', '%'.$q.'%'));
        }

        return view('admin.properties.index', [
            'properties' => $query->paginate(15)->withQueryString(),
            'counts' => [
                'all' => Property::count(),
                'pending' => Property::where('is_approved', false)->count(),
                'approved' => Property::where('is_approved', true)->count(),
                'featured' => Property::where('is_featured', true)->count(),
            ],
            'activeStatus' => $status,
        ]);
    }

    public function show(Property $property): View
    {
        return view('admin.properties.show', [
            'property' => $property->load(['agent.agentProfile', 'images', 'amenities', 'type', 'inquiries.user']),
        ]);
    }

    public function approve(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('approve', $property);

        $this->service->approve($property, $request->user());

        return back()->with('status', 'Listing approved and published.');
    }

    public function reject(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('reject', $property);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Please provide a rejection reason.',
        ]);

        $this->service->reject($property, $data['reason'], $request->user());

        return back()->with('status', 'Listing rejected. The agent has been notified.');
    }

    public function feature(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('feature', $property);

        $this->service->feature($property, ! $property->is_featured, $request->user());

        return back()->with('status', $property->is_featured ? 'Listing featured.' : 'Listing unfeatured.');
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $this->service->delete($property);

        return redirect()->route('admin.properties.index')->with('status', 'Listing deleted.');
    }
}
