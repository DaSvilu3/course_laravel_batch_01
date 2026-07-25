<?php

namespace App\Http\Requests;

/** Merchant creating an order by hand from the dashboard. */
class StoreOrderRequest extends OrderRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }
}
