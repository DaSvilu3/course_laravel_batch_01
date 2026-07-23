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

        <footer class="mt-16 border-t border-ink-200/70 dark:border-ink-800/70">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-8 text-sm text-ink-500 sm:flex-row sm:px-6 lg:px-8 dark:text-ink-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                <p class="flex items-center gap-1.5">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ __('common.all_systems_operational') }}
                </p>
            </div>
        </footer>
    </body>
</html>
