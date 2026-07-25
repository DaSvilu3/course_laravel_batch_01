<?php

namespace App\Http\Requests\Admin;

use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
                Rule::unique('services')->ignore($this->route('service'))],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            // Entered in OMR (e.g. 12.500) and converted to baisa below.
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_bookable' => ['boolean'],
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
        $data['is_bookable'] = $this->boolean('is_bookable');
        $data['is_active'] = $this->boolean('is_active');
        $data['is_featured'] = $this->boolean('is_featured');

        return $data;
    }
}
