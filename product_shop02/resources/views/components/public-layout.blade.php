@props(['title' => ''])

<!DOCTYPE html>
<html lang="{{ $localeTag ?? app()->getLocale() }}" dir="{{ $dir ?? 'rtl' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>

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
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-40 start-1/2 h-[32rem] w-[32rem] -translate-x-1/2 rounded-full bg-brand-500/10 blur-3xl dark:bg-brand-500/15"></div>
            <div class="absolute inset-0 bg-grid-light [background-size:22px_22px] opacity-60 dark:bg-grid-dark"></div>
        </div>

        <div class="mx-auto flex min-h-screen max-w-2xl flex-col px-4 py-8 sm:py-12">
            <header class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-sm font-black text-white shadow-glow">
                        {{ mb_substr(config('app.name'), 0, 1) }}
                    </span>
                    <span class="text-lg font-bold tracking-tight text-ink-900 dark:text-white">{{ config('app.name') }}</span>
                </a>
                <div class="flex items-center gap-2">
                    <x-locale-switcher />
                    <x-theme-toggle />
                </div>
            </header>

            <main class="flex-1 py-8">
                {{ $slot }}
            </main>

            <footer class="border-t border-ink-200/70 pt-5 text-center text-xs text-ink-400 dark:border-ink-800/70 dark:text-ink-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </footer>
        </div>
    </body>
</html>
