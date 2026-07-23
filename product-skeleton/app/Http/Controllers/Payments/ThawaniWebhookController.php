<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Payments\Exceptions\PaymentException;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Optional but recommended: Thawani can call this when a payment settles, so
 * orders complete even if the customer closes the tab before being redirected.
 *
 * Configure the URL in the Thawani merchant portal:
 *   https://your-domain.com/webhooks/thawani
 *
 * We do not trust the body: we take the session id out of it and ask the API
 * directly what the status is.
 */
class ThawaniWebhookController extends Controller
{
    public function __invoke(Request $request, CheckoutService $checkout): JsonResponse
    {
        if (! $this->hasValidSecret($request)) {
            Log::warning('Thawani webhook rejected: bad secret', ['ip' => $request->ip()]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $sessionId = $request->input('data.session_id')
            ?? $request->input('session_id');

        if (! $sessionId) {
            return response()->json(['message' => 'Missing session id'], 422);
        }

        $payment = Payment::where('session_id', $sessionId)->first();

        if (! $payment) {
            Log::warning('Thawani webhook for unknown session', ['session_id' => $sessionId]);

            // 200 so Thawani stops retrying a session we do not own.
            return response()->json(['message' => 'Ignored']);
        }

        try {
            $checkout->settle($payment);
        } catch (PaymentException $e) {
            Log::error('Thawani webhook verification failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            // 500 asks Thawani to retry later.
            return response()->json(['message' => 'Verification failed'], 500);
        }

        return response()->json(['message' => 'OK']);
    }

    private function hasValidSecret(Request $request): bool
    {
        $expected = config('payments.thawani.webhook_secret');

        if (blank($expected)) {
            return true; // No secret configured: accept (verification still happens).
        }

        $given = $request->header('X-Webhook-Secret', $request->input('secret', ''));

        return is_string($given) && hash_equals($expected, $given);
    }
}
