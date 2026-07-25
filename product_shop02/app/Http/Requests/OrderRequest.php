<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Support\Money;
use App\Support\Regions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared validation for an order captured either through the public intake
 * form or entered manually by the merchant. Subclasses only change authorize().
 */
abstract class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:32'],
            'item_description' => ['required', 'string', 'max:2000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'country' => ['nullable', Rule::in(Regions::countries())],
            'wilayat' => ['nullable', Rule::in(Regions::wilayatKeys())],
            'address' => ['nullable', 'string', 'max:1000'],
            'location_note' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'image', 'max:4096'],
        ];
    }

    /**
     * Normalized attributes ready for Order::create — price converted to baisa,
     * uploaded image stored, country defaulted.
     */
    public function orderData(): array
    {
        $data = $this->safe()->except(['price', 'attachment']);

        $data['country'] = $this->input('country') ?: Regions::defaultCountry();
        $data['governorate'] = Regions::governorateOfWilayat($this->input('wilayat'));
        $data['price'] = $this->filled('price') ? Money::toBaisa($this->input('price')) : null;

        if ($this->hasFile('attachment')) {
            $data['attachment_path'] = $this->file('attachment')->store('orders', 'public');
        }

        return $data;
    }
}
