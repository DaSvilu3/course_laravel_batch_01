<x-app-layout>
    <x-slot name="title">{{ config('app.name') }}</x-slot>

    {{-- ================================================================= --}}
    {{-- 1. HERO                                                           --}}
    {{-- ================================================================= --}}
    <section id="top" class="relative overflow-hidden rounded-3xl border border-ink-200/60 bg-white/60 px-6 py-16 backdrop-blur-sm sm:px-12 sm:py-24 dark:border-ink-800/60 dark:bg-ink-900/40">
        {{-- decorative glow blobs --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -end-24 -top-24 h-80 w-80 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -start-20 h-80 w-80 rounded-full bg-violet-500/20 blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-3xl text-center">
            <span class="eyebrow animate-fade-up">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                {{ __('landing.hero_badge') }}
            </span>

            <h1 class="mt-6 animate-fade-up text-4xl font-black leading-tight tracking-tight text-ink-900 sm:text-6xl dark:text-white" style="animation-delay:.05s">
                {{ __('landing.hero_title') }}
                <span class="mt-1 block text-gradient">{{ __('landing.hero_highlight') }}</span>
            </h1>

            <p class="mx-auto mt-6 max-w-2xl animate-fade-up text-lg leading-relaxed text-ink-600 dark:text-ink-300" style="animation-delay:.1s">
                {{ __('landing.hero_subtitle') }}
            </p>

            <div class="mt-9 flex animate-fade-up flex-wrap justify-center gap-3" style="animation-delay:.15s">
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

            <p class="mt-5 animate-fade-up text-sm text-ink-500 dark:text-ink-400" style="animation-delay:.2s">
                {{ __('landing.hero_note') }}
            </p>
        </div>

        {{-- faux dashboard preview card --}}
        <div class="mx-auto mt-14 max-w-4xl animate-fade-up" style="animation-delay:.25s">
            <div class="rounded-2xl border border-ink-200/80 bg-white/80 p-3 shadow-lift backdrop-blur dark:border-ink-800 dark:bg-ink-950/60">
                {{-- window chrome --}}
                <div class="flex items-center gap-1.5 px-2 pb-3 pt-1">
                    <span class="h-2.5 w-2.5 rounded-full bg-rose-400/80"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-400/80"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/80"></span>
                </div>
                <div class="rounded-xl border border-ink-200/70 bg-ink-50/60 p-4 dark:border-ink-800/70 dark:bg-ink-900/60">
                    {{-- stat chips --}}
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-ink-200/70 bg-white p-4 dark:border-ink-800 dark:bg-ink-900">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-ink-500 dark:text-ink-400">{{ __('landing.nav_pricing') }}</span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m4-14H9.5a2.5 2.5 0 0 0 0 5h5a2.5 2.5 0 0 1 0 5H8" /></svg>
                                </span>
                            </div>
                            <p class="mt-2 text-2xl font-black text-ink-900 dark:text-white">98%</p>
                        </div>
                        <div class="rounded-xl border border-ink-200/70 bg-white p-4 dark:border-ink-800 dark:bg-ink-900">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-ink-500 dark:text-ink-400">{{ __('landing.nav_features') }}</span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2L3 14h7l-1 8 10-12h-7l1-8z" /></svg>
                                </span>
                            </div>
                            <p class="mt-2 text-2xl font-black text-ink-900 dark:text-white">12k+</p>
                        </div>
                        <div class="rounded-xl border border-ink-200/70 bg-white p-4 dark:border-ink-800 dark:bg-ink-900">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-ink-500 dark:text-ink-400">{{ __('landing.trusted_by') }}</span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8" /></svg>
                                </span>
                            </div>
                            <p class="mt-2 text-2xl font-black text-ink-900 dark:text-white">4.9<span class="text-base font-semibold text-ink-400">/5</span></p>
                        </div>
                    </div>
                    {{-- faux chart bars --}}
                    <div class="mt-3 flex h-24 items-end gap-2 rounded-xl border border-ink-200/70 bg-white p-4 dark:border-ink-800 dark:bg-ink-900">
                        @foreach ([40, 65, 50, 80, 60, 95, 75] as $h)
                            <div class="flex-1 rounded-t bg-gradient-to-t from-brand-500/70 to-violet-500/70" style="height: {{ $h }}%"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- 2. TRUST STRIP                                                    --}}
    {{-- ================================================================= --}}
    <section class="mt-16 text-center">
        <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('landing.trusted_by') }}</p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-ink-400 dark:text-ink-600">
            <span class="text-xl font-black tracking-tight">Northwind</span>
            <span class="text-xl font-extrabold italic tracking-tight">Lumen</span>
            <span class="text-xl font-black uppercase tracking-widest">Atlas</span>
            <span class="text-xl font-bold tracking-tight">Quantic</span>
            <span class="text-xl font-black tracking-tight">Vertex</span>
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
            $featureIcons = [
                'M4 7h16M4 12h16M4 17h10',
                'M3 10h18M7 15h1m4 0h1M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z',
                'M12 3l7 4v5c0 4-3 7-7 9-4-2-7-5-7-9V7l7-4z',
                'M3 12h4l3 8 4-16 3 8h4',
                'M3 5h18M3 12h18M3 19h18',
                'M12 3a9 9 0 1 0 9 9M12 3v9l6.5 3.8',
            ];
        @endphp

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (__('landing.features_items') as $i => $f)
                <div class="card-hover p-6">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow-soft">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $featureIcons[$i % count($featureIcons)] }}" />
                        </svg>
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-ink-900 dark:text-white">{{ $f['title'] }}</h3>
                    <p class="mt-2 leading-relaxed text-ink-600 dark:text-ink-300">{{ $f['body'] }}</p>
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

        <div class="mt-12 grid items-start gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($plans as $plan)
                <div class="card relative p-8 {{ $plan->is_featured ? 'ring-2 ring-brand-500 shadow-lift' : '' }}">
                    @if ($plan->is_featured)
                        <span class="absolute -top-3 end-6 inline-flex items-center gap-1 rounded-full bg-gradient-to-br from-brand-600 to-violet-600 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white shadow-glow">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.5 5 5.5.8-4 3.9 1 5.5-5-2.7-5 2.7 1-5.5-4-3.9 5.5-.8L12 3z" /></svg>
                            {{ __('landing.most_popular') }}
                        </span>
                    @endif

                    <h3 class="text-lg font-bold text-ink-900 dark:text-white">{{ $plan->translate('name') }}</h3>
                    <p class="mt-2 min-h-[2.5rem] text-sm leading-relaxed text-ink-500 dark:text-ink-400">{{ $plan->description }}</p>

                    <div class="mt-6 flex items-baseline gap-1">
                        <span class="text-4xl font-black tracking-tight text-ink-900 dark:text-white">{{ $plan->formattedPrice() }}</span>
                        @unless ($plan->isFree())
                            <span class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('landing.per_month') }}</span>
                        @endunless
                    </div>

                    @if ($plan->trial_days > 0)
                        <p class="mt-2 text-xs font-medium text-brand-600 dark:text-brand-400">{{ __('billing.trial_days', ['days' => $plan->trial_days]) }}</p>
                    @endif

                    <ul class="mt-6 space-y-3 text-sm">
                        @php $maxProjects = $plan->feature('max_projects'); @endphp
                        <li class="flex items-start gap-2.5 text-ink-700 dark:text-ink-200">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <span>{{ $maxProjects === -1 ? 'Unlimited projects' : $maxProjects . ' projects' }}</span>
                        </li>
                        <li class="flex items-start gap-2.5 {{ $plan->feature('api_access') ? 'text-ink-700 dark:text-ink-200' : 'text-ink-400 dark:text-ink-500' }}">
                            @if ($plan->feature('api_access'))
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            @else
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-ink-300 dark:text-ink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                            @endif
                            <span>API access</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-ink-700 dark:text-ink-200">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <span class="capitalize">{{ $plan->feature('support') }} support</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="{{ $plan->is_featured ? 'btn-primary' : 'btn-secondary' }} mt-8 w-full py-3">
                        {{ __('landing.choose_plan') }}
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
