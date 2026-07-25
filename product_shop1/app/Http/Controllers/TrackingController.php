<?php

namespace App\Http\Controllers;

use App\Models\MerchantOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public order tracking: a customer enters their tracker code to see the
 * current status of their order.
 */
class TrackingController extends Controller
{
    public function show(Request $request, ?string $code = null): View
    {
        $code = $code ?? $request->query('code');
        $order = null;

        if ($code) {
            $order = MerchantOrder::with('user')
                ->where('tracker_code', strtoupper(trim($code)))
                ->first();
        }

        return view('track.show', [
            'code' => $code,
            'order' => $order,
            'notFound' => $code && ! $order,
        ]);
    }
}
