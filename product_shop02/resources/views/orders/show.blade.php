<x-app-layout>
    <x-slot name="title">{{ $order->tracker_code }}</x-slot>

    @php($regions = \App\Support\Regions::class)

    <div class="mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('orders.index') }}" class="btn-ghost px-2.5 py-2">
                    <svg class="h-5 w-5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-mono text-2xl font-black tracking-tight text-ink-900 dark:text-white">{{ $order->tracker_code }}</h1>
                        <x-status-badge :status="$order->status" />
                    </div>
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                        {{ $order->created_at->translatedFormat('d M Y — g:i A') }} ·
                        <span class="badge {{ $order->source->color() }}">{{ $order->source->label() }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Status actions --}}
        <div class="card mt-6 flex flex-wrap items-center gap-3 p-4">
            <span class="text-sm font-semibold text-ink-700 dark:text-ink-200">{{ __('shop.update_status') }}:</span>
            @if ($order->status === \App\Enums\OrderStatus::New)
                <form method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button class="btn-primary px-4 py-2 text-sm">{{ __('shop.mark_in_progress') }}</button>
                </form>
            @endif
            @if ($order->status === \App\Enums\OrderStatus::InProgress)
                <form method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button class="btn-primary px-4 py-2 text-sm">{{ __('shop.mark_completed') }}</button>
                </form>
            @endif
            @if ($order->status->isOpen())
                <form method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button class="btn-secondary px-4 py-2 text-sm">{{ __('shop.mark_cancelled') }}</button>
                </form>
            @endif

            <div class="ms-auto flex items-center gap-2">
                <a href="{{ $order->whatsappLink() }}" target="_blank" rel="noopener" class="btn-ghost px-3 py-2 text-sm">{{ __('shop.whatsapp') }}</a>
                <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('{{ __('shop.delete_order_confirm') }}')">
                    @csrf @method('DELETE')
                    <button class="btn-ghost px-3 py-2 text-sm text-rose-600 dark:text-rose-400">{{ __('shop.delete_order') }}</button>
                </form>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            {{-- Order details --}}
            <div class="card p-6 lg:col-span-2">
                <h2 class="font-bold text-ink-900 dark:text-white">{{ __('shop.order_details') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.item') }}</dt>
                        <dd class="text-end font-medium text-ink-900 dark:text-white">{{ $order->item_description }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.quantity') }}</dt>
                        <dd class="font-medium text-ink-900 dark:text-white">{{ $order->quantity }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.price') }}</dt>
                        <dd class="font-medium text-ink-900 dark:text-white">{{ $order->formattedPrice() }}</dd>
                    </div>
                    @if ($order->payment_method)
                        <div class="flex justify-between gap-4">
                            <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.payment_method') }}</dt>
                            <dd class="font-medium text-ink-900 dark:text-white">{{ $order->payment_method->label() }}</dd>
                        </div>
                    @endif
                    @if ($order->notes)
                        <div class="border-t border-ink-200/70 pt-3 dark:border-ink-800/70">
                            <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.notes') }}</dt>
                            <dd class="mt-1 text-ink-800 dark:text-ink-200">{{ $order->notes }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($order->attachment_path)
                    <div class="mt-5">
                        <p class="mb-2 text-sm text-ink-500 dark:text-ink-400">{{ __('shop.attachment') }}</p>
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($order->attachment_path) }}" target="_blank" rel="noopener">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($order->attachment_path) }}" alt="" class="max-h-56 rounded-xl border border-ink-200 dark:border-ink-800" />
                        </a>
                    </div>
                @endif
            </div>

            {{-- Customer + delivery --}}
            <div class="space-y-6">
                <div class="card p-6">
                    <h2 class="font-bold text-ink-900 dark:text-white">{{ __('shop.customer_details') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.customer_name') }}</dt>
                            <dd class="font-medium text-ink-900 dark:text-white">{{ $order->customer_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.phone') }}</dt>
                            <dd class="font-medium text-ink-900 dark:text-white" dir="ltr">{{ $order->customer_phone }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="card p-6">
                    <h2 class="font-bold text-ink-900 dark:text-white">{{ __('shop.delivery') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.country') }} / {{ __('shop.governorate') }}</dt>
                            <dd class="font-medium text-ink-900 dark:text-white">
                                {{ $regions::countryLabel($order->country) }}
                                @if ($order->wilayat) — {{ $regions::wilayatLabel($order->wilayat) }}@endif
                            </dd>
                        </div>
                        @if ($order->address)
                            <div>
                                <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.address') }}</dt>
                                <dd class="text-ink-800 dark:text-ink-200">{{ $order->address }}</dd>
                            </div>
                        @endif
                        @if ($order->location_note)
                            <div>
                                <dt class="text-ink-500 dark:text-ink-400">{{ __('shop.location_note') }}</dt>
                                <dd><a href="{{ $order->location_note }}" target="_blank" rel="noopener" class="text-brand-600 hover:underline dark:text-brand-400" dir="ltr">{{ $order->location_note }}</a></dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
