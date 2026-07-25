<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderQuotaService;
use App\Services\OrderService;
use App\Support\Regions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request, OrderQuotaService $quota): View
    {
        $user = $request->user();

        $orders = $user->orders()
            ->status($request->query('status'))
            ->search($request->query('q'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'status' => $request->query('status'),
            'q' => $request->query('q'),
            'statusOptions' => OrderStatus::options(),
            'counts' => $this->statusCounts($user),
            'quota' => $quota,
        ]);
    }

    public function create(): View
    {
        return view('orders.create', [
            'countries' => Regions::countryOptions(),
            'wilayatGroups' => Regions::wilayatGroups(),
            'paymentMethods' => PaymentMethod::options(),
            'defaultCountry' => Regions::defaultCountry(),
        ]);
    }

    public function store(StoreOrderRequest $request, OrderService $orders, OrderQuotaService $quota): RedirectResponse
    {
        $user = $request->user();

        if ($quota->hasReachedLimit($user)) {
            return back()
                ->withInput()
                ->withErrors(['quota' => __('shop.quota_reached_merchant')]);
        }

        $order = $orders->create($user, $request->orderData(), \App\Enums\OrderSource::Manual);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', __('shop.order_created'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('orders.show', ['order' => $order]);
    }

    public function update(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        $orders->updateStatus($order, OrderStatus::from($validated['status']));

        return back()->with('status', __('shop.order_status_updated'));
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('status', __('shop.order_deleted'));
    }

    /** Count of orders per status for the filter chips. */
    private function statusCounts($user): array
    {
        $counts = $user->orders()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        return collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $s) => [$s->value => (int) ($counts[$s->value] ?? 0)])
            ->all();
    }
}
