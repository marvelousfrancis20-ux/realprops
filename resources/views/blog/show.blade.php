<x-site.layout :title="$post->meta_title ?: $post->title.' — Real Props'" :description="$post->meta_description ?: $post->excerptText()">
    <article class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <x-breadcrumbs :items="[
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => $post->title],
        ]" />

        @if ($post->category)
            <a href="{{ route('blog.category', $post->category->slug) }}" class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400">
                {{ $post->category->name }}
            </a>
        @endif

        <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">
            {{ $post->title }}
        </h1>

        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            {{ $post->author?->name }} · {{ $post->published_at?->format('F j, Y') }} · {{ $post->readingTime() }} min read
        </p>

        <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}"
             class="mt-6 aspect-[16/9] w-full rounded-2xl object-cover">

        <div class="prose prose-gray mt-8 max-w-none dark:prose-invert">
            {!! $post->content !!}
        </div>

        @if ($post->tags->isNotEmpty())
            <div class="mt-8 flex flex-wrap gap-2">
                @foreach ($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}"
                       class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 hover:bg-brand-100 hover:text-brand-700 dark:bg-gray-800 dark:text-gray-300">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-10 border-t border-gray-100 pt-6 dark:border-gray-800">
            <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400">
                ← Back to blog
            </a>
        </div>
    </article>
</x-site.layout>
