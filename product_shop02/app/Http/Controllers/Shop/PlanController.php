<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\View\View;

class PlanController extends Controller
{
    /** Public pricing page. */
    public function index(): View
    {
        return view('shop.plans', [
            'plans' => Plan::active()->ordered()->get(),
            'current' => auth()->user()?->activeSubscription(),
        ]);
    }
}
