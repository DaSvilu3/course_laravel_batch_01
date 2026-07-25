{{-- Storefront + customer area layout. $dir / $localeTag are shared by the SetLocale middleware. --}}
<!DOCTYPE html>
<html lang="{{ $localeTag ?? app()->getLocale() }}" dir="{{ $dir ?? 'rtl' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('app.name') }}</title>

        {{-- Set the theme before first paint to avoid a flash of the wrong colours. --}}
        <script>
            (function () {
                var t = localStorage.getItem('theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans antialiased">
        {{-- Ambient background glow — subtle, sits behind everything. --}}
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-40 start-1/2 h-[32rem] w-[32rem] -translate-x-1/2 rounded-full bg-brand-500/10 blur-3xl dark:bg-brand-500/15"></div>
            <div class="absolute inset-0 bg-grid-light [background-size:22px_22px] opacity-60 dark:bg-grid-dark"></div>
        </div>

        @include('layouts.navigation')

        @isset($header)
            <header class="border-b border-ink-200/70 bg-white/60 backdrop-blur dark:border-ink-800/70 dark:bg-ink-900/40">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <x-alerts />
            {{ $slot }}
        </main>

        <footer class="mt-20 border-t border-ink-200/70 dark:border-ink-800/70">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-8 sm:flex-row sm:items-start sm:justify-between">
                    <div class="max-w-xs">
                        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-sm font-black text-white shadow-glow">
                                {{ mb_substr(config('app.name'), 0, 1) }}
                            </span>
                            <span class="text-lg font-bold tracking-tight text-ink-900 dark:text-white">{{ config('app.name') }}</span>
                        </a>
                        <p class="mt-4 text-sm text-ink-500 dark:text-ink-400">{{ __('landing.footer_tagline') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-12">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-ink-500">{{ __('landing.footer_product') }}</h3>
                            <ul class="mt-4 space-y-3 text-sm">
                                <li><a href="{{ route('home').'#features' }}" class="text-ink-600 hover:text-brand-600 dark:text-ink-300 dark:hover:text-brand-400">{{ __('landing.nav_features') }}</a></li>
                                <li><a href="{{ route('plans.index') }}" class="text-ink-600 hover:text-brand-600 dark:text-ink-300 dark:hover:text-brand-400">{{ __('landing.nav_pricing') }}</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-400 dark:text-ink-500">{{ __('landing.footer_company') }}</h3>
                            <ul class="mt-4 space-y-3 text-sm">
                                <li><a href="{{ route('privacy') }}" class="text-ink-600 hover:text-brand-600 dark:text-ink-300 dark:hover:text-brand-400">{{ __('landing.nav_privacy') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-ink-200/70 pt-6 text-sm text-ink-500 sm:flex-row dark:border-ink-800/70 dark:text-ink-400">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }} — {{ __('landing.footer_rights') }}</p>
                    <p class="flex items-center gap-1.5">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        {{ __('common.all_systems_operational') }}
                    </p>
                </div>
            </div>
        </footer>
    </body>
</html>
