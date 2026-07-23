@php
    $cart = app(App\Support\Cart::class);
@endphp

<nav x-data="{ open: false }" class="border-b border-gray-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-lg font-bold text-brand-700">
                    {{ config('app.name') }}
                </a>

                <div class="hidden gap-6 sm:flex">
                    <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                        {{ __('shop.services') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('shop.products') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden items-center gap-4 sm:flex">
                <x-locale-switcher />

                <a href="{{ route('cart.index') }}" class="relative text-sm font-medium text-gray-600 hover:text-gray-900">
                    {{ __('shop.cart') }}
                    @if ($cart->quantity() > 0)
                        <span class="absolute -top-2 -end-3 rounded-full bg-brand-600 px-1.5 text-xs text-white">
                            {{ $cart->quantity() }}
                        </span>
                    @endif
                </a>

                @auth
                    <x-dropdown align="end" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                                {{ Auth::user()->name }}
                                <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
                            <x-dropdown-link :href="route('orders.index')">{{ __('shop.my_orders') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('bookings.index')">{{ __('shop.my_bookings') }}</x-dropdown-link>
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
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('common.login') }}</a>
                    <a href="{{ route('register') }}" class="btn-primary text-xs">{{ __('common.register') }}</a>
                @endauth
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="rounded-md p-2 text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('services.index')">{{ __('shop.services') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')">{{ __('shop.products') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cart.index')">
                {{ __('shop.cart') }} ({{ $cart->quantity() }})
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-gray-200 pb-3 pt-4">
            @auth
                <div class="px-4 pb-2">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                @if (Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')">{{ __('common.admin_panel') }}</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('dashboard')">{{ __('common.dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('orders.index')">{{ __('shop.my_orders') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('common.profile') }}</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('common.logout') }}
                    </x-responsive-nav-link>
                </form>
            @else
                <x-responsive-nav-link :href="route('login')">{{ __('common.login') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">{{ __('common.register') }}</x-responsive-nav-link>
            @endauth

            <div class="px-4 pt-3">
                <x-locale-switcher />
            </div>
        </div>
    </div>
</nav>
