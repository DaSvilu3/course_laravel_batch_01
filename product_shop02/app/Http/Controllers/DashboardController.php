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

        // This month's orders — the basis for the shareable monthly report.
        $monthStart = now()->startOfMonth();
        $monthOrders = $user->orders()->where('created_at', '>=', $monthStart);

        $topGovernorates = (clone $monthOrders)
            ->whereNotNull('governorate')
            ->selectRaw('governorate, count(*) as aggregate')
            ->groupBy('governorate')
            ->orderByDesc('aggregate')
            ->take(3)
            ->get()
            ->map(fn ($row) => [
                'label' => \App\Support\Regions::governorateLabel($row->governorate),
                'count' => (int) $row->aggregate,
            ]);

        $report = [
            'month' => now()->translatedFormat('F Y'),
            'total' => (clone $monthOrders)->count(),
            'completed' => (clone $monthOrders)->where('status', OrderStatus::Completed->value)->count(),
            'cancelled' => (clone $monthOrders)->where('status', OrderStatus::Cancelled->value)->count(),
            'value' => (int) (clone $monthOrders)->where('status', '!=', OrderStatus::Cancelled->value)->sum('price'),
            'top_governorates' => $topGovernorates,
        ];

        return view('dashboard', [
            'todayCount' => $user->orders()->whereDate('created_at', today())->count(),
            'monthCount' => (clone $monthOrders)->count(),
            'totalCount' => $user->orders()->count(),
            'openCount' => $user->orders()->whereIn('status', [
                OrderStatus::New->value,
                OrderStatus::InProgress->value,
            ])->count(),
            'recentOrders' => $user->orders()->latest()->take(8)->get(),
            'quota' => $quota,
            'plan' => $quota->planFor($user),
            'subscription' => $user->activeSubscription(),
            'report' => $report,
        ]);
    }
}
