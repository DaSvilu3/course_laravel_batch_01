<?php

namespace App\Http\Controllers;

use App\Enums\OrderSource;
use App\Enums\PaymentMethod;
use App\Http\Requests\PublicOrderRequest;
use App\Models\User;
use App\Services\OrderQuotaService;
use App\Services\OrderService;
use App\Support\Regions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * The public intake form a merchant shares with customers (/o/{slug}). Anyone
 * with the link can submit an order; they get back a tracker code.
 */
class PublicOrderController extends Controller
{
    public function show(string $slug): View
    {
        $merchant = $this->resolveMerchant($slug);

        return view('public.intake', [
            'merchant' => $merchant,
            'countries' => Regions::countryOptions(),
            'wilayatGroups' => Regions::wilayatGroups(),
            'paymentMethods' => PaymentMethod::options(),
            'defaultCountry' => Regions::defaultCountry(),
        ]);
    }

    public function store(PublicOrderRequest $request, string $slug, OrderService $orders, OrderQuotaService $quota): RedirectResponse
    {
        $merchant = $this->resolveMerchant($slug);

        if ($quota->hasReachedLimit($merchant)) {
            return back()
                ->withInput()
                ->withErrors(['quota' => __('shop.quota_reached_public')]);
        }

        $order = $orders->create($merchant, $request->orderData(), OrderSource::Form);

        return redirect()
            ->route('track.show', ['code' => $order->tracker_code])
            ->with('created', true);
    }

    /** Only active merchants with a slug accept orders. */
    private function resolveMerchant(string $slug): User
    {
        return User::query()
            ->where('intake_slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }
}
