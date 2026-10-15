@php
    $isEdit = $post->exists;
    $action = $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store');
@endphp

<x-panel.layout :panel="'Admin'" :title="($isEdit ? 'Edit post' : 'New post')">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <x-input-label for="title" value="Title" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $post->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="mt-5">
                    <x-input-label for="excerpt" value="Excerpt" />
                    <textarea id="excerpt" name="excerpt" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('excerpt', $post->excerpt) }}</textarea>
                    <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                </div>

                <div class="mt-5">
                    <x-input-label for="content" value="Content (HTML allowed)" />
                    <textarea id="content" name="content" rows="16" required
                              class="mt-1 block w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('content', $post->content) }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">SEO</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <x-input-label for="meta_title" value="Meta title" />
                        <x-text-input id="meta_title" name="meta_title" class="mt-1 block w-full" :value="old('meta_title', $post->meta_title)" />
                    </div>
                    <div>
                        <x-input-label for="meta_description" value="Meta description" />
                        <textarea id="meta_description" name="meta_description" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('meta_description', $post->meta_description) }}</textarea>
                    </div>
                    <x-input-error :messages="$errors->get('meta_description')" />
                </div>
            </div>
        </div>

        <aside class="space-y-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Publish</h3>

                <div class="mt-3">
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (\App\Enums\PostStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $post->status?->value ?? 'draft') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="published_at" value="Publish date" />
                    <input id="published_at" name="published_at" type="datetime-local"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Category</h3>
                <select id="category_id" name="category_id"
                        class="mt-3 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">— No category —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $post->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Featured image</h3>
                <img src="{{ $post->coverUrl() }}" alt="" class="mt-3 h-32 w-full rounded-lg object-cover bg-gray-100">
                <input id="featured_image" name="featured_image" type="file" accept="image/*"
                       class="mt-3 block w-full text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-400">
                <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tags</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($tags as $tag)
                        <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   @checked(in_array($tag->id, array_map('intval', (array) old('tags', $selectedTags->all())), true))
                                   class="rounded border-gray-300 text-brand-700 focus:ring-brand-500">
                            {{ $tag->name }}
                        </label>
                    @empty
                        <p class="text-xs text-gray-500">No tags created yet.</p>
                    @endforelse
                </div>
                <x-input-error :messages="$errors->get('tags')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>{{ $isEdit ? 'Save changes' : 'Create post' }}</x-primary-button>
                <a href="{{ route('admin.posts.index') }}" class="text-sm font-medium text-gray-500 hover:text-brand-700">Cancel</a>
            </div>
        </aside>
    </form>
</x-panel.layout>
