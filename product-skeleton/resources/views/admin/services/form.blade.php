<x-admin-layout>
    <x-slot name="header">{{ $service->exists ? __('admin.edit_service') : __('admin.new_service') }}</x-slot>

    <form method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-5 p-6"
          action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}">
        @csrf
        @if ($service->exists) @method('PUT') @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name_ar" :value="__('admin.name_ar')" />
                <x-text-input id="name_ar" name="name_ar" class="mt-1 block w-full" :value="old('name_ar', $service->name_ar)" required />
                <x-input-error :messages="$errors->get('name_ar')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="name_en" :value="__('admin.name_en')" />
                <x-text-input id="name_en" name="name_en" class="mt-1 block w-full" :value="old('name_en', $service->name_en)" required />
                <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="category_id" :value="__('admin.category')" />
                <select id="category_id" name="category_id" class="form-input-field mt-1">
                    <option value="">{{ __('admin.no_category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $service->category_id) === $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('admin.slug')" />
                <x-text-input id="slug" name="slug" class="mt-1 block w-full" :value="old('slug', $service->slug)" />
                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('admin.slug_hint') }}</p>
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="price" :value="__('admin.price_omr')" />
                <x-text-input id="price" name="price" type="number" step="0.001" min="0" class="mt-1 block w-full"
                              :value="old('price', $service->exists ? App\Support\Money::decimalString($service->price) : '')" required />
                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('admin.price_hint') }}</p>
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="duration_minutes" :value="__('admin.duration_minutes')" />
                <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="0" class="mt-1 block w-full"
                              :value="old('duration_minutes', $service->duration_minutes)" />
                <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('admin.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full"
                              :value="old('sort_order', $service->sort_order ?? 0)" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="description_ar" :value="__('admin.description_ar')" />
                <textarea id="description_ar" name="description_ar" rows="4" class="form-input-field mt-1">{{ old('description_ar', $service->description_ar) }}</textarea>
            </div>
            <div>
                <x-input-label for="description_en" :value="__('admin.description_en')" />
                <textarea id="description_en" name="description_en" rows="4" class="form-input-field mt-1">{{ old('description_en', $service->description_en) }}</textarea>
            </div>
        </div>

        <div>
            <x-input-label for="image" :value="__('admin.image')" />
            <input id="image" name="image" type="file" accept="image/*"
                   class="mt-1 block w-full text-sm text-ink-600 file:me-3 file:rounded-lg file:border-0 file:bg-ink-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-ink-700 hover:file:bg-ink-200 dark:text-ink-400 dark:file:bg-ink-800 dark:file:text-ink-200 dark:hover:file:bg-ink-700">
            <x-input-error :messages="$errors->get('image')" class="mt-2" />

            @if ($service->image_path)
                <img src="{{ $service->purchasableImageUrl() }}" alt=""
                     class="mt-3 h-24 w-24 rounded-lg object-cover">
            @endif
        </div>

        <div class="flex flex-wrap gap-6">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       class="rounded border-ink-300 text-brand-600 focus:ring-brand-500 dark:border-ink-600 dark:bg-ink-900"
                       @checked(old('is_active', $service->is_active ?? true))>
                <span class="text-sm text-ink-700 dark:text-ink-300">{{ __('common.active') }}</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="hidden" name="is_bookable" value="0">
                <input type="checkbox" name="is_bookable" value="1"
                       class="rounded border-ink-300 text-brand-600 focus:ring-brand-500 dark:border-ink-600 dark:bg-ink-900"
                       @checked(old('is_bookable', $service->is_bookable ?? true))>
                <span class="text-sm text-ink-700 dark:text-ink-300">{{ __('admin.bookable') }}</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1"
                       class="rounded border-ink-300 text-brand-600 focus:ring-brand-500 dark:border-ink-600 dark:bg-ink-900"
                       @checked(old('is_featured', $service->is_featured ?? false))>
                <span class="text-sm text-ink-700 dark:text-ink-300">{{ __('admin.featured') }}</span>
            </label>
        </div>

        <div class="flex gap-3 border-t border-ink-100 pt-4 dark:border-ink-800">
            <button type="submit" class="btn-primary">{{ __('common.save') }}</button>
            <a href="{{ route('admin.services.index') }}" class="btn-secondary">{{ __('common.cancel') }}</a>
        </div>
    </form>
</x-admin-layout>
