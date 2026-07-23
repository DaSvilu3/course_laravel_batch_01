<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('shop.checkout') }}</h1>
    </x-slot>

    <form method="POST" action="{{ route('checkout.store') }}" class="grid gap-6 lg:grid-cols-3">
        @csrf

        <div class="card space-y-4 p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-ink-900 dark:text-white">{{ __('shop.contact_details') }}</h2>

            <div>
                <x-input-label for="customer_name" :value="__('shop.full_name')" />
                <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full"
                              :value="old('customer_name', auth()->user()->name)" required />
                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="customer_email" :value="__('shop.email')" />
                <x-text-input id="customer_email" name="customer_email" type="email" class="mt-1 block w-full"
                              :value="old('customer_email', auth()->user()->email)" required />
                <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="customer_phone" :value="__('shop.phone')" />
                <x-text-input id="customer_phone" name="customer_phone" type="tel" class="mt-1 block w-full"
                              :value="old('customer_phone', auth()->user()->phone)" />
                <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" :value="__('shop.notes')" />
                <textarea id="notes" name="notes" rows="3" class="form-input-field mt-1">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
        </div>

        <div class="card h-fit p-6">
            <h2 class="text-lg font-semibold text-ink-900 dark:text-white">{{ __('shop.order_summary') }}</h2>

            <ul class="mt-4 divide-y divide-ink-100 text-sm dark:divide-ink-800">
                @foreach ($cart->items() as $item)
                    <li class="flex justify-between gap-2 py-2">
                        <span class="text-ink-700 dark:text-ink-300">{{ $item->name() }} × {{ $item->quantity }}</span>
                        <span class="font-medium text-ink-900 dark:text-white">{{ $item->formattedTotal() }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4 flex justify-between border-t border-ink-100 pt-3 dark:border-ink-800">
                <span class="font-semibold text-ink-900 dark:text-white">{{ __('shop.total') }}</span>
                <span class="font-bold text-ink-900 dark:text-white">{{ $cart->formattedSubtotal() }}</span>
            </div>

            <button type="submit" class="btn-primary mt-6 w-full">{{ __('shop.place_order') }}</button>

            <p class="mt-3 text-xs leading-relaxed text-ink-500 dark:text-ink-400">{{ __('shop.pay_with_thawani') }}</p>
        </div>
    </form>
</x-app-layout>
