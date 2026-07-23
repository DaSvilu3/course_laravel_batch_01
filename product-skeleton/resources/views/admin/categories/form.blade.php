<x-admin-layout>
    <x-slot name="header">{{ $category->exists ? __('admin.edit_category') : __('admin.new_category') }}</x-slot>

    <form method="POST" class="card max-w-2xl space-y-5 p-6"
          action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div>
            <x-input-label for="type" :value="__('admin.type')" />
            <select id="type" name="type" class="form-input-field mt-1">
                @foreach (App\Enums\CatalogType::options() as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $category->type?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('type')" class="mt-2" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name_ar" :value="__('admin.name_ar')" />
                <x-text-input id="name_ar" name="name_ar" class="mt-1 block w-full" :value="old('name_ar', $category->name_ar)" required />
                <x-input-error :messages="$errors->get('name_ar')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="name_en" :value="__('admin.name_en')" />
                <x-text-input id="name_en" name="name_en" class="mt-1 block w-full" :value="old('name_en', $category->name_en)" required />
                <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="slug" :value="__('admin.slug')" />
            <x-text-input id="slug" name="slug" class="mt-1 block w-full" :value="old('slug', $category->slug)" />
            <p class="mt-1 text-xs text-gray-500">{{ __('admin.slug_hint') }}</p>
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="description_ar" :value="__('admin.description_ar')" />
                <textarea id="description_ar" name="description_ar" rows="3" class="form-input-field mt-1">{{ old('description_ar', $category->description_ar) }}</textarea>
            </div>
            <div>
                <x-input-label for="description_en" :value="__('admin.description_en')" />
                <textarea id="description_en" name="description_en" rows="3" class="form-input-field mt-1">{{ old('description_en', $category->description_en) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="sort_order" :value="__('admin.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full"
                              :value="old('sort_order', $category->sort_order ?? 0)" />
            </div>
            <label class="flex items-end gap-2 pb-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-brand-600"
                       @checked(old('is_active', $category->is_active ?? true))>
                <span class="text-sm text-gray-700">{{ __('common.active') }}</span>
            </label>
        </div>

        <div class="flex gap-3 border-t border-gray-100 pt-4">
            <button type="submit" class="btn-primary">{{ __('common.save') }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">{{ __('common.cancel') }}</a>
        </div>
    </form>
</x-admin-layout>
