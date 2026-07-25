<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $subscriptions = Subscription::query()
            ->with('user', 'plan')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), fn ($q) => $q->whereHas(
                'user',
                fn ($u) => $u->where('name', 'like', '%'.$request->string('q').'%')
                    ->orWhere('email', 'like', '%'.$request->string('q').'%')
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscriptions.index', ['subscriptions' => $subscriptions]);
    }

    public function cancel(Subscription $subscription, SubscriptionService $service): RedirectResponse
    {
        $service->cancel($subscription, immediately: true);

        return back()->with('status', __('admin.saved'));
    }
}
