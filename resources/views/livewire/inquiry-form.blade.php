<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
        {{ $isTour ? 'Book a tour' : 'Send an enquiry' }}
    </h3>

    @if ($success)
        <div class="mt-3 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
            {{ $success }}
        </div>
    @endif

    <form wire:submit="submit" class="mt-4 space-y-3">
        <div>
            <label for="inq-name" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Full name *</label>
            <input id="inq-name" type="text" wire:model="name"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
            @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
                <label for="inq-email" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">E-mail *</label>
                <input id="inq-email" type="email" wire:model="email"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="inq-phone" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Phone</label>
                <input id="inq-phone" type="text" wire:model="phone"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        @if ($isTour)
            <div>
                <label for="inq-date" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Preferred date *</label>
                <input id="inq-date" type="date" wire:model="preferredTourDate"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">
                @error('preferredTourDate') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label for="inq-message" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                {{ $isTour ? 'Message (optional)' : 'Message *' }}
            </label>
            <textarea id="inq-message" rows="4" wire:model="message"
                      placeholder="I am interested in this property..."
                      class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950"></textarea>
            @error('message') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 disabled:opacity-60"
                wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="submit">
                {{ $isTour ? 'Request tour' : 'Send enquiry' }}
            </span>
            <span wire:loading wire:target="submit">Sending...</span>
        </button>
    </form>
</div>
