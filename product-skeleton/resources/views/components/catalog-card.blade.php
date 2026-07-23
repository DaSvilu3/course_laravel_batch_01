{{-- One card in the services / products grid. Works with any Purchasable. --}}
@props(['item'])

<div class="card flex flex-col overflow-hidden transition hover:shadow-md">
    <a href="{{ $item->purchasableUrl() }}" class="block aspect-[4/3] overflow-hidden bg-gray-100">
        @if ($item->purchasableImageUrl())
            <img src="{{ $item->purchasableImageUrl() }}" alt="{{ $item->purchasableName() }}"
                 class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center text-4xl text-gray-300">
                {{ mb_substr($item->purchasableName(), 0, 1) }}
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-2 p-4">
        @if ($item->category)
            <span class="text-xs text-gray-500">{{ $item->category->name }}</span>
        @endif

        <a href="{{ $item->purchasableUrl() }}" class="font-semibold text-gray-900 hover:text-brand-700">
            {{ $item->purchasableName() }}
        </a>

        @if ($item->description)
            <p class="line-clamp-2 text-sm text-gray-500">{{ $item->description }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between pt-3">
            <span class="font-bold text-brand-700">{{ $item->formattedPrice() }}</span>

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
