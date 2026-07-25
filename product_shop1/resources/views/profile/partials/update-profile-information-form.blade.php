<section>
    <header>
        <h2 class="text-lg font-medium text-ink-900 dark:text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
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

        {{-- ---- Store settings (public order link) ---- --}}
        <div class="border-t border-ink-100 pt-6 dark:border-ink-800">
            <h3 class="text-sm font-semibold text-ink-900 dark:text-white">{{ __('orders.store_settings') }}</h3>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.store_settings_hint') }}</p>
        </div>

        <div>
            <x-input-label for="store_name" :value="__('orders.store_name_label')" />
            <x-text-input id="store_name" name="store_name" type="text" class="mt-1 block w-full" :value="old('store_name', $user->store_name)" />
            <x-input-error class="mt-2" :messages="$errors->get('store_name')" />
        </div>

        <div>
            <x-input-label for="whatsapp" :value="__('orders.whatsapp_label')" />
            <x-text-input id="whatsapp" name="whatsapp" type="tel" dir="ltr" class="mt-1 block w-full text-start" :value="old('whatsapp', $user->whatsapp)" placeholder="9XXXXXXX" />
            <x-input-error class="mt-2" :messages="$errors->get('whatsapp')" />
        </div>

        {{-- Logo upload with a live preview --}}
        <div x-data="{ preview: '{{ $user->logoUrl() }}',
            pick(e) { const f = e.target.files[0]; if (f) this.preview = URL.createObjectURL(f); } }">
            <x-input-label for="logo" :value="__('orders.logo_label')" />
            <div class="mt-1.5 flex items-center gap-4">
                <template x-if="preview">
                    <img :src="preview" alt="" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-ink-900/5 dark:ring-white/10">
                </template>
                <template x-if="!preview">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-ink-100 text-ink-400 dark:bg-ink-800">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.16-5.16a2.25 2.25 0 0 1 3.18 0l5.16 5.16m-1.5-1.5 1.41-1.41a2.25 2.25 0 0 1 3.18 0l2.16 2.16M3.75 4.5h16.5a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Z" /></svg>
                    </span>
                </template>
                <div>
                    <label for="logo" class="btn-secondary cursor-pointer text-sm">{{ __('orders.logo_label') }}</label>
                    <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp" class="sr-only" @change="pick($event)">
                    <p class="mt-1.5 text-xs text-ink-400 dark:text-ink-500">{{ __('orders.logo_hint') }}</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('logo')" />
        </div>

        @if ($user->store_slug)
            <div>
                <x-input-label :value="__('orders.your_public_link')" />
                <div class="mt-1.5 flex items-center gap-2 rounded-xl border border-ink-200/70 bg-ink-50/60 px-3 py-2.5 dark:border-ink-800 dark:bg-ink-950/40">
                    <span dir="ltr" class="flex-1 truncate font-mono text-sm text-ink-700 dark:text-ink-200">{{ $user->publicIntakeUrl() }}</span>
                    <a href="{{ $user->publicIntakeUrl() }}" target="_blank" rel="noopener" class="btn-ghost shrink-0 px-3 py-1.5 text-xs">{{ __('orders.open_link') }}</a>
                </div>
            </div>
        @endif

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
