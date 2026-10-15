<x-panel.layout :panel="'Admin'" :title="'Message from ' . $message->name">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <article class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 pb-4 dark:border-gray-800">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $message->subject ?: '(no subject)' }}</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            From <span class="font-medium text-gray-700 dark:text-gray-300">{{ $message->name }}</span>
                            &lt;{{ $message->email }}&gt;
                        </p>
                    </div>
                    <x-badge :badge="$message->status->value === 'new' ? 'blue' : ($message->status->value === 'replied' ? 'green' : 'gray')" :label="$message->status->label()" />
                </div>

                <div class="prose prose-sm dark:prose-invert mt-5 max-w-none text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($message->message)) !!}
                </div>

                <p class="mt-6 text-xs text-gray-500">Received {{ $message->created_at->format('M j, Y H:i:s') }}</p>
            </article>
        </div>

        <aside class="space-y-5">
            <form method="POST" action="{{ route('admin.contact-messages.update', $message) }}"
                  class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                @csrf
                @method('PATCH')

                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Update message</h3>

                <div class="mt-4">
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (['new', 'read', 'replied', 'closed'] as $value)
                            <option value="{{ $value }}" @selected(old('status', $message->status->value) === $value)>{{ \Illuminate\Support\Str::ucfirst($value) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="admin_note" value="Internal note" />
                    <textarea id="admin_note" name="admin_note" rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('admin_note', $message->admin_note) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Only visible to admins.</p>
                    <x-input-error :messages="$errors->get('admin_note')" class="mt-2" />
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <x-primary-button>Save</x-primary-button>
                    <a href="{{ route('admin.contact-messages.index') }}" class="text-sm font-medium text-gray-500 hover:text-brand-700">Back</a>
                </div>
            </form>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Contact</h3>
                <dl class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <div><dt class="inline font-medium">Email:</dt>
                        <dd class="inline"><a href="mailto:{{ $message->email }}?subject=Re: {{ rawurlencode($message->subject ?: 'Your message') }}" class="text-brand-700 hover:underline">{{ $message->email }}</a></dd>
                    </div>
                    @if ($message->phone)
                        <div><dt class="inline font-medium">Phone:</dt> <dd class="inline">{{ $message->phone }}</dd></div>
                    @endif
                    <div><dt class="inline font-medium">Received:</dt> <dd class="inline">{{ $message->created_at->format('M j, Y') }}</dd></div>
                </dl>

                <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" class="mt-5"
                      onsubmit="return confirm({{ json_encode('Delete this message permanently?') }})">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Delete message</button>
                </form>
            </div>
        </aside>
    </div>
</x-panel.layout>
