<div x-data="{ open: false }" class="relative" x-on:click.outside="open = false">
    <button type="button" x-on:click="open = !open"
            class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-transition x-cloak
         class="absolute right-0 z-50 mt-2 w-80 rounded-xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-center justify-between px-2 py-1.5">
            <p class="text-sm font-semibold">Notifications</p>

            @if ($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs font-medium text-brand-700 hover:underline dark:text-brand-400">
                    Mark all read
                </button>
            @endif
        </div>

        @forelse ($notifications as $notification)
            <a href="{{ $notification->data['url'] ?? route('dashboard') }}"
               class="block rounded-lg px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                <p class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $notification->data['title'] ?? 'Notification' }}
                </p>
                <p class="mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ $notification->data['body'] ?? '' }}
                </p>
                <p class="mt-1 text-[11px] text-gray-400">
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </a>
        @empty
            <p class="px-3 py-6 text-center text-sm text-gray-500">No notifications yet.</p>
        @endforelse
    </div>
</div>
