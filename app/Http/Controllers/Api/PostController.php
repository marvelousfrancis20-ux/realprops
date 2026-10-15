<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query()->published()->with(['author:id,name', 'category'])->latest('published_at');

        if (filled($request->query('category'))) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->query('category')));
        }

        if (filled($request->query('tag'))) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->query('tag')));
        }

        $perPage = min(max((int) $request->query('per_page', 12), 1), 50);

        return PostResource::collection($query->paginate($perPage));
    }

    public function show(string $post): PostResource
    {
        $model = Post::query()
            ->published()
            ->where('slug', $post)
            ->with(['author:id,name', 'category', 'tags'])
            ->firstOrFail();

        return new PostResource($model);
    }

    public function categories()
    {
        return CategoryResource::collection(Category::orderBy('name')->get());
    }

    public function tags()
    {
        return TagResource::collection(Tag::orderBy('name')->get());
    }
}
