<x-app-layout>
    {{-- ---- Hero ------------------------------------------------------ --}}
    <section class="relative overflow-hidden rounded-3xl border border-ink-200/60 bg-white/60 px-6 py-16 backdrop-blur-sm sm:px-12 sm:py-20 dark:border-ink-800/60 dark:bg-ink-900/40">
        {{-- decorative glows --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -end-24 -top-24 h-72 w-72 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -start-16 h-72 w-72 rounded-full bg-violet-500/20 blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-3xl text-center">
            <span class="eyebrow animate-fade-up">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                {{ config('app.name') }}
            </span>

            <h1 class="mt-5 animate-fade-up text-4xl font-black tracking-tight text-ink-900 sm:text-5xl dark:text-white" style="animation-delay:.05s">
                {{ __('shop.hero_title') }}
            </h1>

            <p class="mx-auto mt-5 max-w-2xl animate-fade-up text-lg leading-relaxed text-ink-600 dark:text-ink-300" style="animation-delay:.1s">
                {{ __('shop.hero_subtitle') }}
            </p>

            <div class="mt-9 flex animate-fade-up flex-wrap justify-center gap-3" style="animation-delay:.15s">
                <a href="{{ route('services.index') }}" class="btn-primary px-6 py-3 text-base">
                    {{ __('shop.browse_services') }}
                    <svg class="h-4 w-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </a>
                <a href="{{ route('products.index') }}" class="btn-secondary px-6 py-3 text-base">
                    {{ __('shop.browse_products') }}
                </a>
            </div>
        </div>
    </section>

    @if ($services->isNotEmpty())
        <section class="mt-16">
            <div class="mb-6 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('shop.featured_services') }}</h2>
                    <div class="mt-2 h-1 w-12 rounded-full bg-gradient-to-r from-brand-500 to-violet-500"></div>
                </div>
                <a href="{{ route('services.index') }}" class="group inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                    {{ __('shop.browse_services') }}
                    <svg class="h-4 w-4 rtl-flip transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <x-catalog-card :item="$service" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($products->isNotEmpty())
        <section class="mt-16">
            <div class="mb-6 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('shop.featured_products') }}</h2>
                    <div class="mt-2 h-1 w-12 rounded-full bg-gradient-to-r from-brand-500 to-violet-500"></div>
                </div>
                <a href="{{ route('products.index') }}" class="group inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                    {{ __('shop.browse_products') }}
                    <svg class="h-4 w-4 rtl-flip transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-catalog-card :item="$product" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($services->isEmpty() && $products->isEmpty())
        <div class="mt-16 rounded-3xl border border-dashed border-ink-300 py-20 text-center dark:border-ink-700">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-ink-100 text-ink-400 dark:bg-ink-800">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <p class="mt-4 text-ink-500 dark:text-ink-400">{{ __('common.no_results') }}</p>
        </div>
    @endif
</x-app-layout>
