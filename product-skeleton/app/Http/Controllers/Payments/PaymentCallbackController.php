<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Payments\Exceptions\PaymentException;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

/**
 * Where the customer lands after the gateway is done with them.
 *
 * Both routes are signed, and both re-verify the payment with the gateway
 * before anything is written — a redirect is not proof of payment.
 */
class PaymentCallbackController extends Controller
{
    public function __construct(private readonly CheckoutService $checkout) {}

    public function success(Payment $payment): RedirectResponse
    {
        $payment = $this->settle($payment);

        return redirect()->to($payment->payable->paymentReturnUrl())->with(
            'status',
            $payment->isPaid() ? __('shop.payment_success') : __('shop.payment_pending'),
        );
    }

    public function cancel(Payment $payment): RedirectResponse
    {
        $payment = $this->settle($payment);

        return redirect()->to($payment->payable->paymentReturnUrl())
            ->with('status', __('shop.payment_cancelled'));
    }

    private function settle(Payment $payment): Payment
    {
        try {
            return $this->checkout->settle($payment);
        } catch (PaymentException $e) {
            Log::error('Payment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return $payment;
        }
    }
}
