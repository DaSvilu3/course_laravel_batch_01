<?php

namespace App\Payments\Exceptions;

use RuntimeException;

class PaymentException extends RuntimeException
{
    public static function gatewayError(string $gateway, string $message, array $context = []): self
    {
        return new self(sprintf('[%s] %s %s', $gateway, $message, $context ? json_encode($context) : ''));
    }
}
