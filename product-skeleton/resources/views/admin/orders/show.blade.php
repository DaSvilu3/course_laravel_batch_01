<x-admin-layout>
    <x-slot name="header">{{ __('shop.order') }} {{ $order->number }}</x-slot>

    <a href="{{ route('admin.orders.index') }}" class="mb-4 inline-block text-sm text-brand-700 hover:underline">
        &larr; {{ __('admin.back_to_list') }}
    </a>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="font-semibold text-gray-900">{{ __('admin.order_items') }}</h2>
            </div>

            <table class="min-w-full divide-y divide-gray-100">
                <tbody class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="table-td">
                                {{ $item->name }}
                                <span class="block text-xs text-gray-500">{{ $item->purchasable_type }}</span>
                            </td>
                            <td class="table-td">{{ $item->formattedUnitPrice() }} × {{ $item->quantity }}</td>
                            <td class="table-td text-end font-semibold">{{ $item->formattedTotal() }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="table-td font-semibold" colspan="2">{{ __('shop.total') }}</td>
                        <td class="table-td text-end font-bold text-brand-700">{{ $order->formattedTotal() }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="mb-3 font-semibold text-gray-900">{{ __('admin.update_status') }}</h2>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-input-field">
                        @foreach (App\Enums\OrderStatus::options() as $value => $label)
                            <option value="{{ $value }}" @selected($order->status->value === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary text-xs">{{ __('common.save') }}</button>
                </form>
            </div>

            <div class="card p-6">
                <h2 class="mb-3 font-semibold text-gray-900">{{ __('admin.customer_details') }}</h2>
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-gray-500">{{ __('admin.name') }}</dt><dd>{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('admin.email') }}</dt><dd>{{ $order->customer_email }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('admin.phone') }}</dt><dd>{{ $order->customer_phone ?? __('common.none') }}</dd></div>
                    @if ($order->notes)
                        <div><dt class="text-gray-500">{{ __('shop.notes') }}</dt><dd class="whitespace-pre-line">{{ $order->notes }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="card p-6">
                <h2 class="mb-3 font-semibold text-gray-900">{{ __('admin.payments') }}</h2>

                @forelse ($order->payments as $payment)
                    <div class="border-b border-gray-100 py-2 text-xs last:border-0">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                            <x-status-badge :status="$payment->status" />
                        </div>
                        <div class="mt-1 font-mono text-[11px] text-gray-400">{{ $payment->session_id }}</div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('admin.no_records') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
