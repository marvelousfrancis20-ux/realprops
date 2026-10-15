@props(['page' => null, 'fallbackTitle' => 'Page'])

<x-site.layout :title="($page?->meta_title ?: ($page?->title ?: $fallbackTitle)).' — Real Props'" :description="$page?->meta_description">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => $page?->title ?: $fallbackTitle]]" />

        @if ($page)
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $page->title }}</h1>

            <div class="prose prose-gray mt-6 max-w-none dark:prose-invert">
                {!! $page->content !!}
            </div>
        @else
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $fallbackTitle }}</h1>
            <p class="mt-4 text-sm text-gray-500">This page has not been published yet.</p>
        @endif
    </div>
</x-site.layout>
