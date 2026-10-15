<button type="button"
        wire:click="toggle"
        wire:key="fav-{{ $property->id }}"
        title="{{ $favorited ? 'Remove from saved' : 'Save this home' }}"
        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-gray-500 shadow-sm backdrop-blur transition hover:text-rose-600 dark:bg-gray-900/80 dark:text-gray-300">
    <svg class="h-5 w-5 {{ $favorited ? 'text-rose-600' : '' }}" fill="{{ $favorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
    </svg>
</button>
