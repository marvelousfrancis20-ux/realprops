<x-panel.layout :panel="'Admin'" :title="'Settings'">
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        @foreach ($groups as $group => $fields)
            <section class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $group }}</h2>

                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    @foreach ($fields as $key => $field)
                        @php
                            $current = old($key, $values[$key] ?? '');
                        @endphp

                        @if ($field['type'] === 'textarea')
                            <div class="sm:col-span-2">
                                <x-input-label :for="$key" :value="$field['label']" />
                                <textarea id="{{ $key }}" name="{{ $key }}" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ $current }}</textarea>
                            </div>
                        @elseif ($field['type'] === 'boolean')
                            <label class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <input type="hidden" name="{{ $key }}" value="">
                                <input type="checkbox" name="{{ $key }}" value="1"
                                       @checked(old($key, $values[$key] ?? false))
                                       class="rounded border-gray-300 text-brand-700 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                                {{ $field['label'] }}
                            </label>
                        @else
                            <div>
                                <x-input-label :for="$key" :value="$field['label']" />
                                <x-text-input :id="$key" :name="$key" class="mt-1 block w-full" :value="$current" />
                            </div>
                        @endif

                        <x-input-error :messages="$errors->get($key)" class="sm:col-span-2" />
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="flex items-center gap-3">
            <x-primary-button>Save settings</x-primary-button>
            <span class="text-xs text-gray-500">Changes take effect immediately (settings are cached).</span>
        </div>
    </form>
</x-panel.layout>
