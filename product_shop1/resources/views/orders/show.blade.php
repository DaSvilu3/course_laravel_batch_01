<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('merchant.orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink-500 hover:text-brand-600 dark:text-ink-400">
                    <svg class="h-4 w-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    {{ __('orders.back_to_orders') }}
                </a>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 dir="ltr" class="font-mono text-2xl font-black tracking-tight text-ink-900 dark:text-white">{{ $order->tracker_code }}</h1>
                    <x-status-badge :status="$order->status" />
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $waDigits = preg_replace('/\D/', '', $order->customer_phone);
        $waNumber = strlen($waDigits) === 8 ? '968'.$waDigits : $waDigits;
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- ---- Main details ---- --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Customer --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink-500 dark:text-ink-400">{{ __('orders.customer_info') }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.form_name') }}</p>
                        <p class="mt-0.5 font-medium text-ink-900 dark:text-white">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.phone') }}</p>
                        <p dir="ltr" class="mt-0.5 text-start font-medium text-ink-900 dark:text-white">{{ $order->customer_phone }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.location') }}</p>
                        <p class="mt-0.5 font-medium text-ink-900 dark:text-white">{{ $order->customer_location ?: __('orders.not_provided') }}</p>
                    </div>
                </div>
                @if ($waDigits)
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                       class="btn mt-5 w-full justify-center bg-emerald-600 py-2.5 text-white hover:bg-emerald-500 sm:w-auto">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.75.46 3.45 1.32 4.95L2 22l5.3-1.38a9.9 9.9 0 0 0 4.73 1.2h.01c5.46 0 9.9-4.44 9.9-9.9 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm5.8 14.02c-.25.7-1.44 1.33-1.99 1.37-.53.05-1.02.24-3.42-.72-2.9-1.14-4.73-4.1-4.88-4.29-.14-.19-1.16-1.54-1.16-2.94 0-1.4.73-2.08 1-2.37.24-.26.53-.32.7-.32l.5.01c.16 0 .38-.06.59.45.25.6.83 2.08.9 2.23.07.15.12.32.02.51-.1.19-.15.31-.29.48l-.44.51c-.14.14-.29.3-.12.58.16.29.73 1.2 1.57 1.95 1.08.96 1.99 1.26 2.27 1.4.28.15.44.13.6-.08.16-.19.7-.81.88-1.09.19-.28.37-.23.62-.14.25.09 1.62.76 1.9.9.28.14.46.21.53.33.07.12.07.68-.18 1.37Z"/></svg>
                        {{ __('orders.chat_whatsapp') }}
                    </a>
                @endif
            </div>

            {{-- Order --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink-500 dark:text-ink-400">{{ __('orders.order_info') }}</h2>
                <p class="mt-4 whitespace-pre-line leading-relaxed text-ink-800 dark:text-ink-100">{{ $order->item_description }}</p>

                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-ink-200/70 bg-ink-50/60 p-3 dark:border-ink-800 dark:bg-ink-950/40">
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.form_quantity') }}</p>
                        <p class="mt-0.5 text-lg font-bold text-ink-900 dark:text-white">{{ $order->quantity }}</p>
                    </div>
                    <div class="rounded-xl border border-ink-200/70 bg-ink-50/60 p-3 dark:border-ink-800 dark:bg-ink-950/40">
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.form_amount') }}</p>
                        <p class="mt-0.5 text-lg font-bold text-ink-900 dark:text-white">{{ $order->formattedAmount() ?: __('orders.not_provided') }}</p>
                    </div>
                    <div class="col-span-2 rounded-xl border border-ink-200/70 bg-ink-50/60 p-3 sm:col-span-1 dark:border-ink-800 dark:bg-ink-950/40">
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.placed_at') }}</p>
                        <p class="mt-0.5 text-sm font-semibold text-ink-900 dark:text-white">{{ $order->created_at?->isoFormat('D MMM, HH:mm') }}</p>
                    </div>
                </div>

                @if ($order->notes)
                    <div class="mt-4 rounded-xl border border-ink-200/70 bg-white p-3 dark:border-ink-800 dark:bg-ink-900">
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.form_notes') }}</p>
                        <p class="mt-1 text-sm text-ink-700 dark:text-ink-200">{{ $order->notes }}</p>
                    </div>
                @endif

                @if ($order->imageUrl())
                    <div class="mt-4">
                        <p class="text-xs text-ink-400 dark:text-ink-500">{{ __('orders.order_photo') }}</p>
                        <a href="{{ $order->imageUrl() }}" target="_blank" rel="noopener"
                           class="group mt-2 block overflow-hidden rounded-xl border border-ink-200/70 dark:border-ink-800">
                            <img src="{{ $order->imageUrl() }}" alt="{{ __('orders.order_photo') }}"
                                 class="max-h-72 w-full object-cover transition group-hover:opacity-90">
                        </a>
                    </div>
                @endif
            </div>

            {{-- Change status --}}
            <div class="card p-6">
                <label for="status" class="text-sm font-semibold text-ink-500 dark:text-ink-400">{{ __('orders.change_status') }}</label>
                <form method="POST" action="{{ route('merchant.orders.update', $order) }}" class="mt-3 flex flex-col gap-3 sm:flex-row">
                    @csrf
                    @method('PATCH')
                    <select id="status" name="status" class="form-input-field w-full sm:max-w-xs">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <x-primary-button class="justify-center">{{ __('common.save') }}</x-primary-button>
                </form>
            </div>
        </div>

        {{-- ---- History timeline ---- --}}
        <div class="lg:col-span-1">
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink-500 dark:text-ink-400">{{ __('orders.history_title') }}</h2>

                @if ($order->events->isEmpty())
                    <p class="mt-4 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.history_empty') }}</p>
                @else
                    <ol class="mt-5 space-y-5">
                        @foreach ($order->events as $event)
                            <li class="relative flex gap-4 ps-1">
                                {{-- connector --}}
                                @unless ($loop->last)
                                    <span aria-hidden="true" class="absolute top-7 h-[calc(100%+0.25rem)] w-px bg-ink-200 dark:bg-ink-700" style="inset-inline-start: 0.6875rem;"></span>
                                @endunless
                                <span class="relative z-10 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $event->status->color() }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-ink-900 dark:text-white">{{ $event->status->label() }}</p>
                                    <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">{{ $event->created_at?->isoFormat('D MMM YYYY · HH:mm') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
