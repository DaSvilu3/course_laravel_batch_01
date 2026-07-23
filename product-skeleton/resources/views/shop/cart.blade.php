<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('shop.cart') }}</h1>
    </x-slot>

    @if ($cart->isEmpty())
        <div class="card p-12 text-center">
            <p class="text-lg font-medium text-gray-900">{{ __('shop.cart_empty') }}</p>
            <p class="mt-2 text-sm text-gray-500">{{ __('shop.empty_cart_hint') }}</p>
            <a href="{{ route('services.index') }}" class="btn-primary mt-6">{{ __('shop.continue_shopping') }}</a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card divide-y divide-gray-100 lg:col-span-2">
                @foreach ($cart->items() as $item)
                    <div class="flex flex-wrap items-center gap-4 p-4">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                            @if ($item->purchasable->purchasableImageUrl())
                                <img src="{{ $item->purchasable->purchasableImageUrl() }}" alt=""
                                     class="h-full w-full object-cover">
                            @endif
                        </div>

                        <div class="min-w-40 flex-1">
                            <a href="{{ $item->purchasable->purchasableUrl() }}" class="font-medium text-gray-900 hover:text-brand-700">
                                {{ $item->name() }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $item->formattedUnitPrice() }}</p>

                            @if (! empty($item->options['starts_at']))
                                <p class="text-xs text-gray-500">
                                    {{ __('shop.appointment') }}:
                                    {{ \Illuminate\Support\Carbon::parse($item->options['starts_at'])->format('Y-m-d H:i') }}
                                </p>
                            @endif

                            @unless ($item->isAvailable())
                                <p class="text-xs font-medium text-rose-600">{{ __('shop.item_unavailable') }}</p>
                            @endunless
                        </div>

                        <form method="POST" action="{{ route('cart.update', $item->key) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="100"
                                   class="w-20 rounded-lg border-gray-300 text-sm">
                            <button type="submit" class="btn-secondary text-xs">{{ __('common.update') }}</button>
                        </form>

                        <div class="w-24 text-end font-semibold text-gray-900">{{ $item->formattedTotal() }}</div>

                        <form method="POST" action="{{ route('cart.destroy', $item->key) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-rose-600 hover:underline">{{ __('shop.remove') }}</button>
                        </form>
                    </div>
                @endforeach

                <div class="flex justify-between p-4">
                    <a href="{{ route('services.index') }}" class="text-sm text-brand-700 hover:underline">
                        {{ __('shop.continue_shopping') }}
                    </a>

                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-gray-500 hover:underline">{{ __('shop.clear_cart') }}</button>
                    </form>
                </div>
            </div>

            <div class="card h-fit p-6">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('shop.order_summary') }}</h2>

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ __('shop.subtotal') }}</dt>
                        <dd class="font-medium">{{ $cart->formattedSubtotal() }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-100 pt-2 text-base">
                        <dt class="font-semibold">{{ __('shop.total') }}</dt>
                        <dd class="font-bold text-brand-700">{{ $cart->formattedSubtotal() }}</dd>
                    </div>
                </dl>

                <a href="{{ route('checkout.show') }}" class="btn-primary mt-6 w-full">
                    {{ __('shop.proceed_to_checkout') }}
                </a>

                @guest
                    <p class="mt-3 text-center text-xs text-gray-500">
                        {{ __('common.login') }} / {{ __('common.register') }}
                    </p>
                @endguest
            </div>
        </div>
    @endif
</x-app-layout>
