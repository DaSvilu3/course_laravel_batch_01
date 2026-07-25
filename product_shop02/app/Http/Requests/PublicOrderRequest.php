<?php

namespace App\Http\Requests;

/** Customer submitting an order through a merchant's public intake form. */
class PublicOrderRequest extends OrderRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
