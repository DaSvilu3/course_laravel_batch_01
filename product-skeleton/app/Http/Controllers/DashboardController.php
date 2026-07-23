<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'orders' => $user->orders()->latest()->take(5)->get(),
            'ordersCount' => $user->orders()->count(),
            'spent' => (int) $user->orders()->paid()->sum('total'),
            'bookings' => $user->bookings()->with('service')->upcoming()->take(5)->get(),
        ]);
    }
}
