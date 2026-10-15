<x-panel.layout :panel="'Agent'" :title="'Agency profile'">
    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('agent.profile.update') }}"
              enctype="multipart/form-data"
              class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            @csrf
            @method('PATCH')

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="agency_name" value="Agency name" />
                    <x-text-input id="agency_name" name="agency_name" class="mt-1 block w-full"
                                  :value="old('agency_name', $profile->agency_name)" />
                    <x-input-error :messages="$errors->get('agency_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="license_number" value="License number" />
                    <x-text-input id="license_number" name="license_number" class="mt-1 block w-full"
                                  :value="old('license_number', $profile->license_number)" />
                    <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="years_experience" value="Years of experience" />
                    <x-text-input id="years_experience" name="years_experience" type="number" min="0" max="60"
                                  class="mt-1 block w-full" :value="old('years_experience', $profile->years_experience)" />
                    <x-input-error :messages="$errors->get('years_experience')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="whatsapp" value="WhatsApp number" />
                    <x-text-input id="whatsapp" name="whatsapp" class="mt-1 block w-full"
                                  :value="old('whatsapp', $profile->whatsapp ?? auth()->user()->whatsapp)" />
                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                </div>
            </div>

            <div class="mt-5">
                <x-input-label for="bio" value="Bio" />
                <textarea id="bio" name="bio" rows="5"
                          class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('bio', $profile->bio) }}</textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-2" />
            </div>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="id_document" value="Government ID (jpg, png or pdf)" />
                    <input id="id_document" name="id_document" type="file" accept=".jpg,.jpeg,.png,.pdf"
                           class="mt-1 block w-full text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-400">
                    <x-input-error :messages="$errors->get('id_document')" class="mt-2" />
                    @if ($profile->id_document_path)
                        <a href="{{ $profile->id_document_path }}" target="_blank" rel="noopener"
                           class="mt-1 inline-block text-xs font-medium text-brand-700 hover:underline">View current ID document</a>
                    @endif
                </div>

                <div>
                    <x-input-label for="license_document" value="License document (jpg, png or pdf)" />
                    <input id="license_document" name="license_document" type="file" accept=".jpg,.jpeg,.png,.pdf"
                           class="mt-1 block w-full text-sm text-gray-600 file:me-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-400">
                    <x-input-error :messages="$errors->get('license_document')" class="mt-2" />
                    @if ($profile->license_document_path)
                        <a href="{{ $profile->license_document_path }}" target="_blank" rel="noopener"
                           class="mt-1 inline-block text-xs font-medium text-brand-700 hover:underline">View current license document</a>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-primary-button>Save profile</x-primary-button>
                <span class="text-xs text-gray-500">Documents are stored privately and only reviewed by admins.</span>
            </div>
        </form>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Verification</h3>
                <div class="mt-3">
                    @if ($profile->isVerified())
                        <x-badge badge="green" label="Verified agent" />
                        <p class="mt-2 text-xs text-gray-500">Verified on {{ $profile->verified_at->format('M j, Y') }}.</p>
                    @else
                        <x-badge badge="amber" label="Pending verification" />
                        <p class="mt-2 text-xs text-gray-500">Upload your ID and license, then an admin will review them.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Public profile</h3>
                <p class="mt-2 text-sm text-gray-500">See how buyers view your listings.</p>
                <a href="{{ route('agents.show', auth()->user()) }}"
                   class="mt-3 inline-block rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                    View public profile
                </a>
            </div>
        </aside>
    </div>
</x-panel.layout>
