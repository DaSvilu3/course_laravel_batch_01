<?php

namespace App\Http\Requests\Admin;

use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('products')->ignore($this->route('product'))],
            'sku' => ['nullable', 'string', 'max:64',
                Rule::unique('products')->ignore($this->route('product'))],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            // Leave empty for unlimited stock.
            'stock' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function payload(): array
    {
        $data = $this->safe()->except('image');

        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name_en']);
        $data['price'] = Money::toBaisa($data['price']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $this->boolean('is_active');
        $data['is_featured'] = $this->boolean('is_featured');

        return $data;
    }
}
