<x-app-layout>
    <x-slot name="title">{{ __('privacy.title') }}</x-slot>

    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('privacy.title') }}</h1>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('privacy.updated') }}: 24 July 2026</p>
        </div>
    </x-slot>

    <div class="card mx-auto max-w-3xl p-8">
        <p class="text-ink-500 dark:text-ink-400 leading-relaxed">{{ __('privacy.intro') }}</p>

        @foreach (__('privacy.sections') as $s)
            <h2 class="mt-8 first:mt-0 text-lg font-semibold text-ink-900 dark:text-white">{{ $s['heading'] }}</h2>
            <p class="mt-2 text-ink-600 dark:text-ink-300 leading-relaxed">{{ $s['body'] }}</p>
        @endforeach
    </div>
</x-app-layout>
