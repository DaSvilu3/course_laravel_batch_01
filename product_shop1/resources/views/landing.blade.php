<x-app-layout>
    <x-slot name="title">{{ config('app.name') }}</x-slot>

    {{-- ================================================================= --}}
    {{-- 1. HERO                                                           --}}
    {{-- ================================================================= --}}
    <section id="top" class="relative overflow-hidden rounded-3xl border border-ink-200/60 bg-white/60 px-6 py-14 backdrop-blur-sm sm:px-12 sm:py-20 dark:border-ink-800/60 dark:bg-ink-900/40">
        {{-- decorative glow blobs --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -end-24 -top-24 h-80 w-80 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -start-20 h-80 w-80 rounded-full bg-violet-500/20 blur-3xl"></div>
        </div>

        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            {{-- ---- Copy ---- --}}
            <div class="text-center lg:text-start">
                <span class="eyebrow animate-fade-up">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                    {{ __('landing.hero_badge') }}
                </span>

                <h1 class="mt-6 animate-fade-up text-4xl font-black leading-tight tracking-tight text-ink-900 sm:text-5xl xl:text-6xl dark:text-white" style="animation-delay:.05s">
                    {{ __('landing.hero_title') }}
                    <span class="mt-1 block text-gradient">{{ __('landing.hero_highlight') }}</span>
                </h1>

                <p class="mx-auto mt-6 max-w-xl animate-fade-up text-lg leading-relaxed text-ink-600 lg:mx-0 dark:text-ink-300" style="animation-delay:.1s">
                    {{ __('landing.hero_subtitle') }}
                </p>

                <div class="mt-9 flex animate-fade-up flex-wrap justify-center gap-3 lg:justify-start" style="animation-delay:.15s">
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-3 text-base">
                        {{ __('landing.hero_cta') }}
                        <svg class="h-4 w-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </a>
                    <a href="#pricing" class="btn-secondary px-6 py-3 text-base">
                        {{ __('landing.hero_cta_secondary') }}
                    </a>
                </div>

                <p class="mt-5 flex animate-fade-up items-center justify-center gap-2 text-sm text-ink-500 lg:justify-start dark:text-ink-400" style="animation-delay:.2s">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
                    {{ __('landing.hero_note') }}
                </p>
            </div>

            {{-- ---- Product photo + floating cards ---- --}}
            <div class="relative animate-fade-up" style="animation-delay:.15s">
                <div aria-hidden="true" class="absolute inset-0 -z-10 translate-x-4 translate-y-6 rounded-[2rem] bg-gradient-to-br from-brand-500/30 to-violet-500/30 blur-2xl rtl:-translate-x-4"></div>

                <div class="relative overflow-hidden rounded-[1.75rem] border border-white/60 bg-white shadow-lift ring-1 ring-ink-900/5 dark:border-ink-800 dark:ring-white/10">
                    <img src="{{ asset('landing_page_shop.jpeg') }}" alt="{{ __('landing.hero_highlight') }}"
                         class="aspect-[4/3] w-full object-cover" loading="eager" width="1000" height="664">
                    <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink-950/20 to-transparent"></div>
                </div>

                {{-- floating: new order toast --}}
                <div class="absolute top-4 start-4 flex items-center gap-2.5 rounded-2xl border border-ink-200/70 bg-white/95 px-3.5 py-2.5 shadow-lift backdrop-blur dark:border-ink-700 dark:bg-ink-900/95">
                    <span class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m-3 0h13.5l-.75 9a1.5 1.5 0 0 1-1.5 1.35H7.5a1.5 1.5 0 0 1-1.5-1.35l-.75-9Z" /></svg>
                        <span class="absolute -end-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-ink-900"></span>
                    </span>
                    <div class="text-start">
                        <p class="text-sm font-bold text-ink-900 dark:text-white">{{ __('landing.hero_toast_title') }}</p>
                        <p class="text-xs text-ink-500 dark:text-ink-400">{{ __('landing.hero_toast_sub') }}</p>
                    </div>
                </div>

                {{-- floating: delivered + tracker chip --}}
                <div class="absolute bottom-4 end-4 rounded-2xl border border-ink-200/70 bg-white/95 px-4 py-3 shadow-lift backdrop-blur dark:border-ink-700 dark:bg-ink-900/95">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-violet-500 text-white">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
                        </span>
                        <span class="text-sm font-bold text-ink-900 dark:text-white">{{ __('landing.hero_chip_delivered') }}</span>
                    </div>
                    <p dir="ltr" class="mt-1.5 text-end font-mono text-xs font-semibold tracking-wider text-brand-600 dark:text-brand-300">TLB-9F3K2A</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- 2. TRUST STRIP                                                    --}}
    {{-- ================================================================= --}}
    <section class="mt-16 text-center">
        <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('landing.usecases_title') }}</p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            @foreach (__('landing.usecases_items') as $use)
                <span class="inline-flex items-center gap-2 rounded-full border border-ink-200/80 bg-white/70 px-4 py-2 text-sm font-semibold text-ink-700 shadow-soft dark:border-ink-800 dark:bg-ink-900/60 dark:text-ink-200">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-gradient-to-br from-brand-500 to-violet-500"></span>
                    {{ $use }}
                </span>
            @endforeach
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- 3. FEATURES                                                       --}}
    {{-- ================================================================= --}}
    <section id="features" class="mt-24 scroll-mt-24">
        <div class="mx-auto max-w-2xl text-center">
            <span class="eyebrow">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                {{ __('landing.features_eyebrow') }}
            </span>
            <h2 class="mt-5 text-3xl font-black tracking-tight text-ink-900 sm:text-4xl dark:text-white">{{ __('landing.features_title') }}</h2>
            <p class="mt-4 text-lg leading-relaxed text-ink-600 dark:text-ink-300">{{ __('landing.features_subtitle') }}</p>
        </div>

        @php
            // Distinct icon + accent per feature (class strings kept literal so Tailwind keeps them).
            $featureMeta = [
                ['icon' => 'M13.5 6H18a3 3 0 0 1 0 6h-1.5m-9 0H6a3 3 0 0 1 0-6h4.5M8 9h8', 'tile' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300'],
                ['icon' => 'M6 4h12a1 1 0 0 1 1 1v14l-3.5-2-3.5 2-3.5-2L5 19V5a1 1 0 0 1 1-1Zm3 5h6M9 12h6', 'tile' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300'],
                ['icon' => 'M4 5h6v6H4zM14 5h6v6h-6zM4 15h6v4H4zM14 13h6v6h-6z', 'tile' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300'],
                ['icon' => 'M12 3l7 4v5c0 4-3 7-7 9-4-2-7-5-7-9V7l7-4zM9.5 12l2 2 3.5-3.5', 'tile' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300'],
                ['icon' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1 1 15 0Z', 'tile' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300'],
                ['icon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.49 0 4.5-4.03 4.5-9S14.49 3 12 3 7.5 7.03 7.5 12 9.51 21 12 21ZM3.6 9h16.8M3.6 15h16.8', 'tile' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300'],
            ];
        @endphp

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (__('landing.features_items') as $i => $f)
                @php $m = $featureMeta[$i % count($featureMeta)]; @endphp
                <div class="group relative overflow-hidden rounded-2xl border border-ink-200/70 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lift dark:border-ink-800 dark:bg-ink-900/60 dark:hover:border-brand-500/40">
                    <div aria-hidden="true" class="pointer-events-none absolute -end-10 -top-10 h-24 w-24 rounded-full bg-gradient-to-br from-brand-500/0 to-violet-500/0 blur-2xl transition group-hover:from-brand-500/10 group-hover:to-violet-500/10"></div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $m['tile'] }} transition group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $m['icon'] }}" />
                        </svg>
                    </span>
                    <h3 class="mt-5 text-lg font-bold text-ink-900 dark:text-white">{{ $f['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-600 dark:text-ink-300">{{ $f['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- 4. HOW IT WORKS                                                   --}}
    {{-- ================================================================= --}}
    <section class="mt-24">
        <div class="mx-auto max-w-2xl text-center">
            <span class="eyebrow">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                {{ __('landing.how_eyebrow') }}
            </span>
            <h2 class="mt-5 text-3xl font-black tracking-tight text-ink-900 sm:text-4xl dark:text-white">{{ __('landing.how_title') }}</h2>
        </div>

        <div class="relative mt-12 grid gap-6 lg:grid-cols-3">
            {{-- connecting line (desktop) --}}
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-8 hidden h-px bg-gradient-to-r from-brand-500/0 via-brand-500/30 to-violet-500/0 lg:block"></div>

            @foreach (__('landing.how_steps') as $i => $s)
                <div class="card relative p-6">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 to-violet-600 text-2xl font-black text-white shadow-glow">
                        {{ $i + 1 }}
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-ink-900 dark:text-white">{{ $s['title'] }}</h3>
                    <p class="mt-2 leading-relaxed text-ink-600 dark:text-ink-300">{{ $s['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- 5. PRICING                                                        --}}
    {{-- ================================================================= --}}
    <section id="pricing" class="mt-24 scroll-mt-24">
        <div class="mx-auto max-w-2xl text-center">
            <span class="eyebrow">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                {{ __('landing.pricing_eyebrow') }}
            </span>
            <h2 class="mt-5 text-3xl font-black tracking-tight text-ink-900 sm:text-4xl dark:text-white">{{ __('landing.pricing_title') }}</h2>
            <p class="mt-4 text-lg leading-relaxed text-ink-600 dark:text-ink-300">{{ __('landing.pricing_subtitle') }}</p>
        </div>

        <div class="mx-auto mt-12 grid max-w-4xl items-stretch gap-6 sm:grid-cols-2">
            @foreach ($plans as $plan)
                <div class="relative flex flex-col rounded-3xl p-8
                    {{ $plan->is_featured
                        ? 'border border-brand-500/40 bg-gradient-to-b from-brand-50/80 to-white shadow-lift ring-1 ring-brand-500/30 sm:-my-2 sm:py-10 dark:from-brand-500/10 dark:to-ink-900'
                        : 'border border-ink-200/70 bg-white dark:border-ink-800 dark:bg-ink-900/60' }}">

                    @if ($plan->is_featured)
                        <span class="absolute -top-3.5 start-1/2 inline-flex -translate-x-1/2 items-center gap-1.5 rounded-full bg-gradient-to-r from-brand-600 to-violet-600 px-4 py-1.5 text-xs font-bold text-white shadow-glow rtl:translate-x-1/2">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.29 3.96a1 1 0 0 0 .95.69h4.16c.97 0 1.37 1.24.59 1.81l-3.37 2.45a1 1 0 0 0-.36 1.12l1.28 3.95c.3.93-.75 1.7-1.53 1.12l-3.37-2.44a1 1 0 0 0-1.18 0L6.5 18.5c-.78.58-1.83-.19-1.53-1.12l1.28-3.95a1 1 0 0 0-.36-1.12L2.52 9.86c-.78-.57-.38-1.81.59-1.81H7.3a1 1 0 0 0 .95-.69l1.29-3.96Z" /></svg>
                            {{ __('landing.most_popular') }}
                        </span>
                    @endif

                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-black text-ink-900 dark:text-white">{{ $plan->translate('name') }}</h3>
                    </div>
                    <p class="mt-2 text-sm leading-relaxed text-ink-500 dark:text-ink-400">{{ $plan->description }}</p>

                    <div class="mt-6 flex items-end gap-1.5">
                        <span class="text-5xl font-black tracking-tight text-ink-900 dark:text-white">{{ $plan->formattedPrice() }}</span>
                        @unless ($plan->isFree())
                            <span class="pb-1.5 text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('landing.per_month') }}</span>
                        @endunless
                    </div>

                    @if ($plan->trial_days > 0)
                        <p class="mt-2 text-xs font-semibold text-brand-600 dark:text-brand-400">{{ __('billing.trial_days', ['days' => $plan->trial_days]) }}</p>
                    @endif

                    <div class="my-6 h-px bg-ink-100 dark:bg-ink-800"></div>

                    <ul class="space-y-3.5 text-sm">
                        @foreach ($plan->featureLines() as $line)
                            <li class="flex items-center gap-3 {{ $line['on'] ? 'text-ink-700 dark:text-ink-200' : 'text-ink-400 line-through decoration-ink-300 dark:text-ink-500' }}">
                                @if ($line['on'])
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    </span>
                                @else
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-ink-100 text-ink-400 dark:bg-ink-800 dark:text-ink-500">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                                    </span>
                                @endif
                                <span>{{ $line['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('register') }}" class="{{ $plan->is_featured ? 'btn-primary' : 'btn-secondary' }} mt-8 w-full justify-center py-3">
                        {{ $plan->isFree() ? __('landing.hero_cta') : __('landing.choose_plan') }}
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('plans.index') }}" class="group inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                {{ __('landing.pricing_cta') }}
                <svg class="h-4 w-4 rtl-flip transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- 6. FAQ                                                            --}}
    {{-- ================================================================= --}}
    <section class="mt-24">
        <div class="mx-auto max-w-2xl text-center">
            <span class="eyebrow">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                {{ __('landing.faq_eyebrow') }}
            </span>
            <h2 class="mt-5 text-3xl font-black tracking-tight text-ink-900 sm:text-4xl dark:text-white">{{ __('landing.faq_title') }}</h2>
        </div>

        <div class="mx-auto mt-10 max-w-3xl space-y-4">
            @foreach (__('landing.faq_items') as $qa)
                <div x-data="{ open: false }" class="card overflow-hidden">
                    <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-6 py-5 text-start">
                        <span class="font-semibold text-ink-900 dark:text-white">{{ $qa['q'] }}</span>
                        <svg class="h-5 w-5 shrink-0 text-ink-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <p class="px-6 pb-5 leading-relaxed text-ink-600 dark:text-ink-300">{{ $qa['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- 7. FINAL CTA                                                      --}}
    {{-- ================================================================= --}}
    <section class="mt-24">
        <div class="card relative overflow-hidden bg-gradient-to-br from-brand-600 to-violet-600 px-6 py-16 text-center text-white sm:px-12">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-0 opacity-30">
                <div class="absolute -end-16 -top-16 h-64 w-64 rounded-full bg-white/20 blur-3xl"></div>
                <div class="absolute -bottom-16 -start-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            </div>
            <div class="relative">
                <h2 class="text-3xl font-black tracking-tight sm:text-4xl">{{ __('landing.cta_title') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-lg text-white/85">{{ __('landing.cta_subtitle') }}</p>
                <a href="{{ route('register') }}" class="btn mt-8 bg-white px-6 py-3 text-base text-brand-700 shadow-lift hover:bg-white/90 active:translate-y-px">
                    {{ __('landing.cta_button') }}
                    <svg class="h-4 w-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" /></svg>
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
