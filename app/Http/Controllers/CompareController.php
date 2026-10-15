<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Compare up to 4 properties side by side (session driven).
 */
class CompareController extends Controller
{
    private const MAX = 4;

    public function index(Request $request)
    {
        $ids = $this->ids($request);

        return view('compare', [
            'properties' => Property::with(['images', 'type', 'amenities', 'agent'])
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn ($property) => array_search($property->id, $ids))
                ->values(),
            'ids' => $ids,
            'max' => self::MAX,
        ]);
    }

    public function add(Request $request, int $property): RedirectResponse
    {
        $ids = $this->ids($request);

        if (! in_array($property, $ids, true)) {
            if (count($ids) >= self::MAX) {
                return back()->with('toast', 'You can compare up to '.self::MAX.' properties at a time.');
            }

            $ids[] = $property;
            $request->session()->put('compare', $ids);

            return back()->with('toast', 'Added to compare.');
        }

        return back()->with('toast', 'Already in your compare list.');
    }

    public function remove(Request $request, int $property): RedirectResponse
    {
        $ids = array_values(array_diff($this->ids($request), [$property]));
        $request->session()->put('compare', $ids);

        return redirect()->route('compare.index');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('compare');

        return redirect()->route('compare.index');
    }

    /**
     * @return list<int>
     */
    private function ids(Request $request): array
    {
        return array_values(array_map('intval', $request->session()->get('compare', [])));
    }
}
