<x-app-layout>
    <div class="rounded-2xl bg-gradient-to-br from-brand-700 to-brand-900 px-6 py-16 text-center text-white sm:px-12">
        <h1 class="text-3xl font-bold sm:text-4xl">{{ __('shop.hero_title') }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-brand-100">{{ __('shop.hero_subtitle') }}</p>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('services.index') }}" class="btn bg-white text-brand-800 hover:bg-brand-50">
                {{ __('shop.browse_services') }}
            </a>
            <a href="{{ route('products.index') }}" class="btn border border-white/40 text-white hover:bg-white/10">
                {{ __('shop.browse_products') }}
            </a>
        </div>
    </div>

    @if ($services->isNotEmpty())
        <section class="mt-12">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">{{ __('shop.featured_services') }}</h2>
                <a href="{{ route('services.index') }}" class="text-sm text-brand-700 hover:underline">
                    {{ __('shop.browse_services') }}
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <x-catalog-card :item="$service" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($products->isNotEmpty())
        <section class="mt-12">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">{{ __('shop.featured_products') }}</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-brand-700 hover:underline">
                    {{ __('shop.browse_products') }}
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-catalog-card :item="$product" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($services->isEmpty() && $products->isEmpty())
        <p class="mt-12 text-center text-gray-500">{{ __('common.no_results') }}</p>
    @endif
</x-app-layout>
