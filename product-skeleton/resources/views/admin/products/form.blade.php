<x-admin-layout>
    <x-slot name="header">{{ $product->exists ? __('admin.edit_product') : __('admin.new_product') }}</x-slot>

    <form method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-5 p-6"
          action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name_ar" :value="__('admin.name_ar')" />
                <x-text-input id="name_ar" name="name_ar" class="mt-1 block w-full" :value="old('name_ar', $product->name_ar)" required />
                <x-input-error :messages="$errors->get('name_ar')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="name_en" :value="__('admin.name_en')" />
                <x-text-input id="name_en" name="name_en" class="mt-1 block w-full" :value="old('name_en', $product->name_en)" required />
                <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="category_id" :value="__('admin.category')" />
                <select id="category_id" name="category_id" class="form-input-field mt-1">
                    <option value="">{{ __('admin.no_category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('admin.slug')" />
                <x-text-input id="slug" name="slug" class="mt-1 block w-full" :value="old('slug', $product->slug)" />
                <p class="mt-1 text-xs text-gray-500">{{ __('admin.slug_hint') }}</p>
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="sku" :value="__('admin.sku')" />
                <x-text-input id="sku" name="sku" class="mt-1 block w-full" :value="old('sku', $product->sku)" />
                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="price" :value="__('admin.price_omr')" />
                <x-text-input id="price" name="price" type="number" step="0.001" min="0" class="mt-1 block w-full"
                              :value="old('price', $product->exists ? App\Support\Money::decimalString($product->price) : '')" required />
                <p class="mt-1 text-xs text-gray-500">{{ __('admin.price_hint') }}</p>
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="stock" :value="__('admin.stock')" />
                <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full"
                              :value="old('stock', $product->stock)" />
                <p class="mt-1 text-xs text-gray-500">{{ __('admin.stock_hint') }}</p>
                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('admin.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full"
                              :value="old('sort_order', $product->sort_order ?? 0)" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="description_ar" :value="__('admin.description_ar')" />
                <textarea id="description_ar" name="description_ar" rows="4" class="form-input-field mt-1">{{ old('description_ar', $product->description_ar) }}</textarea>
            </div>
            <div>
                <x-input-label for="description_en" :value="__('admin.description_en')" />
                <textarea id="description_en" name="description_en" rows="4" class="form-input-field mt-1">{{ old('description_en', $product->description_en) }}</textarea>
            </div>
        </div>

        <div>
            <x-input-label for="image" :value="__('admin.image')" />
            <input id="image" name="image" type="file" accept="image/*" class="mt-1 block w-full text-sm">
            <x-input-error :messages="$errors->get('image')" class="mt-2" />

            @if ($product->image_path)
                <img src="{{ $product->purchasableImageUrl() }}" alt="" class="mt-3 h-24 w-24 rounded-lg object-cover">
            @endif
        </div>

        <div class="flex flex-wrap gap-6">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-brand-600"
                       @checked(old('is_active', $product->is_active ?? true))>
                <span class="text-sm text-gray-700">{{ __('common.active') }}</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-brand-600"
                       @checked(old('is_featured', $product->is_featured ?? false))>
                <span class="text-sm text-gray-700">{{ __('admin.featured') }}</span>
            </label>
        </div>

        <div class="flex gap-3 border-t border-gray-100 pt-4">
            <button type="submit" class="btn-primary">{{ __('common.save') }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">{{ __('common.cancel') }}</a>
        </div>
    </form>
</x-admin-layout>
