<x-app-layout>
    <x-slot name="title">{{ __('billing.pricing') }}</x-slot>

    <div class="mx-auto max-w-2xl text-center">
        <span class="eyebrow">
            <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
            {{ __('landing.pricing_eyebrow') }}
        </span>
        <h1 class="mt-5 text-3xl font-black tracking-tight text-ink-900 sm:text-4xl dark:text-white">{{ __('landing.pricing_title') }}</h1>
        <p class="mt-4 text-lg leading-relaxed text-ink-600 dark:text-ink-300">{{ __('landing.pricing_subtitle') }}</p>
    </div>

    <div class="mx-auto mt-12 grid max-w-4xl items-start gap-6 md:grid-cols-2">
        @foreach ($plans as $plan)
            <div class="card relative p-8 {{ $plan->is_featured ? 'ring-2 ring-brand-500 shadow-lift' : '' }}">
                @if ($plan->is_featured)
                    <span class="absolute -top-3 end-6 inline-flex items-center gap-1 rounded-full bg-gradient-to-br from-brand-600 to-violet-600 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white shadow-glow">
                        {{ __('landing.most_popular') }}
                    </span>
                @endif

                <h3 class="text-lg font-bold text-ink-900 dark:text-white">{{ $plan->translate('name') }}</h3>
                <p class="mt-2 min-h-[2.5rem] text-sm leading-relaxed text-ink-500 dark:text-ink-400">{{ $plan->translate('description') }}</p>

                <div class="mt-6 flex items-baseline gap-1">
                    <span class="text-4xl font-black tracking-tight text-ink-900 dark:text-white">{{ $plan->formattedPrice() }}</span>
                    @unless ($plan->isFree())
                        <span class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ __('landing.per_month') }}</span>
                    @endunless
                </div>

                @if ($plan->trial_days > 0)
                    <p class="mt-2 text-xs font-medium text-brand-600 dark:text-brand-400">{{ __('billing.trial_days', ['days' => $plan->trial_days]) }}</p>
                @endif

                <ul class="mt-6 space-y-3 text-sm">
                    @foreach ($plan->featureLines() as $line)
                        <li class="flex items-start gap-2.5 text-ink-700 dark:text-ink-200">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ auth()->check() ? route('billing.index') : route('register') }}"
                   class="{{ $plan->is_featured ? 'btn-primary' : 'btn-secondary' }} mt-8 w-full py-3">
                    {{ $plan->isFree() ? __('billing.get_started') : __('billing.subscribe') }}
                </a>
            </div>
        @endforeach
    </div>
</x-app-layout>
