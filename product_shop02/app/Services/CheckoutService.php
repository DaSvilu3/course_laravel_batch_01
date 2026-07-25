<?php

namespace App\Services;

use App\Contracts\Payable;
use App\Contracts\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Payments\Exceptions\PaymentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * Drives any Payable (an Order or a Subscription) through the gateway. It knows
 * nothing about what it is charging for — fulfilment is delegated back to the
 * Payable in settle().
 */
class CheckoutService
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    /**
     * Create a payment row, open a session at the gateway and return the
     * payment (its checkout_url is where the customer must be sent).
     */
    public function start(Payable $payable): Payment
    {
        if ($payable->isSettled()) {
            throw new PaymentException('This item has already been paid for.');
        }

        $total = $payable->paymentTotal();
        $minimum = (int) config('payments.minimum_amount', 100);

        if ($total < $minimum) {
            throw new PaymentException("Amount is below the gateway minimum of {$minimum} baisa.");
        }

        $payment = $payable->payments()->create([
            'user_id' => $payable->paymentOwner()->id,
            'gateway' => $this->gateway->name(),
            'status' => PaymentStatus::Pending,
            'amount' => $total,
            'currency' => $payable->paymentCurrency(),
        ]);

        // Signed URLs so the callbacks cannot be forged or replayed with a
        // different payment id. The status is still verified with the gateway.
        $session = $this->gateway->createSession(
            $payable,
            URL::signedRoute('checkout.success', ['payment' => $payment->id]),
            URL::signedRoute('checkout.cancel', ['payment' => $payment->id]),
        );

        $payment->update([
            'session_id' => $session->sessionId,
            'checkout_url' => $session->redirectUrl,
            'payload' => ['session' => $session->raw],
        ]);

        $payable->handleCheckoutStarted($payment);

        return $payment;
    }

    /**
     * Ask the gateway what happened and bring our records in line.
     * Idempotent: calling it from the redirect and from the webhook is fine.
     */
    public function settle(Payment $payment): Payment
    {
        if ($payment->isPaid()) {
            return $payment;
        }

        if (blank($payment->session_id)) {
            return $payment;
        }

        $result = $this->gateway->verify($payment->session_id);

        $payment->forceFill([
            'status' => $result->status,
            'reference' => $result->reference ?? $payment->reference,
            'payload' => array_merge($payment->payload ?? [], ['verification' => $result->raw]),
        ]);

        if ($result->isPaid()) {
            // Guard against an underpaid session being accepted.
            if ($result->amount > 0 && $result->amount !== $payment->amount) {
                Log::warning('Payment amount mismatch', [
                    'payment_id' => $payment->id,
                    'expected' => $payment->amount,
                    'received' => $result->amount,
                ]);

                $payment->status = PaymentStatus::Failed;
                $payment->save();

                return $payment;
            }

            $payment->paid_at = now();
            $payment->save();

            $payment->payable->handlePaymentPaid($payment);

            return $payment->fresh();
        }

        $payment->save();

        if (in_array($result->status, [PaymentStatus::Cancelled, PaymentStatus::Failed], true)) {
            $payment->payable->handlePaymentFailed($payment);
        }

        return $payment;
    }
}
