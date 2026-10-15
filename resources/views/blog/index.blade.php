<x-site.layout :title="'Blog — Real Props'">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    @if (isset($currentCategory)) {{ $currentCategory->name }}
                    @elseif (isset($currentTag)) #{{ $currentTag->name }}
                    @else Blog
                    @endif
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Guides, market news and tips for buyers, renters and agents.
                </p>
            </div>

            <nav class="flex flex-wrap gap-2 text-sm">
                <a href="{{ route('blog.index') }}"
                   class="rounded-full px-3 py-1.5 font-medium {{ request()->routeIs('blog.index') && ! request()->routeIs('blog.category', 'blog.tag') ? 'bg-brand-700 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                    All
                </a>

                @foreach (\App\Models\Category::orderBy('name')->get() as $category)
                    <a href="{{ route('blog.category', $category->slug) }}"
                       class="rounded-full px-3 py-1.5 font-medium {{ isset($currentCategory) && $currentCategory->is($category) ? 'bg-brand-700 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <a href="{{ route('blog.show', $post) }}"
                   class="group overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="aspect-[16/9] overflow-hidden bg-gray-100 dark:bg-gray-800">
                        <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy">
                    </div>

                    <div class="p-5">
                        @if ($post->category)
                            <span class="text-xs font-semibold uppercase tracking-wide text-brand-700 dark:text-brand-400">
                                {{ $post->category->name }}
                            </span>
                        @endif

                        <h2 class="mt-2 line-clamp-2 text-lg font-semibold text-gray-900 group-hover:text-brand-700 dark:text-gray-100">
                            {{ $post->title }}
                        </h2>

                        <p class="mt-2 line-clamp-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ $post->excerptText() }}
                        </p>

                        <p class="mt-4 text-xs text-gray-400">
                            {{ $post->author?->name }} · {{ $post->published_at?->diffForHumans() }} · {{ $post->readingTime() }} min read
                        </p>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-sm text-gray-500">No articles published yet.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</x-site.layout>
