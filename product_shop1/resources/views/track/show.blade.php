<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('orders.track_title') }}</h1>
        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.track_subtitle') }}</p>
    </div>

    <form method="GET" action="{{ route('track') }}" class="flex gap-2">
        <input type="text" name="code" value="{{ $code }}" dir="ltr" required
               placeholder="{{ __('orders.track_placeholder') }}"
               class="form-input-field block w-full text-start font-mono uppercase tracking-wider" />
        <x-primary-button class="shrink-0 px-5">{{ __('orders.track_button') }}</x-primary-button>
    </form>

    @if ($notFound)
        <div class="mt-5 rounded-xl border border-rose-300/70 bg-rose-50 px-4 py-3 text-center text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            {{ __('orders.track_not_found') }}
        </div>
    @endif

    @isset($order)
        @if ($order)
            @php
                $timeline = collect(\App\Enums\MerchantOrderStatus::cases())
                    ->reject(fn ($s) => $s === \App\Enums\MerchantOrderStatus::Cancelled);
                $isCancelled = $order->status === \App\Enums\MerchantOrderStatus::Cancelled;
                $current = $order->status->step();
            @endphp

            <div class="mt-6 rounded-2xl border border-ink-200/70 bg-ink-50/50 p-5 dark:border-ink-800 dark:bg-ink-950/40">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-ink-500 dark:text-ink-400">{{ __('orders.track_for', ['store' => $order->user->displayStoreName()]) }}</p>
                        <p dir="ltr" class="mt-0.5 text-start font-mono text-sm font-semibold text-ink-800 dark:text-ink-200">{{ $order->tracker_code }}</p>
                    </div>
                    <x-status-badge :status="$order->status" />
                </div>

                @if ($isCancelled)
                    <p class="mt-5 rounded-xl border border-rose-200/70 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                        {{ __('orders.was_cancelled') }}
                    </p>
                @else
                    <ol class="mt-5 space-y-4">
                        @foreach ($timeline as $step)
                            @php $done = $step->step() <= $current; @endphp
                            <li class="flex items-center gap-3">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs
                                    {{ $done ? 'bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow-soft' : 'border border-ink-200 text-ink-400 dark:border-ink-700 dark:text-ink-500' }}">
                                    @if ($done)
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
                                    @else
                                        {{ $step->step() }}
                                    @endif
                                </span>
                                <span class="text-sm font-medium {{ $done ? 'text-ink-900 dark:text-white' : 'text-ink-400 dark:text-ink-500' }}">{{ $step->label() }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif

                <p class="mt-5 text-xs text-ink-400 dark:text-ink-500">
                    {{ __('orders.ordered_on') }}: {{ $order->created_at?->isoFormat('LL') }}
                </p>
            </div>
        @endif
    @endisset
</x-guest-layout>
