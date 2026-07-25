<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if (Auth::user()->logoUrl())
                    <img src="{{ Auth::user()->logoUrl() }}" alt="{{ Auth::user()->displayStoreName() }}"
                         class="h-12 w-12 shrink-0 rounded-2xl object-cover ring-1 ring-ink-900/5 dark:ring-white/10">
                @endif
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">
                        {{ __('common.welcome_back', ['name' => Auth::user()->displayStoreName()]) }}
                    </h1>
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.dashboard_subtitle') }}</p>
                </div>
            </div>
            <a href="{{ route('merchant.orders.index') }}" class="btn-secondary text-sm">{{ __('orders.all_orders') }}</a>
        </div>
    </x-slot>

    @php
        $user = Auth::user();
        $intakeUrl = $user->publicIntakeUrl();
        $atLimit = ! $user->canAcceptOrder();
        $nearLimit = $dailyLimit !== -1 && ! $atLimit && $ordersToday >= max(1, $dailyLimit - 2);
    @endphp

    {{-- ================= Quota alert ================= --}}
    @if ($atLimit)
        <div class="card mb-6 flex flex-wrap items-center justify-between gap-4 border-amber-300/70 bg-amber-50 p-5 dark:border-amber-500/30 dark:bg-amber-500/10">
            <p class="flex items-center gap-2.5 text-sm font-medium text-amber-800 dark:text-amber-200">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                {{ __('orders.quota_reached', ['limit' => $dailyLimit === -1 ? $monthlyLimit : $dailyLimit]) }}
            </p>
            <a href="{{ route('plans.index') }}" class="btn-primary text-sm">{{ __('orders.upgrade') }}</a>
        </div>
    @elseif ($nearLimit)
        <div class="card mb-6 flex items-center gap-2.5 border-amber-200/70 bg-amber-50/60 p-4 text-sm text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/5 dark:text-amber-200">
            <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500"></span>
            {{ __('orders.quota_warning', ['used' => $ordersToday, 'limit' => $dailyLimit]) }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- ================= Intake link card ================= --}}
        <div class="card relative overflow-hidden p-6 lg:col-span-2"
             x-data="{ copied: false, copy() { navigator.clipboard.writeText('{{ $intakeUrl }}').then(() => { this.copied = true; setTimeout(() => this.copied = false, 1800); }); } }">
            <div aria-hidden="true" class="pointer-events-none absolute -end-16 -top-16 h-48 w-48 rounded-full bg-brand-500/10 blur-3xl"></div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-ink-900 dark:text-white">{{ __('orders.intake_link_title') }}</p>
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.intake_link_hint') }}</p>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow-glow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H18a3 3 0 0 1 0 6h-1.5m-9 0H6a3 3 0 0 1 0-6h4.5M8 9h8" /></svg>
                </span>
            </div>

            <div class="relative mt-5 flex items-center gap-2 rounded-xl border border-ink-200/70 bg-ink-50/70 px-3 py-2.5 dark:border-ink-800 dark:bg-ink-950/40">
                <span dir="ltr" class="flex-1 truncate font-mono text-sm text-ink-700 dark:text-ink-200">{{ $intakeUrl }}</span>
                <button type="button" @click="copy()" class="btn-ghost shrink-0 px-3 py-1.5 text-xs">
                    <span x-show="!copied">{{ __('orders.copy') }}</span>
                    <span x-show="copied" x-cloak class="text-emerald-600 dark:text-emerald-400">{{ __('orders.copied') }}</span>
                </button>
            </div>

            <div class="relative mt-4 flex flex-wrap gap-3">
                <a href="https://wa.me/?text={{ urlencode(__('orders.whatsapp_message', ['url' => $intakeUrl])) }}" target="_blank" rel="noopener"
                   class="btn bg-emerald-600 text-white hover:bg-emerald-500 active:translate-y-px">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.75.46 3.45 1.32 4.95L2 22l5.3-1.38a9.9 9.9 0 0 0 4.73 1.2h.01c5.46 0 9.9-4.44 9.9-9.9 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm5.8 14.02c-.25.7-1.44 1.33-1.99 1.37-.53.05-1.02.24-3.42-.72-2.9-1.14-4.73-4.1-4.88-4.29-.14-.19-1.16-1.54-1.16-2.94 0-1.4.73-2.08 1-2.37.24-.26.53-.32.7-.32l.5.01c.16 0 .38-.06.59.45.25.6.83 2.08.9 2.23.07.15.12.32.02.51-.1.19-.15.31-.29.48l-.44.51c-.14.14-.29.3-.12.58.16.29.73 1.2 1.57 1.95 1.08.96 1.99 1.26 2.27 1.4.28.15.44.13.6-.08.16-.19.7-.81.88-1.09.19-.28.37-.23.62-.14.25.09 1.62.76 1.9.9.28.14.46.21.53.33.07.12.07.68-.18 1.37Z"/></svg>
                    {{ __('orders.share_whatsapp') }}
                </a>
                <a href="{{ $intakeUrl }}" target="_blank" rel="noopener" class="btn-secondary">{{ __('orders.open_link') }}</a>
            </div>
        </div>

        {{-- ================= Today's quota gauge ================= --}}
        <div class="card p-6">
            <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('orders.stat_today') }}</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-ink-900 dark:text-white">{{ $ordersToday }}</span>
                <span class="text-sm font-medium text-ink-500 dark:text-ink-400">
                    {{ $dailyLimit === -1 ? __('orders.unlimited') : __('orders.stat_today_of', ['limit' => $dailyLimit]) }}
                </span>
            </div>
            @if ($dailyLimit !== -1)
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500"
                         style="width: {{ min(100, (int) round($ordersToday / max(1, $dailyLimit) * 100)) }}%"></div>
                </div>
            @endif
            <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                <div class="rounded-xl border border-ink-200/70 bg-ink-50/60 p-3 dark:border-ink-800 dark:bg-ink-950/40">
                    <p class="text-lg font-bold text-ink-900 dark:text-white">{{ $openCount }}</p>
                    <p class="text-xs text-ink-500 dark:text-ink-400">{{ __('orders.stat_open') }}</p>
                </div>
                <div class="rounded-xl border border-ink-200/70 bg-ink-50/60 p-3 dark:border-ink-800 dark:bg-ink-950/40">
                    <p class="text-lg font-bold text-ink-900 dark:text-white">{{ $ordersThisMonth }}</p>
                    <p class="text-xs text-ink-500 dark:text-ink-400">{{ __('orders.stat_month') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Recent orders ================= --}}
    <div class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-ink-100 px-6 py-4 dark:border-ink-800">
            <h2 class="text-lg font-semibold text-ink-900 dark:text-white">{{ __('orders.recent_orders') }}</h2>
            <a href="{{ route('merchant.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">{{ __('orders.view_all') }}</a>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-semibold text-ink-900 dark:text-white">{{ __('orders.no_orders_yet') }}</p>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.no_orders_hint') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead class="bg-ink-50 dark:bg-ink-900/60">
                        <tr>
                            <th class="table-th">{{ __('orders.col_code') }}</th>
                            <th class="table-th">{{ __('orders.col_customer') }}</th>
                            <th class="table-th">{{ __('orders.col_item') }}</th>
                            <th class="table-th">{{ __('orders.col_status') }}</th>
                            <th class="table-th">{{ __('orders.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                        @foreach ($recentOrders as $order)
                            @include('orders.partials.row', ['order' => $order, 'statuses' => $statuses])
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ================= Subscription mini-card ================= --}}
    <div class="mt-6 card flex flex-wrap items-center justify-between gap-4 p-6">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </span>
            <div>
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('billing.my_subscription') }}</p>
                <p class="font-semibold text-ink-900 dark:text-white">
                    {{ $subscription ? $subscription->plan_name : __('billing.free') }}
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('billing.index') }}" class="btn-secondary text-sm">{{ __('billing.billing') }}</a>
            <a href="{{ route('plans.index') }}" class="btn-primary text-sm">{{ __('billing.upgrade') }}</a>
        </div>
    </div>
</x-app-layout>
