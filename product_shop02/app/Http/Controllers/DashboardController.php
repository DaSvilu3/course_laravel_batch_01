<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Services\OrderQuotaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, OrderQuotaService $quota): View
    {
        $user = $request->user();

        return view('dashboard', [
            'todayCount' => $user->orders()->whereDate('created_at', today())->count(),
            'monthCount' => $user->orders()->where('created_at', '>=', now()->startOfMonth())->count(),
            'totalCount' => $user->orders()->count(),
            'openCount' => $user->orders()->whereIn('status', [
                OrderStatus::New->value,
                OrderStatus::InProgress->value,
            ])->count(),
            'recentOrders' => $user->orders()->latest()->take(8)->get(),
            'quota' => $quota,
            'plan' => $quota->planFor($user),
            'subscription' => $user->activeSubscription(),
        ]);
    }
}
