<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('orders.analytics_title') }}</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.analytics_subtitle') }}</p>
    </x-slot>

    @if ($total === 0)
        <div class="card px-6 py-16 text-center">
            <p class="mx-auto max-w-md text-sm text-ink-500 dark:text-ink-400">{{ __('orders.no_data') }}</p>
            <a href="{{ route('dashboard') }}" class="btn-primary mt-6">{{ __('orders.intake_link_title') }}</a>
        </div>
    @else
        {{-- ================= KPI tiles ================= --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @php
                $kpis = [
                    ['label' => __('orders.kpi_total_orders'), 'value' => $total, 'accent' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300'],
                    ['label' => __('orders.kpi_delivered'), 'value' => $delivered, 'accent' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300'],
                    ['label' => __('orders.kpi_delivery_rate'), 'value' => $deliveryRate.'%', 'accent' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300'],
                    ['label' => __('orders.kpi_this_month'), 'value' => $ordersThisMonth, 'accent' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300'],
                    ['label' => __('orders.kpi_revenue'), 'value' => \App\Support\Money::format($revenue), 'accent' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300', 'small' => true],
                    ['label' => __('orders.kpi_avg_order'), 'value' => \App\Support\Money::format($avgOrder), 'accent' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300', 'small' => true],
                ];
            @endphp
            @foreach ($kpis as $kpi)
                <div class="card p-4">
                    <p class="text-xs font-medium text-ink-500 dark:text-ink-400">{{ $kpi['label'] }}</p>
                    <p class="mt-2 font-black text-ink-900 dark:text-white {{ ($kpi['small'] ?? false) ? 'text-lg' : 'text-2xl' }}">{{ $kpi['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            {{-- ================= Daily bar chart ================= --}}
            <div class="card p-6 lg:col-span-2">
                <h2 class="text-sm font-semibold text-ink-900 dark:text-white">{{ __('orders.chart_daily_title') }}</h2>

                <div class="mt-6 overflow-x-auto">
                    <div class="flex h-52 min-w-[560px] items-end gap-2.5">
                        @foreach ($daily as $day)
                            @php $h = (int) round($day['count'] / $maxDaily * 100); @endphp
                            <div class="group flex flex-1 flex-col items-center gap-2">
                                <span class="text-xs font-semibold text-ink-500 dark:text-ink-400">{{ $day['count'] ?: '' }}</span>
                                <div class="flex w-full flex-1 items-end">
                                    <div class="w-full rounded-t-lg bg-ink-100 dark:bg-ink-800" style="height: 100%">
                                        <div class="w-full rounded-t-lg bg-gradient-to-t from-brand-500 to-violet-500 transition-all group-hover:opacity-90"
                                             style="height: {{ max($day['count'] ? 6 : 0, $h) }}%"></div>
                                    </div>
                                </div>
                                <span class="whitespace-nowrap text-[10px] text-ink-400 dark:text-ink-500">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ================= Status breakdown ================= --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink-900 dark:text-white">{{ __('orders.breakdown_title') }}</h2>
                <ul class="mt-5 space-y-4">
                    @foreach ($byStatus as $row)
                        <li>
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <span class="flex items-center gap-2">
                                    <x-status-badge :status="$row['status']" />
                                </span>
                                <span class="font-semibold text-ink-900 dark:text-white">{{ $row['count'] }}</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500" style="width: {{ $row['percent'] }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</x-app-layout>
