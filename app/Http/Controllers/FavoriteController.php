<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with(['property.images', 'property.type', 'property.agent'])
            ->latest()
            ->paginate(12);

        return view('favorites', [
            'favorites' => $favorites,
            'properties' => $favorites->getCollection()->pluck('property')->filter(),
            'savedIds' => $request->user()->favoritePropertyIds(),
        ]);
    }

    public function toggle(Request $request, int $property): RedirectResponse
    {
        $model = Property::query()->findOrFail($property);

        Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'property_id' => $model->id,
        ]);

        return back()->with('toast', 'Saved to favorites.');
    }

    public function destroy(Request $request, int $property): RedirectResponse
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('property_id', $property)
            ->delete();

        return back()->with('toast', 'Removed from favorites.');
    }
}
