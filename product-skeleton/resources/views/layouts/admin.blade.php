@php
    $nav = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => __('admin.dashboard')],
        ['route' => 'admin.orders.index', 'pattern' => 'admin.orders.*', 'label' => __('admin.orders')],
        ['route' => 'admin.payments.index', 'pattern' => 'admin.payments.*', 'label' => __('admin.payments')],
        ['route' => 'admin.bookings.index', 'pattern' => 'admin.bookings.*', 'label' => __('admin.bookings')],
        ['route' => 'admin.services.index', 'pattern' => 'admin.services.*', 'label' => __('admin.services')],
        ['route' => 'admin.products.index', 'pattern' => 'admin.products.*', 'label' => __('admin.products')],
        ['route' => 'admin.categories.index', 'pattern' => 'admin.categories.*', 'label' => __('admin.categories')],
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

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-100 font-sans antialiased">
        <div x-data="{ open: false }" class="flex min-h-screen flex-col lg:flex-row">

            {{-- Sidebar --}}
            <aside class="bg-gray-900 text-gray-300 lg:w-64 lg:shrink-0">
                <div class="flex items-center justify-between px-4 py-4">
                    <a href="{{ route('admin.dashboard') }}" class="font-bold text-white">
                        {{ __('common.admin_panel') }}
                    </a>
                    <button @click="open = ! open" class="rounded p-1 text-gray-400 lg:hidden">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <nav :class="{ 'block': open, 'hidden': ! open }" class="hidden space-y-1 px-3 pb-4 lg:block">
                    @foreach ($nav as $link)
                        <a href="{{ route($link['route']) }}"
                           class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs($link['pattern']) ? 'bg-brand-600 text-white' : 'hover:bg-gray-800' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    <div class="border-t border-gray-800 pt-3">
                        <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-gray-800">
                            {{ __('common.view_site') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-lg px-3 py-2 text-start text-sm hover:bg-gray-800">
                                {{ __('common.logout') }}
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            {{-- Content --}}
            <div class="flex-1">
                <header class="flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
                    <h1 class="text-lg font-semibold text-gray-900">{{ $header ?? '' }}</h1>
                    <div class="flex items-center gap-4">
                        <x-locale-switcher />
                        <span class="text-sm text-gray-500">{{ Auth::user()->name }}</span>
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
