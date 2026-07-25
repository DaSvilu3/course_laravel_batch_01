<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('orders.all_orders') }}</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.dashboard_subtitle') }}</p>
    </x-slot>

    {{-- ---- Status filter chips ---- --}}
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('merchant.orders.index') }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ! $filter ? 'bg-brand-600 text-white shadow-soft' : 'border border-ink-200 text-ink-600 hover:border-ink-300 dark:border-ink-700 dark:text-ink-300' }}">
            {{ __('orders.filter_all') }}
        </a>
        @foreach ($statuses as $status)
            <a href="{{ route('merchant.orders.index', ['status' => $status->value]) }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ $filter === $status->value ? 'bg-brand-600 text-white shadow-soft' : 'border border-ink-200 text-ink-600 hover:border-ink-300 dark:border-ink-700 dark:text-ink-300' }}">
                {{ $status->label() }}
            </a>
        @endforeach
    </div>

    <div class="card overflow-hidden">
        @if ($orders->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-semibold text-ink-900 dark:text-white">{{ __('orders.no_orders_yet') }}</p>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.no_orders_hint') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead class="bg-ink-50 dark:bg-ink-900/60">
                        <tr>
                            <th class="table-th">{{ __('orders.col_code') }}</th>
                            <th class="table-th">{{ __('orders.col_customer') }}</th>
                            <th class="table-th">{{ __('orders.col_item') }}</th>
                            <th class="table-th">{{ __('orders.col_status') }}</th>
                            <th class="table-th">{{ __('orders.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                        @foreach ($orders as $order)
                            @include('orders.partials.row', ['order' => $order, 'statuses' => $statuses])
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($orders->hasPages())
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-app-layout>
