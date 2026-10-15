<?php

namespace App\Http\Middleware;

use App\Models\Property;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackRecentlyViewed
{
    /**
     * Remember the last N properties viewed by this session (spec: recently viewed).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $routeParam = $request->route('property');

        $propertyId = match (true) {
            $routeParam instanceof Property => $routeParam->id,
            is_string($routeParam) => (int) Property::query()->where('slug', $routeParam)->value('id'),
            default => null,
        };

        if ($propertyId) {
            $ids = $request->session()->get('recently_viewed', []);
            $ids = array_values(array_diff($ids, [$propertyId]));
            array_unshift($ids, $propertyId);
            $request->session()->put('recently_viewed', array_slice($ids, 0, 8));
        }

        return $response;
    }
}
