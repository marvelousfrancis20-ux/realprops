<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyType;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PropertyController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly PropertyRepositoryInterface $repository,
        private readonly PropertyService $service,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.Property::class, only: ['index']),
            new Middleware('can:create,'.Property::class, only: ['create', 'store']),
            new Middleware('can:view,property', only: ['show']),
            new Middleware('can:update,property', only: ['edit', 'update']),
            new Middleware('can:delete,property', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        return view('agent.properties.index', [
            'properties' => $this->repository->byAgent($request->user(), $request->query(), 10),
            'statusCounts' => [
                'total' => $request->user()->properties()->count(),
                'approved' => $request->user()->properties()->where('is_approved', true)->count(),
                'pending' => $request->user()->properties()->where('is_approved', false)->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('agent.properties.form', [
            'property' => new Property([
                'currency' => 'NGN',
                'bedrooms' => 0,
                'bathrooms' => 0,
                'toilets' => 0,
                'listing_type' => 'sale',
                'status' => 'available',
                'country' => 'Nigeria',
            ]),
            'types' => PropertyType::where('is_active', true)->orderBy('sort_order')->get(),
            'amenities' => Amenity::where('is_active', true)->orderBy('name')->get(),
            'listingCount' => $request->user()->properties()->count(),
            'listingLimit' => $request->user()->listingLimit(),
        ]);
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = $this->service->create($request->validated(), $request->user());

        return redirect()
            ->route('agent.properties.show', $property)
            ->with('status', $property->is_approved
                ? 'Listing published.'
                : 'Listing submitted. It will go live once an admin approves it.');
    }

    public function show(Request $request, Property $property): View
    {
        return view('agent.properties.show', [
            'property' => $property->load(['images', 'amenities', 'type', 'inquiries' => fn ($q) => $q->latest()->limit(5)]),
            'inquiryCount' => $property->inquiries()->count(),
            'viewCount' => $property->views_count,
        ]);
    }

    public function edit(Property $property): View
    {
        return view('agent.properties.form', [
            'property' => $property->load('amenities'),
            'types' => PropertyType::where('is_active', true)->orderBy('sort_order')->get(),
            'amenities' => Amenity::where('is_active', true)->orderBy('name')->get(),
            'listingCount' => $property->agent->properties()->count(),
            'listingLimit' => $property->agent->listingLimit(),
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->service->update($property, $request->validated());

        return redirect()
            ->route('agent.properties.show', $property)
            ->with('status', 'Listing updated.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->service->delete($property);

        return redirect()->route('agent.properties.index')->with('status', 'Listing deleted.');
    }
}
