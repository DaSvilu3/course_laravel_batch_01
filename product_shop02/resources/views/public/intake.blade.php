<x-public-layout :title="$merchant->store_name">
    {{-- Store header --}}
    <div class="text-center">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 to-violet-600 text-xl font-black text-white shadow-glow">
            {{ mb_substr($merchant->store_name ?: $merchant->name, 0, 1) }}
        </span>
        <h1 class="mt-4 text-2xl font-black tracking-tight text-ink-900 dark:text-white">{{ $merchant->store_name ?: $merchant->name }}</h1>
        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">{{ __('shop.intake_subtitle') }}</p>
    </div>

    @error('quota')
        <div class="card mt-6 border-amber-200 bg-amber-50 p-4 text-center text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('intake.store', ['slug' => $merchant->intake_slug]) }}" enctype="multipart/form-data" class="card mt-6 p-6">
        @csrf
        @include('partials.order-fields')

        <button class="btn-primary mt-8 w-full justify-center py-3 text-base">
            {{ __('shop.intake_submit') }}
            <svg class="h-4 w-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" /></svg>
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-ink-400 dark:text-ink-500">
        {{ __('shop.powered_by', ['name' => config('app.name')]) }} ·
        <a href="{{ route('track.index') }}" class="text-brand-600 hover:underline dark:text-brand-400">{{ __('shop.track') }}</a>
    </p>
</x-public-layout>
