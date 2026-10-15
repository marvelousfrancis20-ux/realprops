<x-panel.layout :panel="'Admin'" :title="'Users'">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            @foreach (['all' => 'Everyone', 'user' => 'Members', 'agent' => 'Agents', 'admin' => 'Admins'] as $key => $label)
                <a href="{{ $key === 'all' ? route('admin.users.index') : route('admin.users.index', ['role' => $key]) }}"
                   class="rounded-full px-3 py-1.5 font-medium {{ ($activeRole ?? null) === $key || ($key === 'all' && ! $activeRole)
                       ? 'bg-brand-700 text-white'
                       : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs opacity-70">{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
            <span class="rounded-full bg-rose-50 px-3 py-1.5 font-medium text-rose-600 ring-1 ring-rose-200 dark:bg-rose-900/30 dark:text-rose-300">
                Suspended <span class="ml-1 text-xs opacity-70">{{ $counts['suspended'] }}</span>
            </span>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or e-mail"
                   class="w-56 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            <x-secondary-button>Search</x-secondary-button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Listings</th>
                    <th class="px-4 py-3">Joined</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                                    {{ $user->initials() }}
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
                                    <span class="block truncate text-xs text-gray-500">{{ $user->email }}</span>
                                </span>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :badge="match ($user->primaryRole()) {
                                'admin' => 'red', 'agent' => 'teal', default => 'blue',
                            }" :label="\Illuminate\Support\Str::ucfirst($user->primaryRole())" />
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $user->properties_count }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            @if ($user->isSuspended())
                                <x-badge badge="red" label="Suspended" />
                            @elseif (! $user->email_verified_at)
                                <x-badge badge="amber" label="Unverified" />
                            @else
                                <x-badge badge="green" label="Active" />
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-brand-700 hover:underline">Manage</a>

                                @if ($user->isSuspended())
                                    <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:underline">Reinstate</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}"
                                          onsubmit="event.preventDefault(); var reason = prompt('Suspension reason (optional):'); if (reason === null) return; this.reason.value = reason; this.submit();">
                                        @csrf
                                        <input type="hidden" name="reason">
                                        <button type="submit" class="text-amber-600 hover:underline">Suspend</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm({{ json_encode('Delete '.$user->name.' and all of their data?') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $users->links() }}
    </div>
</x-panel.layout>
