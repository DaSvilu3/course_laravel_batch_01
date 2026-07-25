<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillingInterval;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Normalise every active subscription to a monthly figure for MRR.
        $mrr = Subscription::active()->get(['price', 'interval'])
            ->reduce(fn (int $carry, Subscription $s) => $carry + (
                $s->interval === BillingInterval::Year ? intdiv((int) $s->price, 12) : (int) $s->price
            ), 0);

        return view('admin.dashboard', [
            'stats' => [
                'mrr' => $mrr,
                'revenue' => (int) Payment::where('status', PaymentStatus::Paid->value)->sum('amount'),
                'active' => Subscription::active()->count(),
                'trialing' => Subscription::where('status', SubscriptionStatus::Trialing->value)->count(),
                'customers' => User::where('role', 'user')->count(),
                'orders' => Order::count(),
                'orders_today' => Order::whereDate('created_at', today())->count(),
            ],
            'planBreakdown' => Subscription::active()
                ->selectRaw('plan_name, count(*) as subscribers, sum(price) as revenue')
                ->groupBy('plan_name')
                ->orderByDesc('subscribers')
                ->get(),
            'recentSubscriptions' => Subscription::with('user')->latest()->take(6)->get(),
            'recentPayments' => Payment::with('user')
                ->where('status', PaymentStatus::Paid->value)
                ->latest('paid_at')
                ->take(6)
                ->get(),
        ]);
    }
}
