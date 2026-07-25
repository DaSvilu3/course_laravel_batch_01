<?php

namespace App\Http\Controllers\Payments;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * A pretend Thawani checkout page, used when PAYMENT_GATEWAY=fake.
 * Never registered in production — see routes/web.php.
 */
class FakeGatewayController extends Controller
{
    public function show(Request $request): View
    {
        $request->validate([
            'session' => ['required', 'string'],
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
        ]);

        return view('payments.fake-gateway', [
            'payment' => Payment::where('session_id', $request->string('session'))->firstOrFail(),
            'successUrl' => $request->string('success_url')->toString(),
            'cancelUrl' => $request->string('cancel_url')->toString(),
        ]);
    }

    public function pay(Request $request): RedirectResponse
    {
        return $this->simulate($request, PaymentStatus::Paid, 'success_url');
    }

    public function cancel(Request $request): RedirectResponse
    {
        return $this->simulate($request, PaymentStatus::Cancelled, 'cancel_url');
    }

    private function simulate(Request $request, PaymentStatus $status, string $urlField): RedirectResponse
    {
        $data = $request->validate([
            'session' => ['required', 'string'],
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
        ]);

        $payment = Payment::where('session_id', $data['session'])->firstOrFail();

        $payment->update([
            'payload' => array_merge($payment->payload ?? [], ['simulated_status' => $status->value]),
        ]);

        return redirect()->away($data[$urlField]);
    }
}
