<?php

namespace App\Support;

use App\Contracts\Purchasable;

/**
 * One line in the cart. Note that the price is always read from the model,
 * never from the session — otherwise a customer could edit their own price.
 */
class CartItem
{
    public function __construct(
        public readonly string $key,
        public readonly Purchasable $purchasable,
        public readonly int $quantity,
        public readonly array $options = [],
    ) {}

    public function name(): string
    {
        return $this->purchasable->purchasableName();
    }

    public function unitPrice(): int
    {
        return $this->purchasable->purchasableUnitPrice();
    }

    public function total(): int
    {
        return $this->unitPrice() * $this->quantity;
    }

    public function formattedUnitPrice(): string
    {
        return Money::format($this->unitPrice());
    }

    public function formattedTotal(): string
    {
        return Money::format($this->total());
    }

    public function isAvailable(): bool
    {
        return $this->purchasable->isPurchasable($this->quantity);
    }
}
