<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('common.dashboard') }}</h1>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-sm text-gray-500">{{ __('shop.my_orders') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $ordersCount }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-gray-500">{{ __('admin.revenue') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ App\Support\Money::format($spent) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-gray-500">{{ __('shop.my_bookings') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $bookings->count() }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('shop.my_orders') }}</h2>

            @forelse ($orders as $order)
                <a href="{{ route('orders.show', $order) }}"
                   class="flex items-center justify-between border-b border-gray-100 py-3 last:border-0 hover:text-brand-700">
                    <span class="font-mono text-sm">{{ $order->number }}</span>
                    <span class="flex items-center gap-3 text-sm">
                        {{ $order->formattedTotal() }}
                        <x-status-badge :status="$order->status" />
                    </span>
                </a>
            @empty
                <p class="text-sm text-gray-500">{{ __('shop.no_orders') }}</p>
            @endforelse
        </div>

        <div class="card p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ __('shop.my_bookings') }}</h2>

            @forelse ($bookings as $booking)
                <div class="flex items-center justify-between border-b border-gray-100 py-3 last:border-0">
                    <span class="text-sm">{{ $booking->service?->name }}</span>
                    <span class="text-sm text-gray-500">
                        {{ $booking->starts_at?->format('Y-m-d H:i') ?? __('shop.unscheduled') }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('shop.no_bookings') }}</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
