{{-- One card in the services / products grid. Works with any Purchasable. --}}
@props(['item'])

<div class="card-hover group flex flex-col overflow-hidden">
    <a href="{{ $item->purchasableUrl() }}" class="relative block aspect-[4/3] overflow-hidden bg-ink-100 dark:bg-ink-800">
        @if ($item->purchasableImageUrl())
            <img src="{{ $item->purchasableImageUrl() }}" alt="{{ $item->purchasableName() }}"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 to-violet-100 text-5xl font-black text-brand-400 dark:from-ink-800 dark:to-ink-900 dark:text-ink-600">
                {{ mb_substr($item->purchasableName(), 0, 1) }}
            </div>
        @endif

        @if ($item->category)
            <span class="absolute start-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-ink-600 backdrop-blur dark:bg-ink-900/90 dark:text-ink-300">
                {{ $item->category->name }}
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-2 p-4">
        <a href="{{ $item->purchasableUrl() }}" class="font-semibold text-ink-900 transition-colors group-hover:text-brand-600 dark:text-white dark:group-hover:text-brand-400">
            {{ $item->purchasableName() }}
        </a>

        @if ($item->description)
            <p class="line-clamp-2 text-sm text-ink-500 dark:text-ink-400">{{ $item->description }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between pt-3">
            <span class="text-lg font-bold text-ink-900 dark:text-white">{{ $item->formattedPrice() }}</span>

            <form method="POST" action="{{ route('cart.store') }}">
                @csrf
                <input type="hidden" name="type" value="{{ $item->purchasableType() }}">
                <input type="hidden" name="id" value="{{ $item->getKey() }}">
                <button type="submit" class="btn-primary text-xs" @disabled(! $item->isPurchasable())>
                    {{ $item->isPurchasable() ? __('shop.add_to_cart') : __('shop.unavailable') }}
                </button>
            </form>
        </div>
    </div>
</div>
