<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'page' => Page::where('slug', 'about')->where('is_active', true)->first(),
        ]);
    }

    public function faq()
    {
        return view('pages.faq', [
            'page' => Page::where('slug', 'faq')->where('is_active', true)->first(),
        ]);
    }

    public function terms()
    {
        return view('pages.terms', [
            'page' => Page::where('slug', 'terms')->where('is_active', true)->first(),
        ]);
    }

    public function privacy()
    {
        return view('pages.privacy', [
            'page' => Page::where('slug', 'privacy')->where('is_active', true)->first(),
        ]);
    }

    /**
     * Any other CMS managed page (about/faq/terms/privacy have dedicated views).
     */
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('pages.show', ['page' => $page]);
    }
}
