<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', [
            'posts' => $posts,
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'tags' => Tag::withCount('posts')->orderBy('name')->take(20)->get(),
        ]);
    }

    public function show(string $post)
    {
        $model = Post::published()
            ->where('slug', $post)
            ->with(['category', 'tags', 'author'])
            ->firstOrFail();

        $model->increment('views_count');

        return view('blog.show', [
            'post' => $model,
            'related' => Post::published()
                ->where('id', '!=', $model->id)
                ->when($model->category_id, fn ($q) => $q->where('category_id', $model->category_id))
                ->with('category')
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->posts()
            ->published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', [
            'posts' => $posts,
            'currentCategory' => $category,
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'tags' => Tag::withCount('posts')->orderBy('name')->take(20)->get(),
        ]);
    }

    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = $tag->posts()
            ->published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', [
            'posts' => $posts,
            'currentTag' => $tag,
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'tags' => Tag::withCount('posts')->orderBy('name')->take(20)->get(),
        ]);
    }
}
