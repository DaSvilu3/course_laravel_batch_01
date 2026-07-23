<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Exceptions\PaymentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CheckoutService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly OrderService $orders,
    ) {}

    /**
     * Create a payment row, open a session at the gateway and return the
     * payment (its checkout_url is where the customer must be sent).
     */
    public function start(Order $order): Payment
    {
        if ($order->isPaid()) {
            throw new PaymentException('This order has already been paid.');
        }

        $minimum = (int) config('payments.minimum_amount', 100);

        if ($order->total < $minimum) {
            throw new PaymentException("Order total is below the gateway minimum of {$minimum} baisa.");
        }

        $payment = $order->payments()->create([
            'user_id' => $order->user_id,
            'gateway' => $this->gateway->name(),
            'status' => PaymentStatus::Pending,
            'amount' => $order->total,
            'currency' => $order->currency,
        ]);

        // Signed URLs so the callbacks cannot be forged or replayed with a
        // different payment id. The status is still verified with the gateway.
        $session = $this->gateway->createSession(
            $order->load('items'),
            URL::signedRoute('checkout.success', ['payment' => $payment->id]),
            URL::signedRoute('checkout.cancel', ['payment' => $payment->id]),
        );

        $payment->update([
            'session_id' => $session->sessionId,
            'checkout_url' => $session->redirectUrl,
            'payload' => ['session' => $session->raw],
        ]);

        $order->update(['status' => OrderStatus::AwaitingPayment]);

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

            $this->orders->markAsPaid($payment->order);

            return $payment->fresh();
        }

        $payment->save();

        // Cancelled or failed: put the order back so the customer can retry.
        if (in_array($result->status, [PaymentStatus::Cancelled, PaymentStatus::Failed], true)
            && ! $payment->order->isPaid()) {
            $payment->order->update(['status' => OrderStatus::Pending]);
        }

        return $payment;
    }
}
