<button type="button" wire:click="toggle"
        class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold transition
            {{ $inCompare
                ? 'border-brand-700 bg-brand-700 text-white'
                : 'border-gray-300 text-gray-600 hover:border-brand-600 hover:text-brand-700 dark:border-gray-700 dark:text-gray-300' }}">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
    </svg>
    {{ $inCompare ? 'In compare' : 'Compare' }}
</button>
