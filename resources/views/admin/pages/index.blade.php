<x-panel.layout :panel="'Admin'" :title="'Pages'">
    <div class="mb-5 flex items-center justify-between gap-3">
        <p class="text-sm text-gray-500">Static pages such as Terms, Privacy and custom landing pages.</p>
        <a href="{{ route('admin.pages.create') }}" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">+ New page</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Updated</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($pages as $page)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $page->title }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">/{{ $page->slug }}</td>
                        <td class="px-4 py-3">
                            <x-badge :badge="$page->is_active ? 'green' : 'gray'" :label="$page->is_active ? 'Active' : 'Hidden'" />
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $page->updated_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('pages.show', $page) }}" target="_blank" rel="noopener" class="text-gray-500 hover:underline">View</a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-brand-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                      onsubmit="return confirm({{ json_encode('Delete page "'.$page->title.'"?') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">No pages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $pages->links() }}
    </div>
</x-panel.layout>
