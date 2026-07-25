<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Payments\Exceptions\PaymentException;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('shop.orders.index', [
            'orders' => $request->user()->orders()->withCount('items')->paginate(10),
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('shop.orders.show', [
            'order' => $order->load('items.purchasable', 'payments'),
        ]);
    }

    /** Retry (or start) payment for an unpaid order. */
    public function pay(Order $order, CheckoutService $checkout): RedirectResponse
    {
        $this->authorize('pay', $order);

        try {
            $payment = $checkout->start($order);
        } catch (PaymentException $e) {
            Log::error('Retry payment failed', ['order' => $order->number, 'error' => $e->getMessage()]);

            return back()->withErrors(['payment' => __('shop.payment_start_failed')]);
        }

        return redirect()->away($payment->checkout_url);
    }

    public function cancel(Order $order, OrderService $orders): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $orders->cancel($order);

        return back()->with('status', __('shop.order_cancelled'));
    }
}
