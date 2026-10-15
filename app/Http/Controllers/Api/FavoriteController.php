<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->user()
            ->favorites()
            ->latest()
            ->pluck('property_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $properties = Property::query()
            ->whereIn('id', $ids)
            ->where('is_approved', true)
            ->with(['images', 'type', 'agent:id,name,phone', 'agent.agentProfile'])
            ->when($ids !== [], function ($query) use ($ids) {
                $query->orderByRaw('FIELD(id, '.implode(',', $ids).')');
            })
            ->paginate(min(max((int) $request->query('per_page', 15), 1), 50));

        return PropertyResource::collection($properties);
    }

    public function toggle(Request $request, Property $property): JsonResponse
    {
        abort_unless($property->is_approved, 404);

        $user = $request->user();

        if ($user->hasFavorited($property)) {
            $user->favorites()->where('property_id', $property->id)->delete();

            return response()->json(['favorited' => false]);
        }

        $user->favorites()->firstOrCreate(['property_id' => $property->id]);

        return response()->json(['favorited' => true], 201);
    }

    public function destroy(Request $request, Property $property): JsonResponse
    {
        $request->user()->favorites()->where('property_id', $property->id)->delete();

        return response()->json(['favorited' => false]);
    }
}
