<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavedSearch\StoreSavedSearchRequest;
use App\Models\SavedSearch;
use App\Services\SearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SavedSearchController extends Controller
{
    public function __construct(private readonly SearchService $search) {}

    public function index(Request $request)
    {
        return view('saved-searches', [
            'savedSearches' => $request->user()->savedSearches()->latest()->get(),
        ]);
    }

    public function store(StoreSavedSearchRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $request->user()->savedSearches()->create([
            'name' => $data['name'],
            'filters' => $this->search->normalizeFilters($data['filters'] ?? []),
            'notify' => (bool) ($data['notify'] ?? true),
        ]);

        return back()->with('toast', 'Search saved. We will email you when new listings match.');
    }

    public function toggle(Request $request, SavedSearch $savedSearch): RedirectResponse
    {
        abort_unless($savedSearch->user_id === $request->user()->id, 403);

        $savedSearch->update(['notify' => ! $savedSearch->notify]);

        return back()->with('toast', $savedSearch->notify ? 'Email alerts on.' : 'Email alerts off.');
    }

    public function destroy(Request $request, SavedSearch $savedSearch): RedirectResponse
    {
        abort_unless($savedSearch->user_id === $request->user()->id, 403);

        $savedSearch->delete();

        return redirect()->route('saved-searches.index')->with('toast', 'Saved search deleted.');
    }
}
