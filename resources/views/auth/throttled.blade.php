<x-guest-layout>
    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-lg dark:bg-gray-900">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                Too many attempts
            </h1>

            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                We have detected too many requests from your device. Please wait a minute and try again.
            </p>

            <a href="{{ route('home') }}"
               class="mt-6 inline-flex items-center rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-teal-800">
                Back to home
            </a>
        </div>
    </div>
</x-guest-layout>
