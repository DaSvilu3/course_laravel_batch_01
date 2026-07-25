<?php

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\Payments\Exceptions\PaymentException;
use App\Payments\Gateways\FakeGateway;
use App\Payments\Gateways\ThawaniGateway;

/**
 * Resolves the configured payment driver.
 *
 * Adding a provider = write a class implementing PaymentGateway, then add a
 * case here and a config block in config/payments.php.
 */
class PaymentManager
{
    /** @var array<string, PaymentGateway> */
    private array $resolved = [];

    public function __construct(private readonly array $config) {}

    public function driver(?string $name = null): PaymentGateway
    {
        $name = $name ?: ($this->config['default'] ?? 'fake');

        return $this->resolved[$name] ??= $this->resolve($name);
    }

    private function resolve(string $name): PaymentGateway
    {
        return match ($name) {
            'thawani' => new ThawaniGateway($this->config['thawani'] ?? []),
            'fake' => new FakeGateway,
            default => throw new PaymentException("Unsupported payment gateway [{$name}]."),
        };
    }
}
