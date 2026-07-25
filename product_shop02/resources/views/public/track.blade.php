<x-public-layout :title="__('shop.track_title')">
    <div class="mx-auto max-w-md">
        <div class="text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
            </span>
            <h1 class="mt-4 text-2xl font-black tracking-tight text-ink-900 dark:text-white">{{ __('shop.track_title') }}</h1>
            <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">{{ __('shop.track_subtitle') }}</p>
        </div>

        @if (! empty($notFound))
            <div class="card mt-6 border-rose-200 bg-rose-50 p-4 text-center text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                {{ __('shop.track_not_found') }}
            </div>
        @endif

        <form method="POST" action="{{ route('track.lookup') }}" class="card mt-6 p-6">
            @csrf
            <x-input-label for="code" :value="__('shop.track_code_label')" />
            <x-text-input id="code" name="code" :value="old('code', $code ?? '')" class="mt-1.5 block w-full text-center font-mono text-lg tracking-widest" dir="ltr" required autofocus placeholder="{{ __('shop.track_placeholder') }}" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
            <button class="btn-primary mt-5 w-full justify-center py-3">{{ __('shop.track_button') }}</button>
        </form>
    </div>
</x-public-layout>
