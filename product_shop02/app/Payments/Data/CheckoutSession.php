<?php

namespace App\Payments\Data;

/**
 * The result of opening a checkout session at the provider.
 */
class CheckoutSession
{
    public function __construct(
        public readonly string $sessionId,
        public readonly string $redirectUrl,
        public readonly array $raw = [],
    ) {}
}
