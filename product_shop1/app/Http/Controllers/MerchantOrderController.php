<?php

namespace App\Http\Controllers;

use App\Enums\MerchantOrderStatus;
use App\Models\MerchantOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The merchant's own order list, details, status management and analytics.
 */
class MerchantOrderController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('status');

        $orders = $request->user()->merchantOrders()
            ->when(
                in_array($filter, array_column(MerchantOrderStatus::cases(), 'value'), true),
                fn ($q) => $q->where('status', $filter)
            )
            ->paginate(15)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'statuses' => MerchantOrderStatus::cases(),
            'filter' => $filter,
        ]);
    }

    public function show(Request $request, MerchantOrder $merchantOrder): View
    {
        abort_unless($merchantOrder->user_id === $request->user()->id, 403);

        $merchantOrder->load('events');

        return view('orders.show', [
            'order' => $merchantOrder,
            'statuses' => MerchantOrderStatus::cases(),
        ]);
    }

    public function update(Request $request, MerchantOrder $merchantOrder): RedirectResponse
    {
        abort_unless($merchantOrder->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(MerchantOrderStatus::class)],
        ]);

        $merchantOrder->changeStatus(MerchantOrderStatus::from($validated['status']));

        return back()->with('status', __('orders.status_updated'));
    }

    public function analytics(Request $request): View
    {
        $user = $request->user();
        $orders = $user->merchantOrders();

        // ---- Orders per day for the last 14 days ----
        $days = 14;
        $since = today()->subDays($days - 1);
        $rows = (clone $orders)->where('created_at', '>=', $since)->get(['created_at']);

        $daily = [];
        for ($i = 0; $i < $days; $i++) {
            $date = today()->subDays($days - 1 - $i);
            $daily[] = [
                'label' => $date->isoFormat('D/M'),
                'weekday' => $date->isoFormat('dd'),
                'count' => $rows->filter(fn ($o) => $o->created_at->isSameDay($date))->count(),
            ];
        }
        $maxDaily = max(1, collect($daily)->max('count'));

        // ---- Breakdown by status ----
        $counts = (clone $orders)->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $total = (int) $counts->sum();
        $byStatus = [];
        foreach (MerchantOrderStatus::cases() as $status) {
            $c = (int) ($counts[$status->value] ?? 0);
            $byStatus[] = [
                'status' => $status,
                'count' => $c,
                'percent' => $total > 0 ? round($c / $total * 100) : 0,
            ];
        }

        $delivered = (int) ($counts[MerchantOrderStatus::Delivered->value] ?? 0);
        $revenue = (int) (clone $orders)->where('status', MerchantOrderStatus::Delivered->value)->sum('amount');

        return view('analytics.index', [
            'total' => $total,
            'delivered' => $delivered,
            'deliveryRate' => $total > 0 ? round($delivered / $total * 100) : 0,
            'revenue' => $revenue,
            'avgOrder' => $delivered > 0 ? (int) round($revenue / $delivered) : 0,
            'ordersThisMonth' => $user->ordersThisMonthCount(),
            'daily' => $daily,
            'maxDaily' => $maxDaily,
            'byStatus' => $byStatus,
        ]);
    }
}
