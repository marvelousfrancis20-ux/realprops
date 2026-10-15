<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        // Apply the saved theme before paint to avoid a flash of the wrong mode.
        (function () {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <title>{{ $title ?? \App\Models\Setting::get('meta_title', 'Real Props') }}</title>
    <meta name="description" content="{{ $description ?? \App\Models\Setting::get('meta_description', 'Find a place you will love.') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <div class="flex min-h-screen flex-col">
        <x-site.nav />

        <main class="flex-1">
            <x-site.toast />
            {{ $slot }}
        </main>

        <x-site.footer />
    </div>

    <script>
        document.addEventListener('toggle-theme', function () {
            var root = document.documentElement;
            root.classList.toggle('dark');
            localStorage.setItem('theme', root.classList.contains('dark') ? 'dark' : 'light');
        });
    </script>

    @stack('scripts')
</body>
</html>
