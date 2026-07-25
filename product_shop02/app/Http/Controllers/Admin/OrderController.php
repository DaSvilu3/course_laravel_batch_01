<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($w) => $w->where('number', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
                    ->orWhere('customer_email', 'like', $term));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('items.purchasable', 'payments', 'user'),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_column(OrderStatus::cases(), 'value'))],
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with('status', __('admin.saved'));
    }
}
