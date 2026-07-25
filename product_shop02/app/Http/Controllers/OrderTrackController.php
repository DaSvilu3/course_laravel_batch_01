<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public order tracking. A customer enters the tracker code they were given and
 * sees the current status — no account needed.
 */
class OrderTrackController extends Controller
{
    public function index(): View
    {
        return view('public.track');
    }

    /** Handle the "enter your code" form → redirect to the clean status URL. */
    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
        ]);

        return redirect()->route('track.show', ['code' => trim($validated['code'])]);
    }

    public function show(string $code): View
    {
        $order = Order::where('tracker_code', $code)->first();

        if (! $order) {
            return view('public.track', [
                'notFound' => true,
                'code' => $code,
            ]);
        }

        return view('public.tracked', [
            'order' => $order,
            'merchant' => $order->merchant,
        ]);
    }
}
