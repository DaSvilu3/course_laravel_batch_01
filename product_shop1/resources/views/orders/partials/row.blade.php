{{-- One merchant-order row. Expects $order and $statuses (MerchantOrderStatus[]). --}}
<tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
    <td class="table-td">
        <a href="{{ route('merchant.orders.show', $order) }}" dir="ltr"
           class="font-mono text-xs font-semibold text-brand-600 hover:text-brand-500 hover:underline dark:text-brand-400">{{ $order->tracker_code }}</a>
        <p class="mt-0.5 text-xs text-ink-400 dark:text-ink-500">{{ $order->created_at?->isoFormat('D MMM') }}</p>
    </td>
    <td class="table-td">
        <p class="font-medium text-ink-900 dark:text-white">{{ $order->customer_name }}</p>
        <p dir="ltr" class="text-start text-xs text-ink-500 dark:text-ink-400">{{ $order->customer_phone }}</p>
    </td>
    <td class="table-td max-w-[16rem]">
        <p class="flex items-center gap-1.5 truncate text-ink-700 dark:text-ink-200">
            @if ($order->image_path)
                <svg class="h-3.5 w-3.5 shrink-0 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.16-5.16a2.25 2.25 0 0 1 3.18 0l5.16 5.16m-1.5-1.5 1.41-1.41a2.25 2.25 0 0 1 3.18 0l2.16 2.16M3.75 4.5h16.5a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Z" /></svg>
            @endif
            <span class="truncate">{{ $order->item_description }}</span>
        </p>
        <p class="text-xs text-ink-500 dark:text-ink-400">
            {{ __('orders.qty', ['count' => $order->quantity]) }}
            @if ($order->formattedAmount())
                · <span class="font-medium text-ink-700 dark:text-ink-300">{{ $order->formattedAmount() }}</span>
            @endif
        </p>
    </td>
    <td class="table-td">
        <x-status-badge :status="$order->status" />
    </td>
    <td class="table-td">
        <form method="POST" action="{{ route('merchant.orders.update', $order) }}">
            @csrf
            @method('PATCH')
            <select name="status" onchange="this.form.submit()"
                    class="form-input-field w-auto py-1.5 pe-8 ps-3 text-xs">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </form>
    </td>
</tr>
