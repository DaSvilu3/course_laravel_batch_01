@php
    $nav = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => __('admin.dashboard')],
        ['route' => 'admin.orders.index', 'pattern' => 'admin.orders.*', 'label' => __('admin.orders')],
        ['route' => 'admin.subscriptions.index', 'pattern' => 'admin.subscriptions.*', 'label' => __('admin.subscriptions')],
        ['route' => 'admin.plans.index', 'pattern' => 'admin.plans.*', 'label' => __('admin.plans')],
        ['route' => 'admin.payments.index', 'pattern' => 'admin.payments.*', 'label' => __('admin.payments')],
        ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => __('admin.users')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $localeTag ?? app()->getLocale() }}" dir="{{ $dir ?? 'rtl' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — ' : '' }}{{ __('common.admin_panel') }}</title>

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
    <body class="min-h-screen bg-ink-100 font-sans antialiased dark:bg-ink-950">
        <div x-data="{ open: false }" class="flex min-h-screen flex-col lg:flex-row">

            {{-- Sidebar --}}
            <aside class="bg-ink-900 text-ink-300 lg:w-64 lg:shrink-0 dark:border-e dark:border-ink-800 dark:bg-ink-950">
                <div class="flex items-center justify-between px-4 py-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-sm font-black text-white shadow-glow">
                            {{ mb_substr(config('app.name'), 0, 1) }}
                        </span>
                        <span class="font-bold text-white">{{ __('common.admin_panel') }}</span>
                    </a>
                    <button @click="open = ! open" class="rounded-lg p-1.5 text-ink-400 hover:bg-ink-800 lg:hidden">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <nav :class="{ 'block': open, 'hidden': ! open }" class="hidden space-y-1 px-3 pb-4 lg:block">
                    @foreach ($nav as $link)
                        <a href="{{ route($link['route']) }}"
                           class="block rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs($link['pattern']) ? 'bg-brand-600 text-white shadow-glow' : 'text-ink-300 hover:bg-ink-800 hover:text-white' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    <div class="mt-3 space-y-1 border-t border-ink-800 pt-3">
                        <a href="{{ route('home') }}" class="block rounded-xl px-3 py-2 text-sm text-ink-300 transition hover:bg-ink-800 hover:text-white">
                            {{ __('common.view_site') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl px-3 py-2 text-start text-sm text-ink-300 transition hover:bg-ink-800 hover:text-white">
                                {{ __('common.logout') }}
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            {{-- Content --}}
            <div class="flex-1">
                <header class="flex items-center justify-between border-b border-ink-200 bg-white px-6 py-4 dark:border-ink-800 dark:bg-ink-900">
                    <h1 class="text-lg font-semibold text-ink-900 dark:text-white">{{ $header ?? '' }}</h1>
                    <div class="flex items-center gap-3">
                        <x-locale-switcher />
                        <x-theme-toggle />
                        <span class="hidden text-sm text-ink-500 sm:inline dark:text-ink-400">{{ Auth::user()->name }}</span>
                    </div>
                </header>

                <main class="p-6">
                    <x-alerts />
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
