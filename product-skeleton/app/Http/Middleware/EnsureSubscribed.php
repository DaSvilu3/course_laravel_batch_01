<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate a route behind an active subscription — the heart of the SaaS layer.
 *
 *   ->middleware('subscribed')            any active plan
 *   ->middleware('subscribed:pro')        must be on the "pro" plan
 *   ->middleware('subscribed:pro,business') either of them
 *
 * Admins always pass. Everyone else is sent to the pricing page.
 */
class EnsureSubscribed
{
    public function handle(Request $request, Closure $next, string ...$plans): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        $subscription = $user->activeSubscription();

        $ok = $subscription !== null
            && ($plans === [] || in_array($subscription->plan?->slug, $plans, true));

        if (! $ok) {
            return redirect()->route('plans.index')->with('status', __('billing.subscription_required'));
        }

        return $next($request);
    }
}
