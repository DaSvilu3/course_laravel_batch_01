{{-- Storefront + customer area layout. $dir / $localeTag are shared by the SetLocale middleware. --}}
<!DOCTYPE html>
<html lang="{{ $localeTag ?? app()->getLocale() }}" dir="{{ $dir ?? 'rtl' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 font-sans antialiased">
        @include('layouts.navigation')

        @isset($header)
            <header class="border-b border-gray-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <x-alerts />
            {{ $slot }}
        </main>

        <footer class="mt-12 border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 text-sm text-gray-500 sm:px-6 lg:px-8">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
        </footer>
    </body>
</html>
