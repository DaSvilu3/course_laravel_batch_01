<?php

namespace App\Contracts;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Anything that can be charged through the payment engine.
 *
 * An Order (shop) and a Subscription (SaaS) both implement this, which is why
 * one CheckoutService and one Thawani driver bill both without knowing the
 * difference. To bill a new kind of thing (a top-up, a donation…) implement
 * this interface and register a morph alias — checkout does not change.
 */
interface Payable
{
    /** @return MorphMany<Payment> */
    public function payments(): MorphMany;

    /** The account that pays and owns the payment row. */
    public function paymentOwner(): User;

    /** Human reference shown to the customer and sent to the gateway. */
    public function paymentReference(): string;

    /** Amount to charge, in baisa. */
    public function paymentTotal(): int;

    public function paymentCurrency(): string;

    /**
     * Line items for the hosted checkout page.
     *
     * @return array<int, array{name: string, quantity: int, unit_amount: int}>
     */
    public function paymentLineItems(): array;

    /** Extra data stored with the gateway session. */
    public function paymentMetadata(): array;

    /** Where to send the customer back to after paying. */
    public function paymentReturnUrl(): string;

    /** Has this already been paid for? Keeps checkout idempotent. */
    public function isSettled(): bool;

    /** Fulfilment once money is received (activate, ship, grant access…). */
    public function handlePaymentPaid(Payment $payment): void;

    /** Called when a payment attempt is cancelled or fails. */
    public function handlePaymentFailed(Payment $payment): void;
}
