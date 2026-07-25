<x-admin-layout>
    <x-slot name="header">{{ __('admin.payments') }}</x-slot>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
        <input type="search" name="q" value="{{ request('q') }}"
               placeholder="{{ __('admin.session_id') }}" class="form-input-field w-auto text-sm">
        <select name="status" class="form-input-field w-auto text-sm">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach (App\Enums\PaymentStatus::options() as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-ink-50 dark:bg-ink-900/60">
                    <tr>
                        <th class="table-th">{{ __('admin.reference') }}</th>
                        <th class="table-th">{{ __('admin.customer') }}</th>
                        <th class="table-th">{{ __('admin.gateway') }}</th>
                        <th class="table-th">{{ __('admin.amount') }}</th>
                        <th class="table-th">{{ __('common.status') }}</th>
                        <th class="table-th">{{ __('admin.reference') }}</th>
                        <th class="table-th">{{ __('common.date') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                            <td class="table-td">
                                @if ($payment->order)
                                    <a href="{{ route('admin.orders.show', $payment->order) }}"
                                       class="font-mono text-brand-600 hover:text-brand-500 dark:text-brand-400">
                                        {{ $payment->order->number }}
                                    </a>
                                @else
                                    <span class="font-mono">{{ $payment->reference() }}</span>
                                @endif
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
                                        <button class="font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">{{ __('admin.verify') }}</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $payments->links() }}</div>
</x-admin-layout>
