<x-auth-layout :title="__('landing.login_title')" :subtitle="__('landing.login_subtitle')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-ink-300 text-brand-600 shadow-sm focus:ring-brand-500 dark:border-ink-700 dark:bg-ink-800" name="remember">
            <span class="ms-2 text-sm text-ink-600 dark:text-ink-400">{{ __('Remember me') }}</span>
        </label>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Log in') }}
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-500 dark:text-ink-400">
        {{ __('landing.no_account') }}
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
            {{ __('landing.create_account') }}
        </a>
    </p>
</x-auth-layout>
