<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">
            {{ __('common.welcome_back', ['name' => Auth::user()->name]) }}
        </h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('common.dashboard') }}</p>
    </x-slot>

    @if ($subscription)
        {{-- ---- Subscribed: plan overview -------------------------------- --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Current plan --}}
            <div class="card relative overflow-hidden p-6 lg:col-span-2">
                <div aria-hidden="true" class="pointer-events-none absolute -end-16 -top-16 h-48 w-48 rounded-full bg-brand-500/10 blur-3xl"></div>

                <div class="relative flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('billing.my_subscription') }}</p>
                        <div class="mt-1 flex items-center gap-3">
                            <h2 class="text-2xl font-bold text-ink-900 dark:text-white">{{ $subscription->plan_name }}</h2>
                            <x-status-badge :status="$subscription->status" />
                        </div>
                        <p class="mt-1 text-ink-600 dark:text-ink-300">
                            <span class="text-lg font-bold text-ink-900 dark:text-white">{{ $subscription->formattedPrice() }}</span>
                            <span class="text-sm text-ink-500 dark:text-ink-400">{{ $subscription->interval->label() }}</span>
                        </p>
                    </div>

                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow-glow">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.09 4.26L19 8l-3.5 3.4.8 4.8L12 14l-4.3 2.2.8-4.8L5 8l4.91-.74L12 3z" />
                        </svg>
                    </span>
                </div>

                {{-- term / renewal line --}}
                <div class="relative mt-5 rounded-xl border border-ink-200/70 bg-ink-50/60 px-4 py-3 text-sm dark:border-ink-800 dark:bg-ink-950/40">
                    @if ($subscription->onGracePeriod())
                        <p class="flex items-center gap-2 text-amber-700 dark:text-amber-300">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            {{ __('billing.wont_renew') }}
                            <span class="font-semibold">{{ $subscription->ends_at?->isoFormat('LL') }}</span>
                        </p>
                    @elseif ($subscription->onTrial())
                        <p class="flex items-center gap-2 text-ink-600 dark:text-ink-300">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                            {{ __('billing.trial_until') }}:
                            <span class="font-semibold text-ink-900 dark:text-white">{{ $subscription->trial_ends_at?->isoFormat('LL') }}</span>
                        </p>
                    @else
                        <p class="flex items-center gap-2 text-ink-600 dark:text-ink-300">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            {{ __('billing.renews_on') }}:
                            <span class="font-semibold text-ink-900 dark:text-white">{{ $subscription->ends_at?->isoFormat('LL') }}</span>
                        </p>
                    @endif
                </div>

                <div class="relative mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('billing.index') }}" class="btn-primary">{{ __('billing.manage_billing') }}</a>
                    <a href="{{ route('plans.index') }}" class="btn-secondary">{{ __('billing.upgrade') }}</a>
                </div>
            </div>

            {{-- What the plan includes --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink-500 dark:text-ink-400">{{ __('billing.plan_includes') }}</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    @php
                        $projects = $plan?->feature('max_projects', 0);
                        $projectsLabel = ($projects === -1) ? __('billing.unlimited') : $projects;
                    @endphp
                    <li class="flex items-center gap-3">
                        <x-dashboard-check :on="true" />
                        <span class="text-ink-700 dark:text-ink-200">{{ $projectsLabel }} {{ __('billing.projects') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <x-dashboard-check :on="(bool) $plan?->feature('api_access', false)" />
                        <span class="text-ink-700 dark:text-ink-200">{{ __('billing.api_access') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <x-dashboard-check :on="true" />
                        <span class="text-ink-700 dark:text-ink-200 capitalize">{{ $plan?->feature('support', 'community') }} {{ __('billing.support') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    @else
        {{-- ---- Not subscribed: upsell ---------------------------------- --}}
        <div class="card relative overflow-hidden p-10 text-center">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                <div class="absolute -top-24 start-1/2 h-64 w-64 -translate-x-1/2 rounded-full bg-brand-500/15 blur-3xl"></div>
            </div>
            <div class="relative">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow-glow">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.09 4.26L19 8l-3.5 3.4.8 4.8L12 14l-4.3 2.2.8-4.8L5 8l4.91-.74L12 3z" />
                    </svg>
                </span>
                <h2 class="mt-5 text-xl font-bold text-ink-900 dark:text-white">{{ __('billing.no_subscription') }}</h2>
                <p class="mx-auto mt-2 max-w-md text-ink-500 dark:text-ink-400">{{ __('billing.no_subscription_hint') }}</p>
                <a href="{{ route('plans.index') }}" class="btn-primary mt-6">{{ __('billing.view_plans') }}</a>
            </div>
        </div>
    @endif

    {{-- ---- Stat tiles ---------------------------------------------------- --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('billing.total_paid') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m4-14H9.5a2.5 2.5 0 0 0 0 5h5a2.5 2.5 0 0 1 0 5H8" /></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-black text-ink-900 dark:text-white">{{ App\Support\Money::format($totalPaid) }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('billing.status') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.5l2 2 4-4.5M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Z" /></svg>
                </span>
            </div>
            <p class="mt-3 text-lg font-bold text-ink-900 dark:text-white">
                @if ($subscription)
                    {{ $subscription->status->label() }}
                @else
                    {{ __('common.none') }}
                @endif
            </p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('billing.active_since') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-900/40 dark:text-violet-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" /></svg>
                </span>
            </div>
            <p class="mt-3 text-lg font-bold text-ink-900 dark:text-white">{{ Auth::user()->created_at?->isoFormat('MMM YYYY') }}</p>
        </div>
    </div>

    {{-- ---- Recent invoices ---------------------------------------------- --}}
    <div class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-ink-100 px-6 py-4 dark:border-ink-800">
            <h2 class="text-lg font-semibold text-ink-900 dark:text-white">{{ __('billing.recent_invoices') }}</h2>
            <a href="{{ route('billing.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">{{ __('billing.billing') }}</a>
        </div>

        @forelse ($payments as $payment)
            <div class="flex items-center justify-between border-b border-ink-100 px-6 py-3 last:border-0 dark:border-ink-800">
                <div>
                    <p class="font-mono text-sm text-ink-800 dark:text-ink-200">{{ $payment->reference() }}</p>
                    <p class="text-xs text-ink-500 dark:text-ink-400">{{ $payment->created_at?->isoFormat('LL') }}</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="font-semibold text-ink-900 dark:text-white">{{ $payment->formattedAmount() }}</span>
                    <span class="badge {{ $payment->status->color() }}">{{ $payment->status->label() }}</span>
                </div>
            </div>
        @empty
            <p class="px-6 py-8 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('billing.no_invoices') }}</p>
        @endforelse
    </div>
</x-app-layout>
