<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillingInterval;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::withCount('subscriptions')->ordered()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.plans.form', [
            'plan' => new Plan(['interval' => BillingInterval::Month, 'is_active' => true]),
        ]);
    }

    public function store(PlanRequest $request): RedirectResponse
    {
        Plan::create($request->payload());

        return redirect()->route('admin.plans.index')->with('status', __('admin.saved'));
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.form', ['plan' => $plan]);
    }

    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($request->payload());

        return redirect()->route('admin.plans.index')->with('status', __('admin.saved'));
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return back()->with('status', __('admin.deleted'));
    }
}
