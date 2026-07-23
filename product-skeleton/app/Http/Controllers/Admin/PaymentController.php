<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Payments\Exceptions\PaymentException;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with('order', 'user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($w) => $w->where('session_id', 'like', $term)->orWhere('reference', 'like', $term));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', ['payments' => $payments]);
    }

    /** Re-ask the gateway about a payment that looks stuck. */
    public function verify(Payment $payment, CheckoutService $checkout): RedirectResponse
    {
        try {
            $checkout->settle($payment);
        } catch (PaymentException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        return back()->with('status', __('admin.payment_verified'));
    }
}
