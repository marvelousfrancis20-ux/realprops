@php
    $navLinks = [
        route('properties.index') => 'Buy',
        route('properties.index', ['listing_type' => 'rent']) => 'Rent',
        route('agents.index') => 'Agents',
        route('blog.index') => 'Blog',
        route('about') => 'About',
        route('contact') => 'Contact',
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-950/90">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold tracking-tight">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-700 text-white">RP</span>
                <span class="text-brand-700 dark:text-brand-400">Real Props</span>
            </a>

            <nav class="hidden items-center gap-6 lg:flex">
                @foreach ($navLinks as $href => $label)
                    <a href="{{ $href }}"
                       class="text-sm font-medium {{ request()->url() === $href ? 'text-brand-700 dark:text-brand-400' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" x-data x-on:click="$dispatch('toggle-theme')"
                    class="hidden h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900"
                    title="Toggle dark mode">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                </svg>
            </button>

            @auth
                <livewire:notification-bell class="hidden sm:block" />

                <a href="{{ auth()->user()->dashboardUrl() }}"
                   class="hidden rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 sm:inline-flex">
                    Dashboard
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">
                            {{ auth()->user()->name }}
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('dashboard')">{{ __('Dashboard') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('favorites.index')">{{ __('Saved homes') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>

                        @if (auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.dashboard')">{{ __('Admin panel') }}</x-dropdown-link>
                        @elseif (auth()->user()->isAgent())
                            <x-dropdown-link :href="route('agent.dashboard')">{{ __('Agent panel') }}</x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                    Sign up
                </a>
            @endauth

            <button type="button" x-data="{ open: false }" x-on:click="open = !open"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 lg:hidden dark:border-gray-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
    </div>

    <div class="border-t border-gray-100 lg:hidden dark:border-gray-800">
        <nav class="flex flex-wrap gap-4 px-4 py-3">
            @foreach ($navLinks as $href => $label)
                <a href="{{ $href }}" class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $label }}</a>
            @endforeach
        </nav>
    </div>
</header>
