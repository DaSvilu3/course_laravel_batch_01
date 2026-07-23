<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">
                    {{ __('shop.order') }} <span class="font-mono">{{ $order->number }}</span>
                </h1>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ $order->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <x-status-badge :status="$order->status" />
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card divide-y divide-ink-100 lg:col-span-2 dark:divide-ink-800">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between gap-4 p-4">
                    <div>
                        <p class="font-medium text-ink-900 dark:text-white">{{ $item->name }}</p>
                        <p class="text-sm text-ink-500 dark:text-ink-400">
                            {{ $item->formattedUnitPrice() }} × {{ $item->quantity }}
                        </p>
                    </div>
                    <span class="font-semibold text-ink-900 dark:text-white">{{ $item->formattedTotal() }}</span>
                </div>
            @endforeach
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.subtotal') }}</dt>
                        <dd class="text-ink-700 dark:text-ink-300">{{ App\Support\Money::format($order->subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-ink-100 pt-2 text-base dark:border-ink-800">
                        <dt class="font-semibold text-ink-900 dark:text-white">{{ __('shop.total') }}</dt>
                        <dd class="font-bold text-ink-900 dark:text-white">{{ $order->formattedTotal() }}</dd>
                    </div>
                    @if ($order->paid_at)
                        <div class="flex justify-between pt-2 text-xs text-ink-500 dark:text-ink-400">
                            <dt>{{ __('shop.paid_at') }}</dt>
                            <dd>{{ $order->paid_at->format('Y-m-d H:i') }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($order->isPayable())
                    <form method="POST" action="{{ route('orders.pay', $order) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="btn-primary w-full">{{ __('shop.pay_now') }}</button>
                    </form>

                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn-secondary w-full"
                                onclick="return confirm('{{ __('common.confirm_delete') }}')">
                            {{ __('shop.cancel_order') }}
                        </button>
                    </form>
                @endif
            </div>

            @if ($order->payments->isNotEmpty())
                <div class="card p-6">
                    <h2 class="mb-3 text-sm font-semibold text-ink-900 dark:text-white">{{ __('shop.payment_history') }}</h2>
                    <ul class="space-y-2 text-xs">
                        @foreach ($order->payments as $payment)
                            <li class="flex items-center justify-between gap-2">
                                <span class="text-ink-500 dark:text-ink-400">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                                <x-status-badge :status="$payment->status" />
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
