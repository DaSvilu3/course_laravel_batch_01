<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('shop.cart') }}</h1>
    </x-slot>

    @if ($cart->isEmpty())
        <div class="card p-12 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-ink-100 text-ink-400 dark:bg-ink-800">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="mt-4 text-lg font-medium text-ink-900 dark:text-white">{{ __('shop.cart_empty') }}</p>
            <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">{{ __('shop.empty_cart_hint') }}</p>
            <a href="{{ route('services.index') }}" class="btn-primary mt-6">{{ __('shop.continue_shopping') }}</a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card divide-y divide-ink-100 lg:col-span-2 dark:divide-ink-800">
                @foreach ($cart->items() as $item)
                    <div class="flex flex-wrap items-center gap-4 p-4">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-ink-100 dark:bg-ink-800">
                            @if ($item->purchasable->purchasableImageUrl())
                                <img src="{{ $item->purchasable->purchasableImageUrl() }}" alt=""
                                     class="h-full w-full object-cover">
                            @endif
                        </div>

                        <div class="min-w-40 flex-1">
                            <a href="{{ $item->purchasable->purchasableUrl() }}" class="font-medium text-ink-900 hover:text-brand-600 dark:text-white dark:hover:text-brand-400">
                                {{ $item->name() }}
                            </a>
                            <p class="text-sm text-ink-500 dark:text-ink-400">{{ $item->formattedUnitPrice() }}</p>

                            @if (! empty($item->options['starts_at']))
                                <p class="text-xs text-ink-500 dark:text-ink-400">
                                    {{ __('shop.appointment') }}:
                                    {{ \Illuminate\Support\Carbon::parse($item->options['starts_at'])->format('Y-m-d H:i') }}
                                </p>
                            @endif

                            @unless ($item->isAvailable())
                                <p class="text-xs font-medium text-rose-600 dark:text-rose-400">{{ __('shop.item_unavailable') }}</p>
                            @endunless
                        </div>

                        <form method="POST" action="{{ route('cart.update', $item->key) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="100"
                                   class="form-input-field w-20 text-sm">
                            <button type="submit" class="btn-secondary text-xs">{{ __('common.update') }}</button>
                        </form>

                        <div class="w-24 text-end font-semibold text-ink-900 dark:text-white">{{ $item->formattedTotal() }}</div>

                        <form method="POST" action="{{ route('cart.destroy', $item->key) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-rose-600 hover:underline dark:text-rose-400">{{ __('shop.remove') }}</button>
                        </form>
                    </div>
                @endforeach

                <div class="flex justify-between p-4">
                    <a href="{{ route('services.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">
                        {{ __('shop.continue_shopping') }}
                    </a>

                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-ink-500 hover:underline dark:text-ink-400">{{ __('shop.clear_cart') }}</button>
                    </form>
                </div>
            </div>

            <div class="card h-fit p-6">
                <h2 class="text-lg font-semibold text-ink-900 dark:text-white">{{ __('shop.order_summary') }}</h2>

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.subtotal') }}</dt>
                        <dd class="font-medium text-ink-700 dark:text-ink-300">{{ $cart->formattedSubtotal() }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-ink-100 pt-2 text-base dark:border-ink-800">
                        <dt class="font-semibold text-ink-900 dark:text-white">{{ __('shop.total') }}</dt>
                        <dd class="font-bold text-ink-900 dark:text-white">{{ $cart->formattedSubtotal() }}</dd>
                    </div>
                </dl>

                <a href="{{ route('checkout.show') }}" class="btn-primary mt-6 w-full">
                    {{ __('shop.proceed_to_checkout') }}
                </a>

                @guest
                    <p class="mt-3 text-center text-xs text-ink-500 dark:text-ink-400">
                        {{ __('common.login') }} / {{ __('common.register') }}
                    </p>
                @endguest
            </div>
        </div>
    @endif
</x-app-layout>
