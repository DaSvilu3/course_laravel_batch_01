<x-auth-layout :title="__('landing.register_title')" :subtitle="__('landing.register_subtitle')">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="store_name" :value="__('landing.store_name')" />
            <x-text-input id="store_name" class="mt-1.5 block w-full" type="text" name="store_name" :value="old('store_name')" required autofocus placeholder="{{ __('landing.store_name') }}" />
            <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('landing.store_name_hint') }}</p>
            <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name" :value="__('landing.your_name')" />
            <x-text-input id="name" class="mt-1.5 block w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="whatsapp" :value="__('landing.whatsapp')" />
            <x-text-input id="whatsapp" class="mt-1.5 block w-full" type="tel" name="whatsapp" :value="old('whatsapp')" inputmode="tel" placeholder="9XXXXXXX" dir="ltr" />
            <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('landing.whatsapp_hint') }}</p>
            <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('admin.email')" />
            <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" dir="ltr" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('admin.password')" />
            <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" dir="ltr" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('admin.confirm_password')" />
            <x-text-input id="password_confirmation" class="mt-1.5 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" dir="ltr" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('landing.create_account') }}
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-500 dark:text-ink-400">
        {{ __('landing.have_account') }}
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400">
            {{ __('landing.sign_in') }}
        </a>
    </p>
</x-auth-layout>
