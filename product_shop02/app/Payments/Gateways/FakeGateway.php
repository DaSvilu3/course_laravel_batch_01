<?php

namespace App\Payments\Gateways;

use App\Contracts\Payable;
use App\Contracts\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Payments\Data\CheckoutSession;
use App\Payments\Data\PaymentVerification;
use Illuminate\Support\Str;

/**
 * A local stand-in for Thawani.
 *
 * It redirects to a page inside this app with "Pay" and "Cancel" buttons, so
 * the whole checkout flow can be built and demoed before you have API keys.
 * Set PAYMENT_GATEWAY=thawani in .env when you are ready for the real thing.
 */
class FakeGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'fake';
    }

    public function createSession(Payable $payable, string $successUrl, string $cancelUrl): CheckoutSession
    {
        $sessionId = 'fake_'.Str::lower(Str::random(24));

        return new CheckoutSession(
            sessionId: $sessionId,
            redirectUrl: route('fake-gateway.show', [
                'session' => $sessionId,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]),
            raw: ['session_id' => $sessionId, 'simulated' => true],
        );
    }

    /**
     * The fake checkout page writes the outcome onto the payment row itself,
     * so verification just reads it back.
     */
    public function verify(string $sessionId): PaymentVerification
    {
        $payment = Payment::where('session_id', $sessionId)->first();

        $status = $payment?->payload['simulated_status'] ?? null;

        return new PaymentVerification(
            status: $status ? PaymentStatus::from($status) : PaymentStatus::Pending,
            amount: (int) ($payment?->amount ?? 0),
            reference: $payment ? 'FAKE-'.$payment->id : null,
            raw: ['simulated' => true, 'payment_status' => $status],
        );
    }
}
