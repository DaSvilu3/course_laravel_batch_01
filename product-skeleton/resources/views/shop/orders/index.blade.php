<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('shop.my_orders') }}</h1>
    </x-slot>

    @if ($orders->isEmpty())
        <div class="card p-12 text-center text-gray-500">{{ __('shop.no_orders') }}</div>
    @else
        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-th">{{ __('shop.order_number') }}</th>
                        <th class="table-th">{{ __('shop.order_date') }}</th>
                        <th class="table-th">{{ __('shop.items') }}</th>
                        <th class="table-th">{{ __('shop.total') }}</th>
                        <th class="table-th">{{ __('shop.order_status') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        <tr>
                            <td class="table-td font-mono">{{ $order->number }}</td>
                            <td class="table-td">{{ $order->created_at->format('Y-m-d') }}</td>
                            <td class="table-td">{{ $order->items_count }}</td>
                            <td class="table-td font-semibold">{{ $order->formattedTotal() }}</td>
                            <td class="table-td"><x-status-badge :status="$order->status" /></td>
                            <td class="table-td">
                                <a href="{{ route('orders.show', $order) }}" class="text-brand-700 hover:underline">
                                    {{ __('shop.view_order') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-app-layout>
