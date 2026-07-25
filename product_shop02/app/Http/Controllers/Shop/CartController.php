<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly Cart $cart) {}

    public function index(): View
    {
        return view('shop.cart', ['cart' => $this->cart]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', array_keys(Cart::TYPES))],
            'id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'starts_at' => ['nullable', 'date', 'after:now'],
        ]);

        $purchasable = Cart::resolve($data['type'], $data['id']);
        $quantity = (int) ($data['quantity'] ?? 1);

        if (! $purchasable || ! $purchasable->isPurchasable($quantity)) {
            return back()->withErrors(['cart' => __('shop.item_unavailable')]);
        }

        $this->cart->add(
            $purchasable,
            $quantity,
            array_filter(['starts_at' => $data['starts_at'] ?? null]),
        );

        return redirect()->route('cart.index')->with('status', __('shop.added_to_cart'));
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $this->cart->update($key, (int) $data['quantity']);

        return back()->with('status', __('shop.cart_updated'));
    }

    public function destroy(string $key): RedirectResponse
    {
        $this->cart->remove($key);

        return back()->with('status', __('shop.cart_updated'));
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back()->with('status', __('shop.cart_cleared'));
    }
}
