<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.pages.index', [
            'pages' => Page::orderBy('title')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.form', ['page' => new Page(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $page = new Page;
        $page->forceFill($this->validated($request, new Page));
        $page->save();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page created.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', ['page' => $page]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->forceFill($this->validated($request, $page));
        $page->save();

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page updated.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, Page $page): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]*$/', 'unique:pages,slug'.($page->exists ? ','.$page->id : '')],
            'content' => ['required', 'string', 'max:100000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? $page->is_active ?? true);

        if (blank($data['slug'] ?? null)) {
            unset($data['slug']);
        }

        return $data;
    }
}
