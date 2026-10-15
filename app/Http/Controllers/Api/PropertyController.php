<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgentResource;
use App\Http\Resources\PropertyResource;
use App\Models\AgentProfile;
use App\Models\Property;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertyController extends Controller
{
    public function __construct(private readonly PropertyRepositoryInterface $repository) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $properties = $this->repository->paginateApproved($request->query(), $this->perPage($request));

        return PropertyResource::collection($properties);
    }

    public function featured(Request $request): AnonymousResourceCollection
    {
        $properties = $this->repository->paginateFeatured($this->perPage($request));

        return PropertyResource::collection($properties);
    }

    public function show(Property $property): PropertyResource
    {
        abort_unless($property->is_approved, 404);

        $property->load(['images', 'amenities', 'type', 'agent:id,name,email,phone,whatsapp,bio,avatar_path', 'agent.agentProfile']);
        $property->increment('views_count');

        return new PropertyResource($property);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $properties = $this->repository->paginateApproved([
            ...$request->query(),
            'keyword' => $data['q'] ?? null,
        ], $this->perPage($request));

        return PropertyResource::collection($properties);
    }

    public function agents(Request $request): AnonymousResourceCollection
    {
        $agents = AgentProfile::query()
            ->whereNotNull('verified_at')
            ->with('user:id,name,phone,avatar_path')
            ->withCount('properties')
            ->latest()
            ->paginate($this->perPage($request));

        return AgentResource::collection($agents);
    }

    public function agent(Request $request, int $id): AgentResource
    {
        $profile = AgentProfile::query()
            ->where('user_id', $id)
            ->whereNotNull('verified_at')
            ->with('user:id,name,phone,whatsapp,bio,avatar_path')
            ->withCount('properties')
            ->firstOrFail();

        return new AgentResource($profile);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->query('per_page', 15), 1), 50);
    }
}
