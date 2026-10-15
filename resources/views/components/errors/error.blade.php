@props(['code' => '404', 'title' => 'Page not found', 'message' => 'The page you are looking for does not exist or has been moved.'])

<section class="mx-auto flex min-h-[60vh] max-w-xl flex-col items-center justify-center px-4 py-20 text-center">
    <p class="text-7xl font-extrabold tracking-tight text-brand-700 dark:text-brand-400">{{ $code }}</p>

    <h1 class="mt-5 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h1>

    <p class="mt-3 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $message }}</p>

    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('home') }}"
           class="rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
            Back to home
        </a>
        <button type="button" onclick="history.back()"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-white dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900">
            Go back
        </button>
    </div>
</section>
