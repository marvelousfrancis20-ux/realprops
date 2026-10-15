<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">My inquiries</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Every enquiry you have sent to agents, and their replies.
        </p>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($inquiries as $inquiry)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            @if ($inquiry->property)
                                <a href="{{ route('properties.show', $inquiry->property) }}" class="text-sm font-semibold text-gray-900 hover:text-brand-700 dark:text-gray-100">
                                    {{ $inquiry->property->title }}
                                </a>
                            @else
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Deleted listing</p>
                            @endif

                            <p class="mt-0.5 text-xs text-gray-500">
                                Sent {{ $inquiry->created_at->diffForHumans() }}
                                @if ($inquiry->agent)
                                    · to {{ $inquiry->agent->name }}
                                @endif
                            </p>
                        </div>

                        <x-badge :badge="match ($inquiry->status->value) {
                            'new' => 'amber',
                            'read' => 'blue',
                            'replied' => 'green',
                            default => 'gray',
                        }" :label="\Illuminate\Support\Str::ucfirst($inquiry->status->value)" />
                    </div>

                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                        {{ $inquiry->message }}
                    </p>

                    @if ($inquiry->reply_message)
                        <div class="mt-4 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/60">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Agent reply</p>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">{{ $inquiry->reply_message }}</p>
                            <p class="mt-2 text-xs text-gray-400">{{ $inquiry->replied_at?->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-gray-300 p-12 text-center text-sm text-gray-500 dark:border-gray-700">
                    You have not sent any inquiries yet.
                </p>
            @endforelse
        </div>

        <div class="mx-auto mt-8 max-w-4xl sm:px-6 lg:px-8">
            {{ $inquiries->links() }}
        </div>
    </div>
</x-app-layout>
