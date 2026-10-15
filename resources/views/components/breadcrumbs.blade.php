@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <li>
            <a href="{{ route('home') }}" class="hover:text-brand-700 dark:hover:text-brand-400">Home</a>
        </li>

        @foreach ($items as $item)
            <li aria-hidden="true" class="text-gray-300 dark:text-gray-600">/</li>
            @if (! empty($item['url']))
                <li>
                    <a href="{{ $item['url'] }}" class="hover:text-brand-700 dark:hover:text-brand-400">{{ $item['label'] }}</a>
                </li>
            @else
                <li aria-current="page" class="font-medium text-gray-900 dark:text-gray-100">{{ $item['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
