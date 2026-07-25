<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $subscription = $user->activeSubscription();

        return view('dashboard', [
            'subscription' => $subscription,
            'plan' => $subscription?->plan,
            'payments' => $user->payments()->latest()->take(5)->get(),
            'totalPaid' => (int) $user->payments()
                ->where('status', PaymentStatus::Paid->value)
                ->sum('amount'),
        ]);
    }
}
