<x-admin-layout>
    <x-slot name="header">{{ __('admin.subscriptions') }}</x-slot>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
        <input type="search" name="q" value="{{ request('q') }}"
               placeholder="{{ __('common.search') }}" class="form-input-field w-auto text-sm">
        <select name="status" class="form-input-field w-auto text-sm">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach (App\Enums\SubscriptionStatus::options() as $value => $label)
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
                        <th class="table-th">{{ __('admin.customer') }}</th>
                        <th class="table-th">{{ __('billing.plan') }}</th>
                        <th class="table-th">{{ __('billing.price') }}</th>
                        <th class="table-th">{{ __('common.status') }}</th>
                        <th class="table-th">{{ __('admin.started_at') }}</th>
                        <th class="table-th">{{ __('admin.ends_at') }}</th>
                        <th class="table-th">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                    @forelse ($subscriptions as $subscription)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                            <td class="table-td">
                                {{ $subscription->user?->name }}
                                <span class="block text-xs text-ink-500 dark:text-ink-400">{{ $subscription->user?->email }}</span>
                            </td>
                            <td class="table-td font-medium text-ink-900 dark:text-white">{{ $subscription->plan_name }}</td>
                            <td class="table-td">{{ $subscription->formattedPrice() }}</td>
                            <td class="table-td"><x-status-badge :status="$subscription->status" /></td>
                            <td class="table-td">{{ $subscription->starts_at?->format('Y-m-d') ?? __('common.none') }}</td>
                            <td class="table-td">{{ $subscription->ends_at?->format('Y-m-d') ?? __('common.none') }}</td>
                            <td class="table-td">
                                @if ($subscription->isActive())
                                    <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription) }}"
                                          onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                        @csrf
                                        <button class="font-semibold text-rose-600 hover:text-rose-500 dark:text-rose-400">{{ __('admin.cancel_subscription') }}</button>
                                    </form>
                                @else
                                    <span class="text-ink-400 dark:text-ink-500">{{ __('common.none') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $subscriptions->links() }}</div>
</x-admin-layout>
