<?php

namespace App\Contracts;

use App\Payments\Data\CheckoutSession;
use App\Payments\Data\PaymentVerification;
use App\Payments\Exceptions\PaymentException;

/**
 * Every payment provider we support implements this.
 *
 * Controllers depend on this interface, never on Thawani directly — which is
 * why swapping in the FakeGateway for local development and tests works, and
 * why adding a second provider later does not touch the checkout code.
 */
interface PaymentGateway
{
    /** Driver name stored on the payment row, e.g. "thawani". */
    public function name(): string;

    /**
     * Open a hosted checkout session and return where to send the customer.
     *
     * @throws PaymentException
     */
    public function createSession(Payable $payable, string $successUrl, string $cancelUrl): CheckoutSession;

    /**
     * Ask the provider what actually happened. This is the only source of
     * truth — never trust a redirect or a webhook body on its own.
     *
     * @throws PaymentException
     */
    public function verify(string $sessionId): PaymentVerification;
}
