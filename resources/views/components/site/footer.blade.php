<footer class="border-t border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-4">
            <div>
                <div class="flex items-center gap-2 text-lg font-extrabold">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-white">RP</span>
                    <span class="text-brand-700 dark:text-brand-400">Real Props</span>
                </div>
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                    {{ \App\Models\Setting::get('site_tagline', 'Find a place you will love') }}.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Explore</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-brand-700" href="{{ route('properties.index') }}">Properties for sale</a></li>
                    <li><a class="hover:text-brand-700" href="{{ route('properties.index', ['listing_type' => 'rent']) }}">Properties for rent</a></li>
                    <li><a class="hover:text-brand-700" href="{{ route('agents.index') }}">Agent directory</a></li>
                    <li><a class="hover:text-brand-700" href="{{ route('blog.index') }}">Blog</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Company</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                    <li><a class="hover:text-brand-700" href="{{ route('about') }}">About us</a></li>
                    <li><a class="hover:text-brand-700" href="{{ route('contact') }}">Contact</a></li>
                    <li><a class="hover:text-brand-700" href="{{ route('faq') }}">FAQ</a></li>
                    <li><a class="hover:text-brand-700" href="{{ route('terms') }}">Terms</a></li>
                    <li><a class="hover:text-brand-700" href="{{ route('privacy') }}">Privacy</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Get in touch</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                    <li>{{ \App\Models\Setting::get('site_email', 'hello@realprops.test') }}</li>
                    <li>{{ \App\Models\Setting::get('site_phone', '+234 913 073 2722') }}</li>
                    <li>{{ \App\Models\Setting::get('site_address', 'Lagos, Nigeria') }}</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-gray-100 pt-6 text-sm text-gray-400 sm:flex-row dark:border-gray-800">
            <p>&copy; {{ date('Y') }} Real Props. All rights reserved.</p>
            <p>Made with Laravel {{ app()->version() }}</p>
        </div>
    </div>
</footer>
