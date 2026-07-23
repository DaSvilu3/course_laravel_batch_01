<x-app-layout>
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="card aspect-[4/3] overflow-hidden bg-gray-100">
            @if ($service->purchasableImageUrl())
                <img src="{{ $service->purchasableImageUrl() }}" alt="{{ $service->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center text-6xl text-gray-300">
                    {{ mb_substr($service->name, 0, 1) }}
                </div>
            @endif
        </div>

        <div>
            @if ($service->category)
                <span class="text-sm text-gray-500">{{ $service->category->name }}</span>
            @endif

            <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $service->name }}</h1>

            <p class="mt-4 text-2xl font-bold text-brand-700">{{ $service->formattedPrice() }}</p>

            @if ($service->duration_minutes)
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('shop.duration') }}: {{ __('shop.minutes', ['count' => $service->duration_minutes]) }}
                </p>
            @endif

            @if ($service->description)
                <p class="mt-6 whitespace-pre-line leading-relaxed text-gray-700">{{ $service->description }}</p>
            @endif

            <form method="POST" action="{{ route('cart.store') }}" class="card mt-8 space-y-4 p-4">
                @csrf
                <input type="hidden" name="type" value="service">
                <input type="hidden" name="id" value="{{ $service->id }}">

                @if ($service->is_bookable)
                    <div>
                        <label class="mb-1 block text-sm text-gray-600">
                            {{ __('shop.preferred_time') }}
                            <span class="text-gray-400">({{ __('common.optional') }})</span>
                        </label>
                        <input type="datetime-local" name="starts_at" class="form-input-field">
                    </div>
                @endif

                <div>
                    <label class="mb-1 block text-sm text-gray-600">{{ __('shop.quantity') }}</label>
                    <input type="number" name="quantity" value="1" min="1" max="100" class="form-input-field">
                </div>

                <button type="submit" class="btn-primary w-full">
                    {{ $service->is_bookable ? __('shop.book_now') : __('shop.add_to_cart') }}
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
