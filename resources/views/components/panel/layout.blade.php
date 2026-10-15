@php
    $menus = [
        'Agent' => [
            ['label' => 'Dashboard', 'href' => route('agent.dashboard'), 'active' => 'agent.dashboard', 'icon' => '🏠'],
            ['label' => 'My properties', 'href' => route('agent.properties.index'), 'active' => 'agent.properties.*', 'icon' => '🏢'],
            ['label' => 'Add property', 'href' => route('agent.properties.create'), 'active' => 'agent.properties.create', 'icon' => '➕'],
            ['label' => 'Inquiries', 'href' => route('agent.inquiries.index'), 'active' => 'agent.inquiries.*', 'icon' => '✉️'],
            ['label' => 'Tour requests', 'href' => route('agent.tours.index'), 'active' => 'agent.tours.*', 'icon' => '📅'],
            ['label' => 'Analytics', 'href' => route('agent.analytics'), 'active' => 'agent.analytics', 'icon' => '📊'],
            ['label' => 'Subscription', 'href' => route('agent.subscription.index'), 'active' => 'agent.subscription.*', 'icon' => '💳'],
            ['label' => 'Profile', 'href' => route('agent.profile.edit'), 'active' => 'agent.profile.*', 'icon' => '👤'],
        ],
        'Admin' => [
            ['label' => 'Dashboard', 'href' => route('admin.dashboard'), 'active' => 'admin.dashboard', 'icon' => '📊'],
            ['label' => 'Properties', 'href' => route('admin.properties.index'), 'active' => 'admin.properties.*', 'icon' => '🏢'],
            ['label' => 'Users', 'href' => route('admin.users.index'), 'active' => 'admin.users.*', 'icon' => '👥'],
            ['label' => 'Agents', 'href' => route('admin.agents.index'), 'active' => 'admin.agents.*', 'icon' => '🧑‍💼'],
            ['label' => 'Contact messages', 'href' => route('admin.contact-messages.index'), 'active' => 'admin.contact-messages.*', 'icon' => '✉️'],
            ['label' => 'Blog posts', 'href' => route('admin.posts.index'), 'active' => 'admin.posts.*', 'icon' => '📝'],
            ['label' => 'Pages', 'href' => route('admin.pages.index'), 'active' => 'admin.pages.*', 'icon' => '📄'],
            ['label' => 'Plans', 'href' => route('admin.plans.index'), 'active' => 'admin.plans.*', 'icon' => '💳'],
            ['label' => 'Amenities', 'href' => route('admin.amenities.index'), 'active' => 'admin.amenities.*', 'icon' => '🏊'],
            ['label' => 'Property types', 'href' => route('admin.types.index'), 'active' => 'admin.types.*', 'icon' => '🏘️'],
            ['label' => 'States & cities', 'href' => route('admin.states.index'), 'active' => 'admin.states.*', 'icon' => '📍'],
            ['label' => 'Categories & tags', 'href' => route('admin.categories.index'), 'active' => 'admin.categories.*', 'icon' => '🏷️'],
            ['label' => 'Reports', 'href' => route('admin.reports'), 'active' => 'admin.reports', 'icon' => '📈'],
            ['label' => 'Settings', 'href' => route('admin.settings'), 'active' => 'admin.settings*', 'icon' => '⚙️'],
            ['label' => 'Activity log', 'href' => route('admin.activity-log'), 'active' => 'admin.activity-log*', 'icon' => '🧾'],
        ],
    ];

    $menu = $menu ?? ($menus[$panel] ?? []);
    $title = $title ?? $panel ?? 'Dashboard';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            (function () {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <title>{{ $title }} - {{ config('app.name', 'Real Props') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <div class="flex min-h-screen">
            {{-- Sidebar --}}
            <aside class="hidden w-64 shrink-0 border-e border-gray-200 bg-white lg:flex lg:flex-col dark:border-gray-800 dark:bg-gray-900">
                <div class="flex h-16 items-center gap-2 border-b border-gray-100 px-6 dark:border-gray-800">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-white">RP</span>
                        <span class="text-brand-700 dark:text-brand-400">Real Props</span>
                    </a>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                    @foreach ($menu as $item)
                        <a href="{{ $item['href'] }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                               {{ request()->routeIs($item['active'] ?? '*')
                                   ? 'bg-brand-700 text-white'
                                   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white' }}">
                            @if (! empty($item['icon']))
                                <span class="text-base">{{ $item['icon'] }}</span>
                            @endif
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-gray-100 p-4 dark:border-gray-800">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                        <span class="text-base">←</span> Back to site
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                            <span class="text-base">⎋</span> Log out
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6 lg:px-8 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold lg:hidden">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-white">RP</span>
                        </a>

                        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $title ?? $panel ?? 'Dashboard' }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <livewire:notification-bell />

                        <span class="hidden text-sm text-gray-500 sm:inline dark:text-gray-400">
                            {{ auth()->user()->name }}
                        </span>

                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                            {{ auth()->user()->initials() }}
                        </span>
                    </div>
                </header>

                {{-- Mobile nav --}}
                <nav class="flex gap-1 overflow-x-auto border-b border-gray-200 bg-white px-4 py-2 lg:hidden dark:border-gray-800 dark:bg-gray-900">
                    @foreach ($menu as $item)
                        <a href="{{ $item['href'] }}"
                           class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium
                               {{ request()->routeIs($item['active'] ?? '*')
                                   ? 'bg-brand-700 text-white'
                                   : 'text-gray-600 dark:text-gray-300' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @if (session('status'))
                        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('toast'))
                        <div class="mb-4 rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                            {{ session('toast') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
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
