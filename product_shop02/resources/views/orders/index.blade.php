<x-app-layout>
    <x-slot name="title">{{ __('shop.orders') }}</x-slot>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-black tracking-tight text-ink-900 dark:text-white">{{ __('shop.my_orders') }}</h1>
        <a href="{{ route('orders.create') }}" class="btn-primary px-5 py-2.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" /></svg>
            {{ __('shop.new_order') }}
        </a>
    </div>

    {{-- Filters --}}
    <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('orders.index', array_filter(['q' => $q])) }}"
               class="badge px-3 py-1.5 {{ $status ? 'bg-white text-ink-600 dark:bg-ink-900 dark:text-ink-300' : 'bg-brand-600 text-white' }}">
                {{ __('common.all') }} · {{ number_format(array_sum($counts)) }}
            </a>
            @foreach ($statusOptions as $value => $label)
                <a href="{{ route('orders.index', array_filter(['status' => $value, 'q' => $q])) }}"
                   class="badge px-3 py-1.5 {{ $status === $value ? 'bg-brand-600 text-white' : 'bg-white text-ink-600 dark:bg-ink-900 dark:text-ink-300' }}">
                    {{ $label }} · {{ number_format($counts[$value] ?? 0) }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('orders.index') }}" class="flex gap-2">
            @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('shop.search_placeholder') }}"
                   class="form-input-field w-full py-2 text-sm sm:w-72" />
            <button class="btn-secondary px-4 py-2 text-sm">{{ __('common.search') }}</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="card mt-5 overflow-hidden">
        @if ($orders->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-medium text-ink-700 dark:text-ink-200">{{ __('shop.no_orders') }}</p>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('shop.no_orders_hint') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-ink-200/70 dark:divide-ink-800/70">
                    <thead class="bg-ink-50/60 dark:bg-ink-900/40">
                        <tr>
                            <th class="table-th">{{ __('shop.tracker_code') }}</th>
                            <th class="table-th">{{ __('shop.customer') }}</th>
                            <th class="table-th">{{ __('shop.item') }}</th>
                            <th class="table-th">{{ __('shop.price') }}</th>
                            <th class="table-th">{{ __('shop.source') }}</th>
                            <th class="table-th">{{ __('shop.status') }}</th>
                            <th class="table-th">{{ __('shop.received_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-200/70 dark:divide-ink-800/70">
                        @foreach ($orders as $order)
                            <tr class="cursor-pointer transition hover:bg-ink-50 dark:hover:bg-ink-800/40" onclick="window.location='{{ route('orders.show', $order) }}'">
                                <td class="table-td font-mono font-semibold text-brand-600 dark:text-brand-400">{{ $order->tracker_code }}</td>
                                <td class="table-td">
                                    <div class="font-medium text-ink-900 dark:text-white">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-ink-500 dark:text-ink-400" dir="ltr">{{ $order->customer_phone }}</div>
                                </td>
                                <td class="table-td max-w-[16rem] truncate">{{ $order->item_description }}</td>
                                <td class="table-td">{{ $order->formattedPrice() }}</td>
                                <td class="table-td"><span class="badge {{ $order->source->color() }}">{{ $order->source->label() }}</span></td>
                                <td class="table-td"><x-status-badge :status="$order->status" /></td>
                                <td class="table-td text-ink-500 dark:text-ink-400">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</x-app-layout>
