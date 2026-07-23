<x-admin-layout>
    <x-slot name="header">{{ __('admin.dashboard') }}</x-slot>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $tiles = [
                'revenue' => [
                    'value' => App\Support\Money::format($stats['revenue']),
                    'chip' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300',
                    'icon' => 'M12 3v18m4-14H9.5a2.5 2.5 0 0 0 0 5h5a2.5 2.5 0 0 1 0 5H8',
                ],
                'total_orders' => [
                    'value' => $stats['orders'],
                    'chip' => 'bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300',
                    'icon' => 'M4 7h16M4 12h16M4 17h10',
                ],
                'pending_orders' => [
                    'value' => $stats['pending_orders'],
                    'chip' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300',
                    'icon' => 'M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                ],
                'customers' => [
                    'value' => $stats['customers'],
                    'chip' => 'bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300',
                    'icon' => 'M15 19.5a3 3 0 0 0-6 0M18 21a6 6 0 0 0-12 0M12 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z',
                ],
                'services' => [
                    'value' => $stats['services'],
                    'chip' => 'bg-sky-50 text-sky-600 dark:bg-sky-900/40 dark:text-sky-300',
                    'icon' => 'M9.5 3.5l1.6 3.9 4.2.3-3.2 2.7 1 4.1L9.5 12l-3.6 2.3 1-4.1L3.7 7.7l4.2-.3 1.6-3.9Z',
                ],
                'products' => [
                    'value' => $stats['products'],
                    'chip' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300',
                    'icon' => 'M3.5 8.5L12 4l8.5 4.5M3.5 8.5v7L12 20m-8.5-11.5L12 13m0 7v-7m0 0l8.5-4.5m0 0v7L12 20',
                ],
            ];
        @endphp
        @foreach ($tiles as $key => $tile)
            <div class="card p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('admin.'.$key) }}</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $tile['chip'] }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tile['icon'] }}" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-black text-ink-900 dark:text-white">{{ $tile['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-ink-900 dark:text-white">{{ __('admin.recent_orders') }}</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                    {{ __('admin.orders') }}
                </a>
            </div>

            @forelse ($recentOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}"
                   class="-mx-2 flex items-center justify-between rounded-xl px-2 py-3 transition hover:bg-ink-50 dark:hover:bg-ink-800/60">
                    <span>
                        <span class="font-mono text-sm text-ink-800 dark:text-ink-200">{{ $order->number }}</span>
                        <span class="block text-xs text-ink-500 dark:text-ink-400">{{ $order->customer_name }}</span>
                    </span>
                    <span class="flex items-center gap-3 text-sm text-ink-800 dark:text-ink-200">
                        {{ $order->formattedTotal() }}
                        <x-status-badge :status="$order->status" />
                    </span>
                </a>
            @empty
                <p class="py-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</p>
            @endforelse
        </div>

        <div class="card p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-ink-900 dark:text-white">{{ __('admin.upcoming_bookings') }}</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                    {{ __('admin.bookings') }}
                </a>
            </div>

            @forelse ($upcomingBookings as $booking)
                <div class="-mx-2 flex items-center justify-between rounded-xl px-2 py-3">
                    <span>
                        <span class="text-sm text-ink-800 dark:text-ink-200">{{ $booking->service?->name }}</span>
                        <span class="block text-xs text-ink-500 dark:text-ink-400">{{ $booking->user?->name }}</span>
                    </span>
                    <span class="text-sm text-ink-500 dark:text-ink-400">{{ $booking->starts_at?->format('Y-m-d H:i') }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
