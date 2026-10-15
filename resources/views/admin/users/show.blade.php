<x-panel.layout :panel="'Admin'" :title="'User details'">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                            {{ $user->initials() }}
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <x-badge :badge="match ($user->primaryRole()) {
                                    'admin' => 'red', 'agent' => 'teal', default => 'blue',
                                }" :label="\Illuminate\Support\Str::ucfirst($user->primaryRole())" />
                                @if ($user->isSuspended())
                                    <x-badge badge="red" label="Suspended" />
                                @endif
                                @if ($user->email_verified_at)
                                    <x-badge badge="green" label="Verified e-mail" />
                                @else
                                    <x-badge badge="amber" label="Unverified e-mail" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-100 pt-5 text-sm sm:grid-cols-4 dark:border-gray-800">
                    <div><dt class="text-gray-500">Listings</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $user->properties_count }}</dd></div>
                    <div><dt class="text-gray-500">Inquiries</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $user->received_inquiries_count }}</dd></div>
                    <div><dt class="text-gray-500">Tours</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $user->tour_requests_count }}</dd></div>
                    <div><dt class="text-gray-500">Joined</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $user->created_at->format('M j, Y') }}</dd></div>
                </dl>

                @if ($user->bio)
                    <p class="mt-5 border-t border-gray-100 pt-4 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">{{ $user->bio }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Role &amp; account</h3>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-4 grid gap-4 sm:grid-cols-3">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $user->name)" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            @foreach (\App\Enums\UserRole::cases() as $role)
                                <option value="{{ $role->value }}" @selected(old('role', $user->primaryRole()) === $role->value)>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-3">
                        <x-primary-button>Save changes</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent listings</h3>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @forelse ($properties as $property)
                        <a href="{{ route('admin.properties.show', $property) }}" class="group">
                            <img src="{{ $property->imageUrl() }}" alt="{{ $property->imageAlt() }}"
                                 class="h-28 w-full rounded-lg object-cover transition group-hover:opacity-90">
                            <p class="mt-1 truncate text-xs font-medium text-gray-700 dark:text-gray-300">{{ $property->title }}</p>
                        </a>
                    @empty
                        <p class="col-span-3 py-6 text-center text-sm text-gray-500">No listings yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Account status</h3>

                @if ($user->isSuspended())
                    <p class="mt-2 text-sm text-gray-500">Suspended {{ $user->suspended_at?->diffForHumans() }}.</p>
                    @if ($user->suspension_reason)
                        <p class="mt-1 text-sm text-rose-600">â€œ{{ $user->suspension_reason }}â€</p>
                    @endif
                    <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}" class="mt-3">
                        @csrf
                        <x-primary-button class="w-full justify-center">Reinstate account</x-primary-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="mt-3"
                          onsubmit="event.preventDefault(); var reason = prompt('Suspension reason (optional):'); if (reason === null) return; this.reason.value = reason; this.submit();">
                        @csrf
                        <input type="hidden" name="reason">
                        <button type="submit"
                                class="w-full rounded-md border border-amber-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 hover:bg-amber-50 dark:border-amber-800 dark:text-amber-400 dark:hover:bg-amber-900/30">
                            Suspend account
                        </button>
                    </form>
                @endif
            </div>

            <div class="rounded-2xl border border-rose-100 bg-rose-50 p-5 dark:border-rose-900/50 dark:bg-rose-900/20">
                <h3 class="text-sm font-semibold text-rose-700 dark:text-rose-300">Danger zone</h3>
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">Deleting removes listings, inquiries and favorites.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3"
                      onsubmit="return confirm({{ json_encode('Permanently delete '.$user->name.'?') }})">
                    @csrf
                    @method('DELETE')
                    <x-danger-button class="w-full justify-center">Delete user</x-danger-button>
                </form>
            </div>

            <a href="{{ route('admin.users.index') }}" class="block text-center text-sm font-medium text-gray-500 hover:text-brand-700">
                &larr; Back to users
            </a>
        </aside>
    </div>
</x-panel.layout>
