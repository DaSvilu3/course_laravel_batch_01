<x-app-layout>
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="card aspect-[4/3] overflow-hidden bg-ink-100 dark:bg-ink-800">
            @if ($product->purchasableImageUrl())
                <img src="{{ $product->purchasableImageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center bg-gradient-to-br from-brand-100 to-violet-100 text-6xl font-black text-brand-400 dark:from-ink-800 dark:to-ink-900 dark:text-ink-600">
                    {{ mb_substr($product->name, 0, 1) }}
                </div>
            @endif
        </div>

        <div>
            @if ($product->category)
                <span class="text-sm font-medium text-brand-600 dark:text-brand-400">{{ $product->category->name }}</span>
            @endif

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-ink-900 dark:text-white">{{ $product->name }}</h1>

            <p class="mt-4 text-2xl font-bold text-ink-900 dark:text-white">{{ $product->formattedPrice() }}</p>

            <p class="mt-2 text-sm font-medium {{ $product->isPurchasable() ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                @if (! $product->isPurchasable())
                    {{ __('shop.out_of_stock') }}
                @elseif ($product->stock !== null)
                    {{ __('shop.in_stock', ['count' => $product->stock]) }}
                @endif
            </p>

            @if ($product->description)
                <p class="mt-6 whitespace-pre-line leading-relaxed text-ink-700 dark:text-ink-300">{{ $product->description }}</p>
            @endif

            <form method="POST" action="{{ route('cart.store') }}" class="card mt-8 space-y-4 p-4">
                @csrf
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="id" value="{{ $product->id }}">

                <div>
                    <label class="mb-1 block text-sm font-medium text-ink-700 dark:text-ink-300">{{ __('shop.quantity') }}</label>
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
            <h2 class="mb-4 text-xl font-semibold text-ink-900 dark:text-white">{{ __('shop.related') }}</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $item)
                    <x-catalog-card :item="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-app-layout>
