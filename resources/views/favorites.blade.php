<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">My saved homes</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($favorites as $favorite)
                    @if ($favorite->property)
                        <x-property-card :property="$favorite->property" />
                    @endif
                @empty
                    <p class="col-span-full rounded-xl border border-dashed border-gray-300 p-12 text-center text-sm text-gray-500 dark:border-gray-700">
                        You have not saved any homes yet.
                    </p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
