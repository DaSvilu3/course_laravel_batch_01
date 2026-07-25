<?php

namespace App\Http\Controllers;

use App\Enums\MerchantOrderStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The merchant home: intake link, order quota, live stats and the latest
 * orders that came in through the public form.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $openStatuses = [
            MerchantOrderStatus::New->value,
            MerchantOrderStatus::Confirmed->value,
            MerchantOrderStatus::Preparing->value,
            MerchantOrderStatus::OutForDelivery->value,
        ];

        return view('dashboard', [
            'subscription' => $user->activeSubscription(),
            'ordersToday' => $user->ordersTodayCount(),
            'dailyLimit' => $user->dailyOrderLimit(),
            'ordersThisMonth' => $user->ordersThisMonthCount(),
            'monthlyLimit' => $user->monthlyOrderLimit(),
            'openCount' => $user->merchantOrders()->whereIn('status', $openStatuses)->count(),
            'deliveredCount' => $user->merchantOrders()
                ->where('status', MerchantOrderStatus::Delivered->value)->count(),
            'recentOrders' => $user->merchantOrders()->take(8)->get(),
            'statuses' => MerchantOrderStatus::cases(),
        ]);
    }
}
