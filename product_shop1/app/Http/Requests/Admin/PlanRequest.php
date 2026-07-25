<?php

namespace App\Http\Requests\Admin;

use App\Enums\BillingInterval;
use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('plans')->ignore($this->route('plan'))],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'interval' => ['required', Rule::in(array_column(BillingInterval::cases(), 'value'))],
            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            // One "key: value" per line, e.g. "max_projects: 10".
            'features' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function payload(): array
    {
        $data = $this->safe()->except('features');

        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name_en']);
        $data['price'] = Money::toBaisa($data['price']);
        $data['trial_days'] = $data['trial_days'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $this->boolean('is_active');
        $data['is_featured'] = $this->boolean('is_featured');
        $data['features'] = $this->parseFeatures($this->input('features'));

        return $data;
    }

    /**
     * Turn the textarea ("key: value" per line) into a typed feature map.
     * Numbers become ints, true/false become booleans, everything else stays
     * a string.
     */
    private function parseFeatures(?string $raw): array
    {
        $features = [];

        foreach (preg_split('/\r\n|\r|\n/', (string) $raw) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode(':', $line, 2));

            if ($key === '') {
                continue;
            }

            $features[$key] = match (true) {
                is_numeric($value) => $value + 0,
                in_array(strtolower($value), ['true', 'yes'], true) => true,
                in_array(strtolower($value), ['false', 'no'], true) => false,
                default => $value,
            };
        }

        return $features;
    }
}
