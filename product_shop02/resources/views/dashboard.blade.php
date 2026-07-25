<x-app-layout>
    <x-slot name="title">{{ __('common.dashboard') }}</x-slot>

    @php($user = auth()->user())
    @php($limit = $quota->limit($user))
    @php($used = $quota->used($user))

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-ink-900 dark:text-white">
                {{ $user->store_name ?: $user->name }}
            </h1>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('common.welcome_back', ['name' => $user->name]) }}</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn-primary px-5 py-2.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" /></svg>
            {{ __('shop.new_order') }}
        </a>
    </div>

    {{-- Intake link card --}}
    @if ($user->intake_slug)
    <div x-data="{ copied: false, copy(v) { navigator.clipboard.writeText(v); this.copied = true; setTimeout(() => this.copied = false, 1500); } }"
         class="card mt-6 overflow-hidden">
        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5" /></svg>
                    </span>
                    <h2 class="font-bold text-ink-900 dark:text-white">{{ __('shop.intake_link') }}</h2>
                </div>
                <p class="mt-2 truncate text-sm text-brand-600 dark:text-brand-400" dir="ltr">{{ $user->intakeUrl() }}</p>
                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('shop.intake_link_hint') }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <button type="button" @click="copy('{{ $user->intakeUrl() }}')" class="btn-secondary px-4 py-2 text-sm">
                    <span x-show="!copied">{{ __('shop.copy_link') }}</span>
                    <span x-show="copied" x-cloak class="text-emerald-600 dark:text-emerald-400">{{ __('shop.copied') }}</span>
                </button>
                @if ($user->whatsapp)
                    <a href="https://wa.me/?text={{ urlencode($user->store_name.' — '.$user->intakeUrl()) }}" target="_blank" rel="noopener"
                       class="btn-secondary px-4 py-2 text-sm">{{ __('shop.share_whatsapp') }}</a>
                @endif
                <a href="{{ $user->intakeUrl() }}" target="_blank" rel="noopener" class="btn-ghost px-3 py-2 text-sm">{{ __('shop.open_link') }}</a>
            </div>
        </div>
    </div>
    @endif

    {{-- KPI cards --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => __('shop.quota_period_day'), 'value' => $todayCount],
            ['label' => __('shop.quota_period_month'), 'value' => $monthCount],
            ['label' => __('shop.all_orders'), 'value' => $totalCount],
            ['label' => __('enums.order_status.in_progress'), 'value' => $openCount],
        ] as $c)
            <div class="card p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-ink-500 dark:text-ink-400">{{ $c['label'] }}</p>
                <p class="mt-2 text-3xl font-black text-ink-900 dark:text-white">{{ number_format($c['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Quota / plan --}}
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-ink-900 dark:text-white">{{ __('shop.quota') }}</h2>
                <span class="badge bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">{{ $plan?->translate('name') }}</span>
            </div>

            @if ($limit === null)
                <p class="mt-6 text-2xl font-black text-ink-900 dark:text-white">{{ __('shop.quota_unlimited') }}</p>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ number_format($used) }}</p>
            @else
                <div class="mt-6 flex items-baseline gap-1">
                    <span class="text-3xl font-black text-ink-900 dark:text-white">{{ number_format($used) }}</span>
                    <span class="text-sm text-ink-500 dark:text-ink-400">/ {{ number_format($limit) }} · {{ $quota->period($user) === 'day' ? __('shop.quota_period_day') : __('shop.quota_period_month') }}</span>
                </div>
                <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500" style="width: {{ $quota->usagePercent($user) }}%"></div>
                </div>
                <p class="mt-2 text-xs text-ink-500 dark:text-ink-400">{{ __('shop.quota_remaining', ['count' => number_format(max(0, $limit - $used))]) }}</p>
            @endif

            @unless ($subscription)
                <a href="{{ route('pricing') }}" class="btn-primary mt-5 w-full justify-center py-2.5 text-sm">{{ __('billing.upgrade') }}</a>
            @else
                <a href="{{ route('billing.index') }}" class="btn-secondary mt-5 w-full justify-center py-2.5 text-sm">{{ __('billing.manage_billing') }}</a>
            @endunless
        </div>

        {{-- Recent orders --}}
        <div class="card overflow-hidden lg:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-200/70 px-6 py-4 dark:border-ink-800/70">
                <h2 class="font-bold text-ink-900 dark:text-white">{{ __('admin.recent_orders') }}</h2>
                <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">{{ __('shop.all_orders') }}</a>
            </div>

            @if ($recentOrders->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('shop.no_orders') }}</p>
            @else
                <div class="divide-y divide-ink-200/70 dark:divide-ink-800/70">
                    @foreach ($recentOrders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between gap-4 px-6 py-3.5 transition hover:bg-ink-50 dark:hover:bg-ink-800/40">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $order->customer_name }}</p>
                                <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ $order->tracker_code }} · {{ $order->item_description }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="hidden text-sm font-medium text-ink-600 sm:inline dark:text-ink-300">{{ $order->formattedPrice() }}</span>
                                <x-status-badge :status="$order->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- Monthly report — a branded, exportable summary card               --}}
    {{-- ================================================================= --}}
    <section class="mt-10" x-data="reportExport">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-black tracking-tight text-ink-900 dark:text-white">{{ __('shop.monthly_report') }}</h2>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('shop.report_subtitle') }}</p>
            </div>
            <button type="button" @click="exportCard" :disabled="busy" class="btn-primary px-5 py-2.5">
                <svg x-show="!busy" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
                <span x-show="!busy">{{ __('shop.export_image') }}</span>
                <span x-show="busy" x-cloak>{{ __('shop.exporting') }}</span>
            </button>
        </div>

        {{-- The exportable node. Always light + LTR-neutral so the image reads
             the same for everyone, independent of the viewer's theme. --}}
        <div class="mt-5 flex justify-center">
            <div id="report-card" dir="rtl"
                 class="w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-lift ring-1 ring-ink-200">
                {{-- Brand header --}}
                <div class="flex items-center justify-between gap-4 bg-gradient-to-br from-brand-600 to-violet-600 px-7 py-6 text-white">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 text-xl font-black ring-1 ring-white/25">
                            {{ mb_substr(config('app.name'), 0, 1) }}
                        </span>
                        <div>
                            <p class="text-lg font-black leading-none">{{ config('app.name') }}</p>
                            <p class="mt-1 text-xs text-white/80">{{ __('shop.monthly_report') }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="text-sm font-bold">{{ $user->store_name ?: $user->name }}</p>
                        <p class="mt-0.5 text-xs text-white/80">{{ $report['month'] }}</p>
                    </div>
                </div>

                {{-- Stat grid --}}
                <div class="grid grid-cols-2 gap-px bg-ink-100">
                    <div class="bg-white px-7 py-5">
                        <p class="text-xs font-medium text-ink-500">{{ __('shop.report_total_orders') }}</p>
                        <p class="mt-1 text-3xl font-black text-ink-900">{{ number_format($report['total']) }}</p>
                    </div>
                    <div class="bg-white px-7 py-5">
                        <p class="text-xs font-medium text-ink-500">{{ __('shop.report_value') }}</p>
                        <p class="mt-1 text-3xl font-black text-ink-900">{{ \App\Support\Money::format($report['value']) }}</p>
                    </div>
                    <div class="bg-white px-7 py-5">
                        <p class="text-xs font-medium text-ink-500">{{ __('shop.report_completed') }}</p>
                        <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($report['completed']) }}</p>
                    </div>
                    <div class="bg-white px-7 py-5">
                        <p class="text-xs font-medium text-ink-500">{{ __('shop.report_cancelled') }}</p>
                        <p class="mt-1 text-2xl font-black text-rose-500">{{ number_format($report['cancelled']) }}</p>
                    </div>
                </div>

                {{-- Top governorates --}}
                @if ($report['top_governorates']->isNotEmpty())
                    <div class="border-t border-ink-100 px-7 py-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ink-500">{{ __('shop.report_top_governorates') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($report['top_governorates'] as $gov)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-700">
                                    {{ $gov['label'] }}
                                    <span class="rounded-full bg-brand-600 px-1.5 text-xs font-bold text-white">{{ $gov['count'] }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-between gap-4 border-t border-ink-100 bg-ink-50 px-7 py-4">
                    <p class="text-xs font-medium text-ink-500">{{ __('shop.report_tagline') }}</p>
                    <p class="text-[11px] text-ink-400">{{ __('shop.report_generated', ['date' => now()->translatedFormat('d M Y')]) }}</p>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
