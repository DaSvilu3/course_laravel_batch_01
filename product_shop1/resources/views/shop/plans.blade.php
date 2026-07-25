<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('billing.choose_plan') }}</h1>
            <p class="mt-2 text-ink-500 dark:text-ink-400">{{ __('billing.pricing_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="mx-auto grid max-w-4xl items-stretch gap-6 sm:grid-cols-2">
        @foreach ($plans as $plan)
            @php $isCurrent = $current && $current->plan_id === $plan->id; @endphp

            <div class="animate-fade-up relative flex flex-col overflow-hidden rounded-2xl p-6
                {{ $plan->is_featured
                    ? 'bg-white shadow-lift ring-2 ring-brand-500 dark:bg-ink-900'
                    : 'card' }}">
                @if ($plan->is_featured)
                    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 -top-16 -z-10 h-40 bg-gradient-to-b from-brand-500/20 to-transparent blur-2xl"></div>
                    <span class="absolute -top-3 start-6 inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-brand-600 to-violet-600 px-3 py-1 text-xs font-semibold text-white shadow-glow">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.366 2.446c-.784.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.98 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.287-3.958z"/></svg>
                        {{ __('billing.featured') }}
                    </span>
                @endif

                <h2 class="text-lg font-bold text-ink-900 dark:text-white">{{ $plan->name }}</h2>

                @if ($plan->description)
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ $plan->description }}</p>
                @endif

                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-3xl font-black text-ink-900 dark:text-white">{{ $plan->formattedPrice() }}</span>
                    @unless ($plan->isFree())
                        <span class="text-sm text-ink-500 dark:text-ink-400">
                            {{ $plan->interval === App\Enums\BillingInterval::Year ? __('billing.per_year') : __('billing.per_month') }}
                        </span>
                    @endunless
                </div>

                @if ($plan->trial_days > 0)
                    <p class="mt-1 text-xs font-medium text-brand-600 dark:text-brand-400">{{ __('billing.trial_days', ['days' => $plan->trial_days]) }}</p>
                @endif

                <ul class="mt-5 space-y-2.5 text-sm text-ink-700 dark:text-ink-300">
                    @foreach ($plan->featureLines() as $line)
                        <li class="flex items-center gap-2.5 {{ $line['on'] ? '' : 'text-ink-400 dark:text-ink-500' }}">
                            @if ($line['on'])
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            @else
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-ink-100 text-ink-400 dark:bg-ink-800 dark:text-ink-500">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>
                                </span>
                            @endif
                            <span>{{ $line['label'] }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-auto pt-6">
                    @if ($isCurrent)
                        <span class="btn-secondary w-full cursor-default">{{ __('billing.current_plan') }}</span>
                    @elseif (auth()->check())
                        <form method="POST" action="{{ route('billing.subscribe', $plan) }}">
                            @csrf
                            <button type="submit" class="{{ $plan->is_featured ? 'btn-primary' : 'btn-secondary' }} w-full">
                                {{ $current ? __('billing.switch_to') : __('billing.subscribe') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="{{ $plan->is_featured ? 'btn-primary' : 'btn-secondary' }} w-full">{{ __('billing.get_started') }}</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
