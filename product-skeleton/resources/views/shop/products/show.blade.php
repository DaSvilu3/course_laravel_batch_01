<x-app-layout>
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="card aspect-[4/3] overflow-hidden bg-gray-100">
            @if ($product->purchasableImageUrl())
                <img src="{{ $product->purchasableImageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center text-6xl text-gray-300">
                    {{ mb_substr($product->name, 0, 1) }}
                </div>
            @endif
        </div>

        <div>
            @if ($product->category)
                <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
            @endif

            <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

            <p class="mt-4 text-2xl font-bold text-brand-700">{{ $product->formattedPrice() }}</p>

            <p class="mt-2 text-sm {{ $product->isPurchasable() ? 'text-emerald-600' : 'text-rose-600' }}">
                @if (! $product->isPurchasable())
                    {{ __('shop.out_of_stock') }}
                @elseif ($product->stock !== null)
                    {{ __('shop.in_stock', ['count' => $product->stock]) }}
                @endif
            </p>

            @if ($product->description)
                <p class="mt-6 whitespace-pre-line leading-relaxed text-gray-700">{{ $product->description }}</p>
            @endif

            <form method="POST" action="{{ route('cart.store') }}" class="card mt-8 space-y-4 p-4">
                @csrf
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="id" value="{{ $product->id }}">

                <div>
                    <label class="mb-1 block text-sm text-gray-600">{{ __('shop.quantity') }}</label>
                    <input type="number" name="quantity" value="1" min="1"
                           max="{{ $product->stock ?? 100 }}" class="form-input-field">
                </div>

                <button type="submit" class="btn-primary w-full" @disabled(! $product->isPurchasable())>
                    {{ $product->isPurchasable() ? __('shop.add_to_cart') : __('shop.out_of_stock') }}
                </button>
            </form>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">{{ __('shop.related') }}</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $item)
                    <x-catalog-card :item="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-app-layout>
