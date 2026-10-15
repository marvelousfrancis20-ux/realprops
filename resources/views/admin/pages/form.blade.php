@php
    $isEdit = $page->exists;
    $action = $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store');
@endphp

<x-panel.layout :panel="'Admin'" :title="($isEdit ? 'Edit page' : 'New page')">
    <form method="POST" action="{{ $action }}" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <x-input-label for="title" value="Title" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $page->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="mt-5">
                    <x-input-label for="slug" value="Slug" />
                    <x-text-input id="slug" name="slug" class="mt-1 block w-full" :value="old('slug', $page->slug)" placeholder="auto-generated-from-title" />
                    <p class="mt-1 text-xs text-gray-500">Lowercase letters, numbers and dashes only. Leave blank to keep the current one.</p>
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <div class="mt-5">
                    <x-input-label for="content" value="Content (HTML allowed)" />
                    <textarea id="content" name="content" rows="16" required
                              class="mt-1 block w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('content', $page->content) }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>
            </div>
        </div>

        <aside class="space-y-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Visibility</h3>

                <label class="mt-3 flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active ?? true))
                           class="rounded border-gray-300 text-brand-700 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                    Active (visitors can see it)
                </label>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">SEO</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <x-input-label for="meta_title" value="Meta title" />
                        <x-text-input id="meta_title" name="meta_title" class="mt-1 block w-full" :value="old('meta_title', $page->meta_title)" />
                    </div>
                    <div>
                        <x-input-label for="meta_description" value="Meta description" />
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('meta_description', $page->meta_description) }}</textarea>
                        <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>{{ $isEdit ? 'Save changes' : 'Create page' }}</x-primary-button>
                <a href="{{ route('admin.pages.index') }}" class="text-sm font-medium text-gray-500 hover:text-brand-700">Cancel</a>
            </div>
        </aside>
    </form>
</x-panel.layout>
