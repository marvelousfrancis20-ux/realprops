<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $q = $request->query('q');

        $query = Post::query()->with(['author', 'category'])->withCount('tags')->latest();

        if (filled($status)) {
            $query->where('status', $status);
        }

        if (filled($q)) {
            $query->where('title', 'like', '%'.$q.'%');
        }

        return view('admin.posts.index', [
            'posts' => $query->paginate(15)->withQueryString(),
            'counts' => [
                'all' => Post::count(),
                'published' => Post::where('status', PostStatus::Published)->count(),
                'draft' => Post::where('status', PostStatus::Draft)->count(),
                'scheduled' => Post::where('status', PostStatus::Scheduled)->count(),
            ],
            'activeStatus' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(['status' => PostStatus::Draft->value]),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'selectedTags' => collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $post = new Post;
        $post->author_id = $request->user()->id;
        $post->forceFill($data);
        $post->save();

        $this->syncTags($post, $request);
        $this->storeCover($post, $request);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'selectedTags' => $post->tags()->pluck('tags.id'),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $post->forceFill($this->validated($request));
        $post->save();

        $this->syncTags($post, $request);
        $this->storeCover($post, $request);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post moved to trash.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:100000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['required', 'in:'.implode(',', array_column(PostStatus::cases(), 'value'))],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'max:4096'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ], [
            'featured_image.max' => 'Image must be smaller than 4 MB.',
        ]);

        $featured = $data['featured_image'] ?? null;
        unset($data['featured_image']);

        if (filled($data['published_at']) && $data['status'] === PostStatus::Published->value) {
            $data['published_at'] = now()->toDateTimeString();
        }

        return $data;
    }

    private function syncTags(Post $post, Request $request): void
    {
        if ($request->has('tags')) {
            $post->tags()->sync(array_map('intval', (array) $request->input('tags', [])));
        }
    }

    private function storeCover(Post $post, Request $request): void
    {
        if (! $request->hasFile('featured_image')) {
            return;
        }

        $post->clearMediaCollection('featured');
        $post->addMedia($request->file('featured_image'))->toMediaCollection('featured');
    }
}
