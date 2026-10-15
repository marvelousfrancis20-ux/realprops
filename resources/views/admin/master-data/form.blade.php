@php
    $isEdit = $row->exists;
    $action = $isEdit
        ? route('admin.'.$resource.'.update', $row->id)
        : route('admin.'.$resource.'.store');
@endphp

<x-panel.layout :panel="'Admin'" :title="($isEdit ? 'Edit ' : 'New ').$config['label']">
    <div class="max-w-2xl">
        <form method="POST" action="{{ $action }}" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="space-y-5">
                @foreach ($config['fields'] as $key => $field)
                    @php
                        $value = old($key, $row->getAttribute($key));
                    @endphp

                    @if ($field['type'] === 'checkbox')
                        <label class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input type="checkbox" name="{{ $key }}" value="1"
                                   @checked(old($key, $row->getAttribute($key) ?? ($field['default'] ?? false)))
                                   class="rounded border-gray-300 text-brand-700 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                            {{ $field['label'] }}
                        </label>
                    @elseif ($field['type'] === 'select')
                        <div>
                            <x-input-label :for="$key" :value="$field['label']" />
                            <select id="{{ $key }}" name="{{ $key }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">— Select —</option>
                                @foreach ($options[$key] ?? collect() as $optionId => $optionLabel)
                                    <option value="{{ $optionId }}" @selected((string) $value === (string) $optionId)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif ($field['type'] === 'number')
                        <div>
                            <x-input-label :for="$key" :value="$field['label']" />
                            <x-text-input :id="$key" :name="$key" type="number" class="mt-1 block w-full" :value="$value" />
                        </div>
                    @else
                        <div>
                            <x-input-label :for="$key" :value="$field['label']" />
                            <x-text-input :id="$key" :name="$key" class="mt-1 block w-full" :value="$value" />
                        </div>
                    @endif

                    <x-input-error :messages="$errors->get($key)" />
                @endforeach
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-primary-button>{{ $isEdit ? 'Save changes' : 'Create' }}</x-primary-button>
                <a href="{{ route('admin.'.$resource.'.index') }}" class="text-sm font-medium text-gray-500 hover:text-brand-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-panel.layout>
