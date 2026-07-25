<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('shop.my_orders') }}</h1>
    </x-slot>

    @if ($orders->isEmpty())
        <div class="rounded-2xl border border-dashed border-ink-300 py-16 text-center text-ink-500 dark:border-ink-700 dark:text-ink-400">{{ __('shop.no_orders') }}</div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-ink-50 dark:bg-ink-900/60">
                        <tr>
                            <th class="table-th">{{ __('shop.order_number') }}</th>
                            <th class="table-th">{{ __('shop.order_date') }}</th>
                            <th class="table-th">{{ __('shop.items') }}</th>
                            <th class="table-th">{{ __('shop.total') }}</th>
                            <th class="table-th">{{ __('shop.order_status') }}</th>
                            <th class="table-th"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                        @foreach ($orders as $order)
                            <tr class="transition hover:bg-ink-50 dark:hover:bg-ink-800/50">
                                <td class="table-td font-mono text-ink-900 dark:text-white">{{ $order->number }}</td>
                                <td class="table-td">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="table-td">{{ $order->items_count }}</td>
                                <td class="table-td font-semibold text-ink-900 dark:text-white">{{ $order->formattedTotal() }}</td>
                                <td class="table-td"><x-status-badge :status="$order->status" /></td>
                                <td class="table-td">
                                    <a href="{{ route('orders.show', $order) }}" class="font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">
                                        {{ __('shop.view_order') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-app-layout>
