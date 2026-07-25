<?php

namespace App\Http\Requests\Admin;

use App\Enums\CatalogType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_column(CatalogType::cases(), 'value'))],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('categories')->ignore($this->route('category'))],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function payload(): array
    {
        $data = $this->validated();
        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name_en']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $this->boolean('is_active');

        return $data;
    }
}
