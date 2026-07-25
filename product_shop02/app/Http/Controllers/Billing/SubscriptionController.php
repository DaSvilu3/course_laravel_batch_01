<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Payments\Exceptions\PaymentException;
use App\Services\CheckoutService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    /** The customer's billing home: current plan + history. */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('billing.index', [
            'subscription' => $user->activeSubscription(),
            'history' => $user->subscriptions()->with('plan')->get(),
            'plans' => Plan::active()->ordered()->get(),
        ]);
    }

    /** Start subscribing to a plan. Free plans activate immediately. */
    public function store(Request $request, Plan $plan, CheckoutService $checkout): RedirectResponse
    {
        abort_unless($plan->is_active, 404);

        $subscription = $this->subscriptions->subscribe($request->user(), $plan);

        if ($plan->isFree()) {
            return redirect()->route('billing.index')->with('status', __('billing.subscribed'));
        }

        try {
            $payment = $checkout->start($subscription);
        } catch (PaymentException $e) {
            Log::error('Subscription checkout failed', ['plan' => $plan->slug, 'error' => $e->getMessage()]);

            return redirect()->route('billing.index')->withErrors(['payment' => __('shop.payment_start_failed')]);
        }

        return redirect()->away($payment->checkout_url);
    }

    /** Pay to renew / extend the current subscription. */
    public function renew(Request $request, Subscription $subscription, CheckoutService $checkout): RedirectResponse
    {
        abort_unless($subscription->user_id === $request->user()->id, 403);

        try {
            $payment = $checkout->start($subscription);
        } catch (PaymentException $e) {
            return redirect()->route('billing.index')->withErrors(['payment' => __('shop.payment_start_failed')]);
        }

        return redirect()->away($payment->checkout_url);
    }

    /** Cancel at the end of the current term. */
    public function cancel(Request $request, Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->user_id === $request->user()->id, 403);

        $this->subscriptions->cancel($subscription);

        return redirect()->route('billing.index')->with('status', __('billing.canceled_notice'));
    }
}
