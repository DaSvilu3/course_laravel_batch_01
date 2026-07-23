<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('shop.services') }}</h1>
    </x-slot>

    <form method="GET" class="card mb-6 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-48 flex-1">
            <label class="mb-1 block text-sm text-gray-600">{{ __('common.search') }}</label>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="{{ __('shop.search_placeholder') }}" class="form-input-field">
        </div>

        <div class="min-w-48">
            <label class="mb-1 block text-sm text-gray-600">{{ __('shop.category') }}</label>
            <select name="category" class="form-input-field">
                <option value="">{{ __('shop.all_categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-primary">{{ __('common.filter') }}</button>
        <a href="{{ route('services.index') }}" class="btn-secondary">{{ __('common.reset') }}</a>
    </form>

    @if ($services->isEmpty())
        <p class="py-12 text-center text-gray-500">{{ __('shop.no_services') }}</p>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <x-catalog-card :item="$service" />
            @endforeach
        </div>

        <div class="mt-8">{{ $services->links() }}</div>
    @endif
</x-app-layout>
