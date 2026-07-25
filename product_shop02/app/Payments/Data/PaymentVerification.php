<?php

namespace App\Payments\Data;

use App\Enums\PaymentStatus;

/**
 * What the provider says about a checkout session.
 */
class PaymentVerification
{
    public function __construct(
        public readonly PaymentStatus $status,
        public readonly int $amount,       // baisa
        public readonly ?string $reference = null, // invoice number
        public readonly array $raw = [],
    ) {}

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }
}
