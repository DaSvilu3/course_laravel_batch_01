@props(['title' => '', 'subtitle' => ''])

<!DOCTYPE html>
<html lang="{{ $localeTag ?? app()->getLocale() }}" dir="{{ $dir ?? 'rtl' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>

        {{-- Apply the saved theme before paint. --}}
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
    <body class="min-h-screen bg-white font-sans antialiased dark:bg-ink-950">
        <div class="grid min-h-screen lg:grid-cols-2">
            {{-- ---- Brand panel (hidden on small screens) ---------------- --}}
            <aside class="relative hidden overflow-hidden bg-gradient-to-br from-brand-600 via-brand-700 to-violet-800 p-12 lg:flex lg:flex-col lg:justify-between">
                {{-- decorative glows + grid --}}
                <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                    <div class="absolute -end-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="absolute -bottom-32 -start-16 h-80 w-80 rounded-full bg-violet-400/20 blur-3xl"></div>
                    <div class="absolute inset-0 opacity-20 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:26px_26px]"></div>
                </div>

                <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-base font-black text-white ring-1 ring-white/20 backdrop-blur">
                        {{ mb_substr(config('app.name'), 0, 1) }}
                    </span>
                    <span class="text-xl font-bold tracking-tight text-white">{{ config('app.name') }}</span>
                </a>

                <div class="relative z-10 max-w-md">
                    <h2 class="text-3xl font-bold leading-tight text-white">{{ __('landing.auth_panel_title') }}</h2>
                    <ul class="mt-8 space-y-4">
                        @foreach (__('landing.auth_panel_points') as $point)
                            <li class="flex items-start gap-3 text-brand-50">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/20">
                                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-base">{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="relative z-10 flex items-center gap-4 text-sm text-brand-100">
                    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                    <a href="{{ route('privacy') }}" class="hover:text-white">{{ __('landing.nav_privacy') }}</a>
                </div>
            </aside>

            {{-- ---- Form panel ------------------------------------------ --}}
            <main class="relative flex flex-col px-6 py-8 sm:px-12">
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 lg:invisible">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-sm font-black text-white shadow-glow">
                            {{ mb_substr(config('app.name'), 0, 1) }}
                        </span>
                        <span class="text-lg font-bold tracking-tight text-ink-900 dark:text-white">{{ config('app.name') }}</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <x-locale-switcher />
                        <x-theme-toggle />
                    </div>
                </div>

                <div class="flex flex-1 items-center justify-center py-10">
                    <div class="w-full max-w-md">
                        @if ($title)
                            <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ $title }}</h1>
                        @endif
                        @if ($subtitle)
                            <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">{{ $subtitle }}</p>
                        @endif

                        <div class="mt-8">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
