<x-admin-layout>
    <x-slot name="header">{{ __('admin.dashboard') }}</x-slot>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            'revenue' => App\Support\Money::format($stats['revenue']),
            'total_orders' => $stats['orders'],
            'pending_orders' => $stats['pending_orders'],
            'customers' => $stats['customers'],
            'services' => $stats['services'],
            'products' => $stats['products'],
        ] as $key => $value)
            <div class="card p-5">
                <p class="text-sm text-gray-500">{{ __('admin.'.$key) }}</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('admin.recent_orders') }}</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-700 hover:underline">
                    {{ __('admin.orders') }}
                </a>
            </div>

            @forelse ($recentOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}"
                   class="flex items-center justify-between border-b border-gray-100 py-3 last:border-0 hover:text-brand-700">
                    <span>
                        <span class="font-mono text-sm">{{ $order->number }}</span>
                        <span class="block text-xs text-gray-500">{{ $order->customer_name }}</span>
                    </span>
                    <span class="flex items-center gap-3 text-sm">
                        {{ $order->formattedTotal() }}
                        <x-status-badge :status="$order->status" />
                    </span>
                </a>
            @empty
                <p class="text-sm text-gray-500">{{ __('admin.no_records') }}</p>
            @endforelse
        </div>

        <div class="card p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('admin.upcoming_bookings') }}</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-brand-700 hover:underline">
                    {{ __('admin.bookings') }}
                </a>
            </div>

            @forelse ($upcomingBookings as $booking)
                <div class="flex items-center justify-between border-b border-gray-100 py-3 last:border-0">
                    <span>
                        <span class="text-sm">{{ $booking->service?->name }}</span>
                        <span class="block text-xs text-gray-500">{{ $booking->user?->name }}</span>
                    </span>
                    <span class="text-sm text-gray-500">{{ $booking->starts_at?->format('Y-m-d H:i') }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('admin.no_records') }}</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
