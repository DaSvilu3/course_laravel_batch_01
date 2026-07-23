<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('billing.members_title') }}</h1>
    </x-slot>

    <div class="card p-8">
        <p class="text-ink-700 dark:text-ink-300">{{ __('billing.members_body') }}</p>

        @if (auth()->user()->currentPlan())
            <p class="mt-4 text-sm text-ink-500 dark:text-ink-400">
                {{ __('billing.current_plan') }}:
                <strong class="text-ink-800 dark:text-ink-100">{{ auth()->user()->currentPlan()->name }}</strong>
            </p>
        @endif
    </div>
</x-app-layout>
