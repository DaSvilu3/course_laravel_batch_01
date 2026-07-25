<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('shop.services') }}</h1>
    </x-slot>

    <form method="GET" class="card mb-6 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-48 flex-1">
            <label class="mb-1 block text-sm font-medium text-ink-700 dark:text-ink-300">{{ __('common.search') }}</label>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="{{ __('shop.search_placeholder') }}" class="form-input-field">
        </div>

        <div class="min-w-48">
            <label class="mb-1 block text-sm font-medium text-ink-700 dark:text-ink-300">{{ __('shop.category') }}</label>
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
        <div class="rounded-2xl border border-dashed border-ink-300 py-16 text-center dark:border-ink-700">
            <p class="text-ink-500 dark:text-ink-400">{{ __('shop.no_services') }}</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <x-catalog-card :item="$service" />
            @endforeach
        </div>

        <div class="mt-8">{{ $services->links() }}</div>
    @endif
</x-app-layout>
