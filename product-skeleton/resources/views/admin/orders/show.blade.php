<x-admin-layout>
    <x-slot name="header">{{ __('shop.order') }} {{ $order->number }}</x-slot>

    <a href="{{ route('admin.orders.index') }}" class="mb-4 inline-block text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
        &larr; {{ __('admin.back_to_list') }}
    </a>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-ink-200 px-6 py-4 dark:border-ink-800">
                <h2 class="font-semibold text-ink-900 dark:text-white">{{ __('admin.order_items') }}</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="table-td">
                                    {{ $item->name }}
                                    <span class="block text-xs text-ink-500 dark:text-ink-400">{{ $item->purchasable_type }}</span>
                                </td>
                                <td class="table-td">{{ $item->formattedUnitPrice() }} × {{ $item->quantity }}</td>
                                <td class="table-td text-end font-semibold">{{ $item->formattedTotal() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-ink-50 dark:bg-ink-900/60">
                        <tr>
                            <td class="table-td font-semibold" colspan="2">{{ __('shop.total') }}</td>
                            <td class="table-td text-end font-bold text-brand-600 dark:text-brand-400">{{ $order->formattedTotal() }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="mb-3 font-semibold text-ink-900 dark:text-white">{{ __('admin.update_status') }}</h2>

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
                <h2 class="mb-3 font-semibold text-ink-900 dark:text-white">{{ __('admin.customer_details') }}</h2>
                <dl class="space-y-2 text-sm text-ink-700 dark:text-ink-300">
                    <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.name') }}</dt><dd>{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.email') }}</dt><dd>{{ $order->customer_email }}</dd></div>
                    <div><dt class="text-ink-500 dark:text-ink-400">{{ __('admin.phone') }}</dt><dd>{{ $order->customer_phone ?? __('common.none') }}</dd></div>
                    @if ($order->notes)
                        <div><dt class="text-ink-500 dark:text-ink-400">{{ __('shop.notes') }}</dt><dd class="whitespace-pre-line">{{ $order->notes }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="card p-6">
                <h2 class="mb-3 font-semibold text-ink-900 dark:text-white">{{ __('admin.payments') }}</h2>

                @forelse ($order->payments as $payment)
                    <div class="border-b border-ink-100 py-2 text-xs last:border-0 dark:border-ink-800">
                        <div class="flex items-center justify-between">
                            <span class="text-ink-500 dark:text-ink-400">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                            <x-status-badge :status="$payment->status" />
                        </div>
                        <div class="mt-1 font-mono text-[11px] text-ink-400 dark:text-ink-500">{{ $payment->session_id }}</div>
                    </div>
                @empty
                    <p class="text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
