<x-site.layout :title="'Contact us - Real Props'">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Contact']]" />

        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Contact us</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Questions about a listing, your account or anything else? Send us a message.
        </p>

        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="contact-name" value="Name *" />
                        <x-text-input id="contact-name" name="name" :value="old('name', auth()->user()?->name)" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="contact-email" value="E-mail *" />
                        <x-text-input id="contact-email" type="email" name="email" :value="old('email', auth()->user()?->email)" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="contact-phone" value="Phone" />
                        <x-text-input id="contact-phone" name="phone" :value="old('phone')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="contact-subject" value="Subject *" />
                        <x-text-input id="contact-subject" name="subject" :value="old('subject')" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4">
                    <x-input-label for="contact-message" value="Message *" />
                    <textarea id="contact-message" name="message" rows="5" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 dark:border-gray-700 dark:bg-gray-950">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-1" />
                </div>

                @if (config('services.recaptcha.enabled'))
                    <div class="mt-4">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site') }}"></div>
                        <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-1" />
                    </div>
                @endif

                <x-primary-button class="mt-6">Send message</x-primary-button>
            </form>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 p-4 text-sm dark:border-gray-800">
                <p class="font-semibold text-gray-900 dark:text-gray-100">E-mail</p>
                <p class="mt-1 text-gray-500">{{ \App\Models\Setting::get('site_email', 'hello@realprops.test') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 text-sm dark:border-gray-800">
                <p class="font-semibold text-gray-900 dark:text-gray-100">Phone</p>
                <p class="mt-1 text-gray-500">{{ \App\Models\Setting::get('site_phone', '+234 913 073 2722') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 text-sm dark:border-gray-800">
                <p class="font-semibold text-gray-900 dark:text-gray-100">Office</p>
                <p class="mt-1 text-gray-500">{{ \App\Models\Setting::get('site_address', 'Lagos, Nigeria') }}</p>
            </div>
        </div>
    </div>
</x-site.layout>
