<x-guest-layout>
    <div class="text-center"
         x-data="{ copied: false, copy() { navigator.clipboard.writeText('{{ $trackerCode }}').then(() => { this.copied = true; setTimeout(() => this.copied = false, 1800); }); } }">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
        </span>

        <h1 class="mt-5 text-xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('orders.success_title') }}</h1>
        <p class="mx-auto mt-2 max-w-sm text-sm text-ink-500 dark:text-ink-400">
            {{ __('orders.success_body', ['store' => $merchant->displayStoreName()]) }}
        </p>

        <div class="mt-6 rounded-2xl border border-dashed border-brand-300 bg-brand-50/60 p-5 dark:border-brand-500/40 dark:bg-brand-500/10">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-300">{{ __('orders.your_tracker_code') }}</p>
            <div class="mt-2 flex items-center justify-center gap-3">
                <span dir="ltr" class="font-mono text-2xl font-black tracking-widest text-ink-900 dark:text-white">{{ $trackerCode }}</span>
                <button type="button" @click="copy()" class="btn-ghost px-2.5 py-1.5 text-xs">
                    <span x-show="!copied">{{ __('orders.copy') }}</span>
                    <span x-show="copied" x-cloak class="text-emerald-600 dark:text-emerald-400">{{ __('orders.copied') }}</span>
                </button>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3">
            <a href="{{ route('track', $trackerCode) }}" class="btn-primary w-full justify-center py-3">{{ __('orders.track_now') }}</a>
            <a href="{{ route('intake.show', $merchant->store_slug) }}" class="btn-ghost w-full justify-center">{{ __('orders.place_another') }}</a>
        </div>
    </div>
</x-guest-layout>
