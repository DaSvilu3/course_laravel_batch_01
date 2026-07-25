<x-admin-layout>
    <x-slot name="header">{{ __('admin.dashboard') }}</x-slot>

    @php
        $totalSubscribers = $planBreakdown->sum('subscribers');
    @endphp

    {{-- ---- Stat tiles ---------------------------------------------------- --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('admin.mrr') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow-glow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l5-5 4 4 8-8M21 8v5m0-5h-5" /></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-black text-ink-900 dark:text-white">{{ App\Support\Money::format($stats['mrr']) }}</p>
            <p class="mt-0.5 text-xs text-ink-400 dark:text-ink-500">{{ __('admin.mrr_hint') }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('admin.total_revenue') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m4-14H9.5a2.5 2.5 0 0 0 0 5h5a2.5 2.5 0 0 1 0 5H8" /></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-black text-ink-900 dark:text-white">{{ App\Support\Money::format($stats['revenue']) }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('admin.active_subscriptions') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.5l2 2 4-4.5M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Z" /></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-black text-ink-900 dark:text-white">{{ $stats['active'] }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('admin.trialing') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-black text-ink-900 dark:text-white">{{ $stats['trialing'] }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('admin.customers') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M10 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm11 10v-2a4 4 0 0 0-3-3.87M16 4.13A4 4 0 0 1 16 12" /></svg>
                </span>
            </div>
            <p class="mt-3 text-2xl font-black text-ink-900 dark:text-white">{{ $stats['customers'] }}</p>
        </div>
    </div>

    {{-- ---- Plan breakdown + recent subscriptions ------------------------ --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="card p-6">
            <h2 class="mb-4 text-lg font-semibold text-ink-900 dark:text-white">{{ __('admin.plan_breakdown') }}</h2>
            @forelse ($planBreakdown as $row)
                <div class="mb-4 last:mb-0">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-ink-800 dark:text-ink-200">{{ $row->plan_name }}</span>
                        <span class="text-ink-500 dark:text-ink-400">{{ $row->subscribers }}</span>
                    </div>
                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500"
                             style="width: {{ $totalSubscribers > 0 ? round($row->subscribers / $totalSubscribers * 100) : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_subscriptions') }}</p>
            @endforelse
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <h2 class="border-b border-ink-100 px-6 py-4 text-lg font-semibold text-ink-900 dark:border-ink-800 dark:text-white">{{ __('admin.recent_subscriptions') }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                        @forelse ($recentSubscriptions as $sub)
                            <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                                <td class="table-td">
                                    <p class="font-medium text-ink-900 dark:text-white">{{ $sub->user?->name }}</p>
                                    <p class="text-xs text-ink-500 dark:text-ink-400">{{ $sub->user?->email }}</p>
                                </td>
                                <td class="table-td">{{ $sub->plan_name }}</td>
                                <td class="table-td">{{ $sub->formattedPrice() }}</td>
                                <td class="table-td"><x-status-badge :status="$sub->status" /></td>
                                <td class="table-td text-ink-400 dark:text-ink-500">{{ $sub->created_at?->isoFormat('LL') }}</td>
                            </tr>
                        @empty
                            <tr><td class="table-td py-8 text-center text-ink-500 dark:text-ink-400">{{ __('admin.no_subscriptions') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ---- Recent payments ---------------------------------------------- --}}
    <div class="card mt-6 overflow-hidden">
        <h2 class="border-b border-ink-100 px-6 py-4 text-lg font-semibold text-ink-900 dark:border-ink-800 dark:text-white">{{ __('admin.recent_payments') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                    @forelse ($recentPayments as $payment)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                            <td class="table-td font-mono">{{ $payment->reference() }}</td>
                            <td class="table-td">{{ $payment->user?->name }}</td>
                            <td class="table-td font-semibold text-ink-900 dark:text-white">{{ $payment->formattedAmount() }}</td>
                            <td class="table-td"><span class="badge {{ $payment->status->color() }}">{{ $payment->status->label() }}</span></td>
                            <td class="table-td text-ink-400 dark:text-ink-500">{{ $payment->paid_at?->isoFormat('LL') }}</td>
                        </tr>
                    @empty
                        <tr><td class="table-td py-8 text-center text-ink-500 dark:text-ink-400">{{ __('admin.no_payments') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
