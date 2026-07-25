<nav x-data="{ open: false }" class="glass sticky top-0 z-40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-sm font-black text-white shadow-glow transition group-hover:scale-105">
                        {{ mb_substr(config('app.name'), 0, 1) }}
                    </span>
                    <span class="text-lg font-bold tracking-tight text-ink-900 dark:text-white">
                        {{ config('app.name') }}
                    </span>
                </a>

                <div class="hidden gap-1 sm:flex">
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('common.dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('merchant.orders.index')" :active="request()->routeIs('merchant.orders.*')">
                            {{ __('orders.all_orders') }}
                        </x-nav-link>
                        <x-nav-link :href="route('merchant.analytics')" :active="request()->routeIs('merchant.analytics')">
                            {{ __('orders.analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('plans.index')" :active="request()->routeIs('plans.*')">
                            {{ __('landing.nav_pricing') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('home').'#features'">
                            {{ __('landing.nav_features') }}
                        </x-nav-link>
                        <x-nav-link :href="route('plans.index')" :active="request()->routeIs('plans.*')">
                            {{ __('landing.nav_pricing') }}
                        </x-nav-link>
                        <x-nav-link :href="route('track')" :active="request()->routeIs('track')">
                            {{ __('landing.nav_track') }}
                        </x-nav-link>
                        <x-nav-link :href="route('privacy')" :active="request()->routeIs('privacy')">
                            {{ __('landing.nav_privacy') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                <x-locale-switcher />
                <x-theme-toggle />

                @auth
                    <x-dropdown align="end" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-xl border border-ink-200 bg-white py-1.5 pe-2 ps-1.5 text-sm font-medium text-ink-700 transition hover:border-ink-300 dark:border-ink-700 dark:bg-ink-900 dark:text-ink-200 dark:hover:border-ink-600">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-900/50 dark:text-brand-300">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </span>
                                <span class="max-w-[8rem] truncate">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 text-ink-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('common.admin_panel') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('dashboard')">{{ __('common.dashboard') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('billing.index')">{{ __('billing.billing') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">{{ __('common.profile') }}</x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('common.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost text-sm">{{ __('landing.sign_in') }}</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm">{{ __('landing.get_started') }}</a>
                @endauth
            </div>

            <div class="flex items-center gap-2 sm:hidden">
                <x-theme-toggle />
                <button @click="open = ! open" class="rounded-xl p-2 text-ink-500 transition hover:bg-ink-100 dark:text-ink-400 dark:hover:bg-ink-800">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-ink-200/70 sm:hidden dark:border-ink-800/70">
        <div class="space-y-1 px-3 pb-3 pt-2">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('common.dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('merchant.orders.index')" :active="request()->routeIs('merchant.orders.*')">{{ __('orders.all_orders') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('merchant.analytics')" :active="request()->routeIs('merchant.analytics')">{{ __('orders.analytics') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('billing.index')" :active="request()->routeIs('billing.*')">{{ __('billing.billing') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('plans.index')">{{ __('landing.nav_pricing') }}</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('home').'#features'">{{ __('landing.nav_features') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('plans.index')">{{ __('landing.nav_pricing') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('track')">{{ __('landing.nav_track') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('privacy')">{{ __('landing.nav_privacy') }}</x-responsive-nav-link>
            @endauth
        </div>

        <div class="border-t border-ink-200/70 px-3 pb-3 pt-4 dark:border-ink-800/70">
            @auth
                <div class="px-1 pb-2">
                    <div class="text-base font-medium text-ink-800 dark:text-ink-100">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-ink-500 dark:text-ink-400">{{ Auth::user()->email }}</div>
                </div>

                @if (Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')">{{ __('common.admin_panel') }}</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('common.profile') }}</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('common.logout') }}
                    </x-responsive-nav-link>
                </form>
            @else
                <x-responsive-nav-link :href="route('login')">{{ __('landing.sign_in') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">{{ __('landing.get_started') }}</x-responsive-nav-link>
            @endauth

            <div class="px-1 pt-3">
                <x-locale-switcher />
            </div>
        </div>
    </div>
</nav>
