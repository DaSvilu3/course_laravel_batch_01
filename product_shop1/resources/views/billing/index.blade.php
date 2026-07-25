<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('billing.billing') }}</h1>
    </x-slot>

    {{-- Current subscription --}}
    <div class="card p-6">
        <h2 class="mb-4 text-lg font-semibold text-ink-900 dark:text-white">{{ __('billing.my_subscription') }}</h2>

        @if ($subscription)
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-xl font-bold text-ink-900 dark:text-white">{{ $subscription->plan_name }}</span>
                        <x-status-badge :status="$subscription->status" />
                    </div>

                    <dl class="mt-3 space-y-1 text-sm text-ink-700 dark:text-ink-300">
                        <div class="flex gap-2">
                            <dt class="text-ink-500 dark:text-ink-400">{{ __('billing.price') }}:</dt>
                            <dd>{{ $subscription->formattedPrice() }} {{ $subscription->interval->label() }}</dd>
                        </div>
                        @if ($subscription->onTrial())
                            <div class="flex gap-2">
                                <dt class="text-ink-500 dark:text-ink-400">{{ __('billing.trial_until') }}:</dt>
                                <dd>{{ $subscription->trial_ends_at->format('Y-m-d') }}</dd>
                            </div>
                        @endif
                        <div class="flex gap-2">
                            <dt class="text-ink-500 dark:text-ink-400">
                                {{ $subscription->willRenew() ? __('billing.renews_on') : __('billing.ends_on') }}:
                            </dt>
                            <dd>{{ $subscription->ends_at?->format('Y-m-d') }}</dd>
                        </div>
                    </dl>

                    @unless ($subscription->willRenew())
                        <p class="mt-2 text-sm text-amber-700 dark:text-amber-400">{{ __('billing.wont_renew') }}</p>
                    @endunless
                </div>

                <div class="flex flex-col gap-2">
                    <form method="POST" action="{{ route('billing.renew', $subscription) }}">
                        @csrf
                        <button class="btn-primary w-full">{{ __('billing.renew') }}</button>
                    </form>

                    @if ($subscription->willRenew())
                        <form method="POST" action="{{ route('billing.cancel', $subscription) }}"
                              onsubmit="return confirm('{{ __('billing.cancel_confirm') }}')">
                            @csrf
                            <button class="btn-secondary w-full">{{ __('billing.cancel') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <p class="text-ink-700 dark:text-ink-300">{{ __('billing.no_subscription') }}</p>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('billing.no_subscription_hint') }}</p>
        @endif
    </div>

    {{-- Available plans --}}
    <div class="mt-8">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-ink-900 dark:text-white">
                {{ $subscription ? __('billing.change_plan') : __('billing.plans') }}
            </h2>
            <a href="{{ route('plans.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">{{ __('billing.view_plans') }}</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            @foreach ($plans as $plan)
                <div class="card flex flex-col p-4">
                    <span class="font-semibold text-ink-900 dark:text-white">{{ $plan->name }}</span>
                    <span class="mt-1 text-lg font-bold text-brand-600 dark:text-brand-400">{{ $plan->formattedPrice() }}</span>
                    <span class="text-xs text-ink-500 dark:text-ink-400">{{ $plan->interval->label() }}</span>

                    <form method="POST" action="{{ route('billing.subscribe', $plan) }}" class="mt-auto pt-4">
                        @csrf
                        <button class="btn-secondary w-full text-xs"
                                @disabled($subscription && $subscription->plan_id === $plan->id)>
                            {{ $subscription && $subscription->plan_id === $plan->id
                                ? __('billing.current_plan')
                                : __('billing.subscribe') }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    {{-- History --}}
    @if ($history->isNotEmpty())
        <div class="mt-8">
            <h2 class="mb-4 text-lg font-semibold text-ink-900 dark:text-white">{{ __('billing.history') }}</h2>
            <div class="card overflow-hidden overflow-x-auto">
                <table class="min-w-full divide-y divide-ink-100 dark:divide-ink-800">
                    <thead class="bg-ink-50 dark:bg-ink-900/60">
                        <tr>
                            <th class="table-th">{{ __('billing.plan') }}</th>
                            <th class="table-th">{{ __('billing.price') }}</th>
                            <th class="table-th">{{ __('billing.status') }}</th>
                            <th class="table-th">{{ __('billing.started') }}</th>
                            <th class="table-th">{{ __('billing.ends_on') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                        @foreach ($history as $item)
                            <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                                <td class="table-td font-medium text-ink-900 dark:text-white">{{ $item->plan_name }}</td>
                                <td class="table-td">{{ $item->formattedPrice() }}</td>
                                <td class="table-td"><x-status-badge :status="$item->status" /></td>
                                <td class="table-td">{{ $item->starts_at?->format('Y-m-d') ?? __('common.none') }}</td>
                                <td class="table-td">{{ $item->ends_at?->format('Y-m-d') ?? __('common.none') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
