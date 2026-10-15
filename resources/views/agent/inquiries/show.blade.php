<x-panel.layout :panel="'Agent'" :title="'Inquiry #' . $inquiry->id">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $inquiry->name }}</h2>
                        <p class="mt-0.5 text-sm text-gray-500">
                            {{ $inquiry->email }}
                            @if ($inquiry->phone)
                                &middot; {{ $inquiry->phone }}
                            @endif
                        </p>
                    </div>
                    @php
                        $statusBadge = match ($inquiry->status->value) {
                            'new' => 'amber', 'read' => 'blue', 'replied' => 'green', default => 'gray',
                        };
                    @endphp
                    <x-badge :badge="$statusBadge" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                </div>

                <div class="mt-5 rounded-xl bg-gray-50 p-4 text-sm leading-relaxed text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $inquiry->message }}
                </div>

                @if ($inquiry->preferred_tour_date)
                    <p class="mt-4 text-sm text-gray-500">
                        Preferred tour date:
                        <strong class="text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Carbon::parse($inquiry->preferred_tour_date)->format('l, F j, Y') }}</strong>
                    </p>
                @endif

                @if ($inquiry->reply_message)
                    <div class="mt-6 rounded-xl border border-brand-100 bg-brand-50 p-4 dark:border-brand-900/40 dark:bg-brand-900/20">
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-700 dark:text-brand-400">Your reply</p>
                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $inquiry->reply_message }}</p>
                        <p class="mt-2 text-xs text-gray-500">Sent {{ $inquiry->replied_at?->diffForHumans() }}</p>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('agent.inquiries.reply', $inquiry) }}"
                  class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                @csrf
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Reply to {{ $inquiry->name }}</h3>

                <div class="mt-3">
                    <x-input-label for="reply_message" value="Message" />
                    <textarea id="reply_message" name="reply_message" rows="5" required
                              class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                              placeholder="Write your reply...">{{ old('reply_message') }}</textarea>
                    <x-input-error :messages="$errors->get('reply_message')" class="mt-2" />
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <x-primary-button>Send reply</x-primary-button>
                    <span class="text-xs text-gray-500">The buyer receives an e-mail and an in-app notification.</span>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Listing</h3>
                @if ($inquiry->property)
                    <a href="{{ route('properties.show', $inquiry->property) }}" class="mt-3 flex gap-3">
                        <img src="{{ $inquiry->property->imageUrl() }}" alt="{{ $inquiry->property->imageAlt() }}"
                             class="h-16 w-20 rounded-lg object-cover">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $inquiry->property->title }}</p>
                            <p class="text-sm font-semibold text-brand-700">{{ $inquiry->property->formattedPrice() }}</p>
                        </div>
                    </a>
                @else
                    <p class="mt-2 text-sm text-gray-500">This listing has been removed.</p>
                @endif
            </div>

            <form method="POST" action="{{ route('agent.inquiries.update', $inquiry) }}">
                @csrf
                @method('PATCH')
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Status</h3>

                    <select name="status"
                            class="mt-3 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (['new', 'read', 'replied', 'archived'] as $status)
                            <option value="{{ $status }}" @selected($inquiry->status->value === $status)>
                                {{ \Illuminate\Support\Str::ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

                    <x-primary-button class="mt-3 w-full justify-center">Update status</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('agent.inquiries.destroy', $inquiry) }}"
                  onsubmit="return confirm('Delete this inquiry permanently?')">
                @csrf
                @method('DELETE')
                <x-danger-button class="w-full justify-center">Delete inquiry</x-danger-button>
            </form>

            <a href="{{ route('agent.inquiries.index') }}" class="block text-center text-sm font-medium text-gray-500 hover:text-brand-700">
                &larr; Back to inbox
            </a>
        </aside>
    </div>
</x-panel.layout>
