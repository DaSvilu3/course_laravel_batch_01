<x-admin-layout>
    <x-slot name="header">{{ __('admin.plans') }}</x-slot>

    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.plans.create') }}" class="btn-primary text-xs">{{ __('admin.new_plan') }}</a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-ink-50 dark:bg-ink-900/60">
                    <tr>
                        <th class="table-th">{{ __('admin.name') }}</th>
                        <th class="table-th">{{ __('billing.price') }}</th>
                        <th class="table-th">{{ __('admin.interval') }}</th>
                        <th class="table-th">{{ __('admin.trial_days') }}</th>
                        <th class="table-th">{{ __('admin.subscribers') }}</th>
                        <th class="table-th">{{ __('common.status') }}</th>
                        <th class="table-th">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                    @forelse ($plans as $plan)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                            <td class="table-td font-medium text-ink-900 dark:text-white">
                                {{ $plan->name }}
                                @if ($plan->is_featured)
                                    <span class="badge bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">{{ __('billing.featured') }}</span>
                                @endif
                            </td>
                            <td class="table-td">{{ $plan->formattedPrice() }}</td>
                            <td class="table-td">{{ $plan->interval->label() }}</td>
                            <td class="table-td">{{ $plan->trial_days ?: __('common.none') }}</td>
                            <td class="table-td">{{ $plan->subscriptions_count }}</td>
                            <td class="table-td">
                                <span class="badge {{ $plan->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300' }}">
                                    {{ $plan->is_active ? __('common.active') : __('common.inactive') }}
                                </span>
                            </td>
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.plans.edit', $plan) }}" class="font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
                                        {{ __('common.edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                                          onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-rose-600 hover:text-rose-500 dark:text-rose-400">{{ __('common.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $plans->links() }}</div>
</x-admin-layout>
