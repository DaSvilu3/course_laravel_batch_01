<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">
                    {{ __('common.welcome_back', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('common.dashboard') }}</p>
            </div>
        </div>
    </x-slot>

    {{-- ---- Stat cards ------------------------------------------------ --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('shop.my_orders') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-black text-ink-900 dark:text-white">{{ $ordersCount }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('admin.revenue') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m4-14H9.5a2.5 2.5 0 0 0 0 5h5a2.5 2.5 0 0 1 0 5H8" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-black text-ink-900 dark:text-white">{{ App\Support\Money::format($spent) }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('billing.my_subscription') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.09 4.26L19 8l-3.5 3.4.8 4.8L12 14l-4.3 2.2.8-4.8L5 8l4.91-.74L12 3z" />
                    </svg>
                </span>
            </div>
            @if ($subscription)
                <p class="mt-3 text-xl font-bold text-ink-900 dark:text-white">{{ $subscription->plan_name }}</p>
                <a href="{{ route('billing.index') }}" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                    {{ __('billing.billing') }}
                    <svg class="h-3.5 w-3.5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <a href="{{ route('plans.index') }}" class="mt-3 inline-flex items-center gap-1 text-lg font-bold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                    {{ __('billing.view_plans') }}
                    <svg class="h-4 w-4 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
    </div>

    {{-- ---- Lists ----------------------------------------------------- --}}
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="mb-4 text-lg font-semibold text-ink-900 dark:text-white">{{ __('shop.my_orders') }}</h2>

            @forelse ($orders as $order)
                <a href="{{ route('orders.show', $order) }}"
                   class="-mx-2 flex items-center justify-between rounded-xl px-2 py-3 transition hover:bg-ink-50 dark:hover:bg-ink-800/60">
                    <span class="font-mono text-sm text-ink-700 dark:text-ink-300">{{ $order->number }}</span>
                    <span class="flex items-center gap-3 text-sm text-ink-800 dark:text-ink-200">
                        {{ $order->formattedTotal() }}
                        <x-status-badge :status="$order->status" />
                    </span>
                </a>
            @empty
                <p class="py-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('shop.no_orders') }}</p>
            @endforelse
        </div>

        <div class="card p-6">
            <h2 class="mb-4 text-lg font-semibold text-ink-900 dark:text-white">{{ __('shop.my_bookings') }}</h2>

            @forelse ($bookings as $booking)
                <div class="-mx-2 flex items-center justify-between rounded-xl px-2 py-3">
                    <span class="text-sm text-ink-800 dark:text-ink-200">{{ $booking->service?->name }}</span>
                    <span class="text-sm text-ink-500 dark:text-ink-400">
                        {{ $booking->starts_at?->format('Y-m-d H:i') ?? __('shop.unscheduled') }}
                    </span>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('shop.no_bookings') }}</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
