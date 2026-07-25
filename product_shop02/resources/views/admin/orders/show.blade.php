<x-admin-layout>
    <x-slot name="header">{{ __('shop.order') }} {{ $order->tracker_code }}</x-slot>

    @php($regions = \App\Support\Regions::class)

    <a href="{{ route('admin.orders.index') }}" class="mb-4 inline-block text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
        &larr; {{ __('admin.back_to_list') }}
    </a>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-ink-900 dark:text-white">{{ __('admin.order_details') }}</h2>
                <div class="flex items-center gap-2">
                    <span class="badge {{ $order->source->color() }}">{{ $order->source->label() }}</span>
                    <x-status-badge :status="$order->status" />
                </div>
            </div>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.tracker_code') }}</dt><dd class="font-mono font-semibold text-ink-900 dark:text-white">{{ $order->tracker_code }}</dd></div>
                <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.merchant') }}</dt><dd class="font-medium text-ink-900 dark:text-white">{{ $order->user?->store_name ?? $order->user?->name }}</dd></div>
                <div><dt class="text-ink-500 dark:text-ink-400">{{ __('shop.item') }}</dt><dd class="text-ink-900 dark:text-white">{{ $order->item_description }} × {{ $order->quantity }}</dd></div>
                <div><dt class="text-ink-500 dark:text-ink-400">{{ __('shop.price') }}</dt><dd class="font-medium text-ink-900 dark:text-white">{{ $order->formattedPrice() }}</dd></div>
                @if ($order->payment_method)
                    <div><dt class="text-ink-500 dark:text-ink-400">{{ __('shop.payment_method') }}</dt><dd class="text-ink-900 dark:text-white">{{ $order->payment_method->label() }}</dd></div>
                @endif
                <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.delivery') }}</dt><dd class="text-ink-900 dark:text-white">{{ $regions::countryLabel($order->country) }}@if ($order->wilayat) — {{ $regions::wilayatLabel($order->wilayat) }}@endif</dd></div>
                @if ($order->address)
                    <div class="sm:col-span-2"><dt class="text-ink-500 dark:text-ink-400">{{ __('shop.address') }}</dt><dd class="text-ink-900 dark:text-white">{{ $order->address }}</dd></div>
                @endif
                @if ($order->notes)
                    <div class="sm:col-span-2"><dt class="text-ink-500 dark:text-ink-400">{{ __('shop.notes') }}</dt><dd class="whitespace-pre-line text-ink-900 dark:text-white">{{ $order->notes }}</dd></div>
                @endif
            </dl>

            @if ($order->attachment_path)
                <div class="mt-5">
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($order->attachment_path) }}" target="_blank" rel="noopener">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($order->attachment_path) }}" alt="" class="max-h-48 rounded-xl border border-ink-200 dark:border-ink-800" />
                    </a>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="mb-3 font-semibold text-ink-900 dark:text-white">{{ __('admin.update_status') }}</h2>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="form-input-field">
                        @foreach (App\Enums\OrderStatus::options() as $value => $label)
                            <option value="{{ $value }}" @selected($order->status->value === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary text-xs">{{ __('common.save') }}</button>
                </form>
            </div>

            <div class="card p-6">
                <h2 class="mb-3 font-semibold text-ink-900 dark:text-white">{{ __('admin.customer_details') }}</h2>
                <dl class="space-y-2 text-sm text-ink-700 dark:text-ink-300">
                    <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.name') }}</dt><dd>{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.phone') }}</dt><dd dir="ltr">{{ $order->customer_phone }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</x-admin-layout>
