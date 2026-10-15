<x-panel.layout :panel="'Admin'" :title="'Blog posts'">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            @foreach (['all' => 'All', 'published' => 'Published', 'draft' => 'Drafts', 'scheduled' => 'Scheduled'] as $key => $label)
                <a href="{{ $key === 'all' ? route('admin.posts.index') : route('admin.posts.index', ['status' => $key]) }}"
                   class="rounded-full px-3 py-1.5 font-medium {{ ($activeStatus ?? null) === $key || ($key === 'all' && ! $activeStatus)
                       ? 'bg-brand-700 text-white'
                       : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs opacity-70">{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex gap-2">
            <form method="GET" action="{{ route('admin.posts.index') }}">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search title"
                       class="w-48 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </form>
            <a href="{{ route('admin.posts.create') }}"
               class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">+ New post</a>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Post</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Tags</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Published</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($posts as $post)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $post->coverUrl() }}" alt="" class="h-11 w-16 rounded-lg object-cover bg-gray-100">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="block max-w-xs truncate font-medium text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                        {{ $post->title }}
                                    </a>
                                    <p class="text-xs text-gray-500">by {{ $post->author?->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $post->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $post->tags_count }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="match ($post->status->value) {
                                'published' => 'green', 'scheduled' => 'blue', default => 'amber',
                            }" :label="\Illuminate\Support\Str::ucfirst($post->status->value)" />
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $post->published_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('blog.show', $post) }}" target="_blank" rel="noopener" class="text-gray-500 hover:underline">View</a>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-brand-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                      onsubmit="return confirm({{ json_encode('Move "'.$post->title.'" to trash?') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">No posts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $posts->links() }}
    </div>
</x-panel.layout>
