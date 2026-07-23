<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'revenue' => (int) Order::paid()->sum('total'),
                'orders' => Order::count(),
                'pending_orders' => Order::whereIn('status', [
                    OrderStatus::Pending->value,
                    OrderStatus::AwaitingPayment->value,
                ])->count(),
                'customers' => User::where('role', 'user')->count(),
                'services' => Service::count(),
                'products' => Product::count(),
            ],
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
            'upcomingBookings' => Booking::with('service', 'user')->upcoming()->take(8)->get(),
        ]);
    }
}
