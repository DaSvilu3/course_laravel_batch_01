<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Payments\Exceptions\PaymentException;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly Cart $cart,
        private readonly OrderService $orders,
        private readonly CheckoutService $checkout,
    ) {}

    public function show(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('shop.checkout', ['cart' => $this->cart]);
    }

    /**
     * Create the order, open a payment session, send the customer to the gateway.
     */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        $order = $this->orders->createFromCart($request->user(), $this->cart, $request->validated());

        try {
            $payment = $this->checkout->start($order);
        } catch (PaymentException $e) {
            Log::error('Checkout failed', ['order' => $order->number, 'error' => $e->getMessage()]);

            return redirect()
                ->route('orders.show', $order)
                ->withErrors(['payment' => __('shop.payment_start_failed')]);
        }

        return redirect()->away($payment->checkout_url);
    }
}
