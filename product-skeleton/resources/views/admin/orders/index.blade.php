<x-admin-layout>
    <x-slot name="header">{{ __('admin.orders') }}</x-slot>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
        <input type="search" name="q" value="{{ request('q') }}"
               placeholder="{{ __('common.search') }}" class="rounded-lg border-gray-300 text-sm">
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach (App\Enums\OrderStatus::options() as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
        <a href="{{ route('admin.orders.index') }}" class="btn-secondary text-xs">{{ __('common.reset') }}</a>
    </form>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-th">{{ __('shop.order_number') }}</th>
                    <th class="table-th">{{ __('admin.customer') }}</th>
                    <th class="table-th">{{ __('admin.items') }}</th>
                    <th class="table-th">{{ __('shop.total') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('common.date') }}</th>
                    <th class="table-th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                    <tr>
                        <td class="table-td font-mono">{{ $order->number }}</td>
                        <td class="table-td">
                            {{ $order->customer_name }}
                            <span class="block text-xs text-gray-500">{{ $order->customer_email }}</span>
                        </td>
                        <td class="table-td">{{ $order->items_count }}</td>
                        <td class="table-td font-semibold">{{ $order->formattedTotal() }}</td>
                        <td class="table-td"><x-status-badge :status="$order->status" /></td>
                        <td class="table-td">{{ $order->created_at->format('Y-m-d') }}</td>
                        <td class="table-td">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-700 hover:underline">
                                {{ __('shop.view_order') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-sm text-gray-500">{{ __('admin.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</x-admin-layout>
