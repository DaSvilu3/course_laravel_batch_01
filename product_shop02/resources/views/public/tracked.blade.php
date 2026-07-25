<x-public-layout :title="$order->tracker_code">
    @php($regions = \App\Support\Regions::class)
    @php($cancelled = $order->status === \App\Enums\OrderStatus::Cancelled)
    @php($steps = [\App\Enums\OrderStatus::New, \App\Enums\OrderStatus::InProgress, \App\Enums\OrderStatus::Completed])
    @php($currentIndex = $cancelled ? -1 : array_search($order->status, $steps, true))

    <div class="mx-auto max-w-lg">
        @if (session('created'))
            <div class="card border-emerald-200 bg-emerald-50 p-5 text-center dark:border-emerald-900/50 dark:bg-emerald-950/40">
                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </span>
                <h1 class="mt-3 text-lg font-bold text-emerald-900 dark:text-emerald-200">{{ __('shop.order_received_title') }}</h1>
                <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-300/90">{{ __('shop.order_received_body') }}</p>
            </div>
        @endif

        {{-- Tracker code --}}
        <div x-data="{ copied: false }" class="card mt-6 p-6 text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-500 dark:text-ink-400">{{ __('shop.your_tracker_code') }}</p>
            <div class="mt-2 flex items-center justify-center gap-3">
                <span class="font-mono text-2xl font-black tracking-widest text-ink-900 dark:text-white">{{ $order->tracker_code }}</span>
                <button type="button" @click="navigator.clipboard.writeText('{{ $order->tracker_code }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        class="btn-ghost px-2 py-1.5 text-xs">
                    <span x-show="!copied">{{ __('shop.copy') }}</span>
                    <span x-show="copied" x-cloak class="text-emerald-600 dark:text-emerald-400">{{ __('shop.copied') }}</span>
                </button>
            </div>
            <p class="mt-3 text-sm text-ink-500 dark:text-ink-400">{{ $order->merchant->store_name ?: $order->merchant->name }}</p>
        </div>

        {{-- Status --}}
        <div class="card mt-6 p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-ink-900 dark:text-white">{{ __('shop.order_status_now') }}</h2>
                <x-status-badge :status="$order->status" />
            </div>

            @if ($cancelled)
                <p class="mt-5 rounded-xl bg-rose-50 px-4 py-3 text-center text-sm text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                    {{ $order->status->label() }}
                </p>
            @else
                <ol class="mt-6 flex items-center">
                    @foreach ($steps as $i => $step)
                        <li @class(['flex items-center', 'flex-1' => ! $loop->last])>
                            <div class="flex flex-col items-center gap-2">
                                <span @class([
                                    'flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold ring-2',
                                    'bg-brand-600 text-white ring-brand-600' => $i <= $currentIndex,
                                    'bg-white text-ink-400 ring-ink-200 dark:bg-ink-900 dark:ring-ink-700' => $i > $currentIndex,
                                ])>
                                    @if ($i < $currentIndex)
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </span>
                                <span class="text-center text-xs font-medium {{ $i <= $currentIndex ? 'text-ink-900 dark:text-white' : 'text-ink-400 dark:text-ink-500' }}">{{ $step->label() }}</span>
                            </div>
                            @unless ($loop->last)
                                <div class="mx-1 h-0.5 flex-1 rounded {{ $i < $currentIndex ? 'bg-brand-600' : 'bg-ink-200 dark:bg-ink-700' }}"></div>
                            @endunless
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

        {{-- Order summary --}}
        <div class="card mt-6 p-6">
            <h2 class="font-bold text-ink-900 dark:text-white">{{ __('shop.order_details') }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.item') }}</dt>
                    <dd class="text-end font-medium text-ink-900 dark:text-white">{{ $order->item_description }} × {{ $order->quantity }}</dd>
                </div>
                @if ($order->hasPrice())
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.price') }}</dt>
                        <dd class="font-medium text-ink-900 dark:text-white">{{ $order->formattedPrice() }}</dd>
                    </div>
                @endif
                @if ($order->wilayat)
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.delivery') }}</dt>
                        <dd class="font-medium text-ink-900 dark:text-white">{{ $regions::wilayatLabel($order->wilayat) }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('track.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">{{ __('shop.track_another') }}</a>
        </div>
    </div>
</x-public-layout>
