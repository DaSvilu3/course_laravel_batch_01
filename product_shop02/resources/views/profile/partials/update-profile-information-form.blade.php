<section>
    <header>
        <h2 class="text-lg font-medium text-ink-900 dark:text-white">
            {{ __('common.store_settings') }}
        </h2>

        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            {{ __('shop.intake_link_hint') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="store_name" :value="__('landing.store_name')" />
            <x-text-input id="store_name" name="store_name" type="text" class="mt-1 block w-full" :value="old('store_name', $user->store_name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('store_name')" />
        </div>

        <div>
            <x-input-label for="intake_slug" :value="__('shop.intake_link')" />
            <div class="mt-1 flex items-center gap-2">
                <span class="text-xs text-ink-400 dark:text-ink-500" dir="ltr">{{ url('/o') }}/</span>
                <x-text-input id="intake_slug" name="intake_slug" type="text" class="block w-full" :value="old('intake_slug', $user->intake_slug)" required dir="ltr" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('intake_slug')" />
        </div>

        <div>
            <x-input-label for="whatsapp" :value="__('landing.whatsapp')" />
            <x-text-input id="whatsapp" name="whatsapp" type="tel" class="mt-1 block w-full" :value="old('whatsapp', $user->whatsapp)" dir="ltr" placeholder="9XXXXXXX" />
            <x-input-error class="mt-2" :messages="$errors->get('whatsapp')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('landing.your_name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('admin.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-ink-700 dark:text-ink-300">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="rounded-md text-sm text-brand-600 underline hover:text-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:text-brand-400 dark:focus:ring-offset-ink-900">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-600 dark:text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-ink-500 dark:text-ink-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
