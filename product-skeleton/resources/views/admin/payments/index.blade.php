<x-admin-layout>
    <x-slot name="header">{{ __('admin.payments') }}</x-slot>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
        <input type="search" name="q" value="{{ request('q') }}"
               placeholder="{{ __('admin.session_id') }}" class="rounded-lg border-gray-300 text-sm">
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach (App\Enums\PaymentStatus::options() as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
    </form>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-th">{{ __('shop.order_number') }}</th>
                    <th class="table-th">{{ __('admin.customer') }}</th>
                    <th class="table-th">{{ __('admin.gateway') }}</th>
                    <th class="table-th">{{ __('admin.amount') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('admin.reference') }}</th>
                    <th class="table-th">{{ __('common.date') }}</th>
                    <th class="table-th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($payments as $payment)
                    <tr>
                        <td class="table-td">
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}"
                               class="font-mono text-brand-700 hover:underline">
                                {{ $payment->order?->number }}
                            </a>
                        </td>
                        <td class="table-td">{{ $payment->user?->name }}</td>
                        <td class="table-td">{{ $payment->gateway }}</td>
                        <td class="table-td font-semibold">{{ $payment->formattedAmount() }}</td>
                        <td class="table-td"><x-status-badge :status="$payment->status" /></td>
                        <td class="table-td font-mono text-xs">{{ $payment->reference ?? __('common.none') }}</td>
                        <td class="table-td">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        <td class="table-td">
                            @unless ($payment->isPaid())
                                <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                                    @csrf
                                    <button class="text-brand-700 hover:underline">{{ __('admin.verify') }}</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-sm text-gray-500">{{ __('admin.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $payments->links() }}</div>
</x-admin-layout>
