<!DOCTYPE html>
<html lang="{{ $localeTag ?? app()->getLocale() }}" dir="{{ $dir ?? 'rtl' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        {{-- Apply the saved theme before paint (matches the app layout). --}}
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
    <body class="font-sans antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            {{-- ambient glow --}}
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute -top-40 start-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-brand-500/15 blur-3xl"></div>
                <div class="absolute inset-0 bg-grid-light [background-size:22px_22px] opacity-60 dark:bg-grid-dark"></div>
            </div>

            <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-base font-black text-white shadow-glow transition group-hover:scale-105">
                    {{ mb_substr(config('app.name'), 0, 1) }}
                </span>
                <span class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ config('app.name') }}</span>
            </a>

            <div class="mt-8 w-full overflow-hidden rounded-2xl border border-ink-200/80 bg-white/90 px-6 py-6 shadow-lift backdrop-blur sm:max-w-md dark:border-ink-800 dark:bg-ink-900/90">
                {{ $slot }}
            </div>

            <div class="mt-6">
                <x-locale-switcher />
            </div>
        </div>
    </body>
</html>
