<x-site.layout>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-900 text-white">
        <div class="absolute inset-0 bg-cover bg-center opacity-15" style="background-image: url('{{ \App\Support\PhotoPool::HERO }}')"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="max-w-2xl">
                <p class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                    {{ \App\Models\Setting::get('site_tagline', 'Find a place you will love') }}
                </p>

                <h1 class="mt-4 text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    Discover your next
                    <span class="text-accent-400">dream home</span>
                </h1>

                <p class="mt-4 text-lg text-brand-100">
                    Browse {{ number_format($stats['listings']) }} verified listings for sale and rent,
                    from {{ number_format($stats['agents']) }} trusted agents across Nigeria.
                </p>

                <form action="{{ route('properties.index') }}" method="GET"
                      class="mt-8 rounded-2xl bg-white p-3 shadow-xl dark:bg-gray-900">
                    <div class="grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto]">
                        <select name="keyword" class="rounded-xl border-0 text-sm text-gray-900 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Any keyword</option>
                        </select>

                        <select name="listing_type" class="rounded-xl border-0 text-sm text-gray-900 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Buy or rent</option>
                            <option value="sale">Buy</option>
                            <option value="rent">Rent</option>
                        </select>

                        <select name="city" class="rounded-xl border-0 text-sm text-gray-900 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Any city</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->name }}">{{ $city->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit"
                                class="rounded-xl bg-brand-700 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-800">
                            Search
                        </button>
                    </div>
                </form>

                <div class="mt-6 flex flex-wrap gap-x-8 gap-y-2 text-sm text-brand-100">
                    <span><strong class="text-white">{{ number_format($stats['listings']) }}</strong> listings</span>
                    <span><strong class="text-white">{{ number_format($stats['agents']) }}</strong> agents</span>
                    <span><strong class="text-white">{{ number_format($stats['cities']) }}</strong> cities</span>
                    <span><strong class="text-white">{{ number_format($stats['viewed']) }}</strong> views</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Property types --}}
    @if ($types->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Browse by property type</h2>

            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($types as $type)
                    <a href="{{ route('properties.index', ['property_type' => $type->id]) }}"
                       class="group rounded-2xl border border-gray-200 bg-white p-5 text-center transition hover:border-brand-600 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                        <span class="text-3xl">{{ $type->icon ?: 'ðŸ ' }}</span>
                        <p class="mt-2 text-sm font-semibold text-gray-900 group-hover:text-brand-700 dark:text-gray-100">
                            {{ $type->name }}
                        </p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ number_format($type->properties_count) }} listings</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Featured --}}
    @if ($featured->isNotEmpty())
        <section class="bg-white py-16 dark:bg-gray-900/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Featured properties</h2>
                    <a href="{{ route('properties.index', ['sort' => 'newest']) }}"
                       class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400">
                        View all
                    </a>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featured as $property)
                        <x-property-card :property="$property" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Popular --}}
    @if ($popular->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Most viewed this week</h2>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($popular as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA for agents --}}
    <section class="bg-brand-800 text-white">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-4 py-14 sm:px-6 lg:flex-row lg:items-center lg:px-8">
            <div>
                <h2 class="text-2xl font-bold">Are you an agent?</h2>
                <p class="mt-2 max-w-xl text-brand-100">
                    Join Real Props, publish your listings and reach thousands of serious buyers and renters.
                </p>
            </div>

            @auth
                <a href="{{ auth()->user()->isAgent() ? route('agent.properties.create') : route('profile.edit') }}"
                   class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-brand-800 hover:bg-brand-50">
                    Start listing
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-brand-800 hover:bg-brand-50">
                    Create an agent account
                </a>
            @endauth
        </div>
    </section>

    {{-- Latest listings --}}
    @if ($latest->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Fresh on the market</h2>
                <a href="{{ route('properties.index') }}" class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400">
                    Browse all
                </a>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($latest as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Blog --}}
    @if ($posts->isNotEmpty())
        <section class="bg-white py-16 dark:bg-gray-900/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">From the blog</h2>
                    <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400">
                        All articles
                    </a>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post) }}"
                           class="group rounded-2xl border border-gray-200 bg-white p-5 transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                            @if ($post->category)
                                <span class="text-xs font-semibold uppercase tracking-wide text-brand-700 dark:text-brand-400">
                                    {{ $post->category->name }}
                                </span>
                            @endif

                            <h3 class="mt-2 line-clamp-2 text-base font-semibold text-gray-900 group-hover:text-brand-700 dark:text-gray-100">
                                {{ $post->title }}
                            </h3>

                            <p class="mt-2 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ $post->excerptText() }}
                            </p>

                            <p class="mt-3 text-xs text-gray-400">
                                {{ $post->published_at?->diffForHumans() }} Â· {{ $post->readingTime() }} min read
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-site.layout>
