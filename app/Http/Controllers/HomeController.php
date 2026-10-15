<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Post;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Repositories\Contracts\PropertyRepositoryInterface;

class HomeController extends Controller
{
    public function __construct(private readonly PropertyRepositoryInterface $properties) {}

    public function index()
    {
        $stats = [
            'listings' => Property::approved()->published()->count(),
            'agents' => User::role('agent')->count(),
            'cities' => City::where('is_active', true)->count(),
            'viewed' => (int) Property::sum('views_count'),
        ];

        return view('home', [
            'featured' => $this->properties->featured(8),
            'latest' => $this->properties->latest(9),
            'popular' => $this->properties->mostViewed(4),
            'types' => PropertyType::where('is_active', true)
                ->withCount(['properties' => fn ($q) => $q->approved()->published()])
                ->orderBy('sort_order')
                ->get(),
            'cities' => City::where('is_active', true)
                ->with('state')
                ->withCount(['properties' => fn ($q) => $q->approved()->published()])
                ->orderByDesc('properties_count')
                ->take(8)
                ->get(),
            'posts' => Post::published()->with(['category', 'author'])->latest('published_at')->take(3)->get(),
            'stats' => $stats,
        ]);
    }
}
