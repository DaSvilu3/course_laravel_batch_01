<?php

namespace App\Http\Controllers\Intake;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The public order intake form living at /o/{store_slug}. A customer fills it
 * in after agreeing the order on WhatsApp; it creates a MerchantOrder and
 * hands back a tracker code.
 */
class PublicOrderController extends Controller
{
    public function show(User $merchant): View
    {
        return view('intake.show', [
            'merchant' => $merchant,
            'full' => ! $merchant->canAcceptOrder(),
        ]);
    }

    public function store(Request $request, User $merchant): RedirectResponse
    {
        if (! $merchant->canAcceptOrder()) {
            return back()->with('error', __('orders.intake_closed'));
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:32'],
            'customer_location' => ['nullable', 'string', 'max:255'],
            'item_description' => ['required', 'string', 'max:2000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            // Secure image rules: `image` re-checks the actual file contents,
            // `mimes` whitelists raster formats only (no SVG — XSS risk).
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        // Laravel's store() writes with a random hashed name (the user's
        // filename is never trusted) on the non-executable public disk.
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('order-images', 'public')
            : null;

        $order = $merchant->merchantOrders()->create([
            ...collect($validated)->except('image', 'amount')->all(),
            'amount' => isset($validated['amount']) ? Money::toBaisa($validated['amount']) : null,
            'image_path' => $imagePath,
        ]);

        $order->logStatus(); // first history entry: "new"

        return redirect()
            ->route('intake.received', $merchant->store_slug)
            ->with('tracker_code', $order->tracker_code);
    }

    public function received(Request $request, User $merchant): View|RedirectResponse
    {
        $code = $request->session()->get('tracker_code');

        if (! $code) {
            return redirect()->route('intake.show', $merchant->store_slug);
        }

        return view('intake.success', [
            'merchant' => $merchant,
            'trackerCode' => $code,
        ]);
    }
}
