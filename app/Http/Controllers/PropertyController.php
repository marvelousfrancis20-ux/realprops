<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\PropertyService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyRepositoryInterface $properties,
        private readonly PropertyService $service,
    ) {}

    /**
     * Property listing page (interactive filters are rendered by Livewire).
     */
    public function index(Request $request)
    {
        return view('properties.index', [
            'filters' => $request->query(),
            'view' => $request->query('view') === 'map' ? 'map' : 'grid',
        ]);
    }

    public function show(Request $request, string $property)
    {
        $model = $this->properties->findBySlug($property);

        if (! $model->is_approved && ! $request->user()?->can('update', $model)) {
            abort(404);
        }

        $this->service->incrementViews($model);

        $recentIds = $request->session()->get('recently_viewed', []);
        $recentlyViewed = $recentIds
            ? Property::query()
                ->whereIn('id', $recentIds)
                ->whereKeyNot($model->id)
                ->where('is_approved', true)
                ->where('status', 'available')
                ->with('images')
                ->get()
                ->sortBy(fn (Property $p) => array_search($p->id, $recentIds))
                ->values()
            : collect();

        return view('properties.show', [
            'property' => $model->loadMissing(['images', 'amenities', 'type', 'agent.agentProfile']),
            'similar' => $this->properties->similar($model, 4),
            'recentlyViewed' => $recentlyViewed,
            'savedIds' => $request->user()?->favoritePropertyIds() ?? collect(),
        ]);
    }
}
