<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ __('shop.order') }} <span class="font-mono">{{ $order->number }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">{{ $order->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <x-status-badge :status="$order->status" />
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card divide-y divide-gray-100 lg:col-span-2">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between gap-4 p-4">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $item->formattedUnitPrice() }} × {{ $item->quantity }}
                        </p>
                    </div>
                    <span class="font-semibold">{{ $item->formattedTotal() }}</span>
                </div>
            @endforeach
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ __('shop.subtotal') }}</dt>
                        <dd>{{ App\Support\Money::format($order->subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-100 pt-2 text-base">
                        <dt class="font-semibold">{{ __('shop.total') }}</dt>
                        <dd class="font-bold text-brand-700">{{ $order->formattedTotal() }}</dd>
                    </div>
                    @if ($order->paid_at)
                        <div class="flex justify-between pt-2 text-xs text-gray-500">
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
                    <h2 class="mb-3 text-sm font-semibold text-gray-900">{{ __('shop.payment_history') }}</h2>
                    <ul class="space-y-2 text-xs">
                        @foreach ($order->payments as $payment)
                            <li class="flex items-center justify-between gap-2">
                                <span class="text-gray-500">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                                <x-status-badge :status="$payment->status" />
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
