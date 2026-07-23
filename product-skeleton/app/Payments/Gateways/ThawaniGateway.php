<?php

namespace App\Payments\Gateways;

use App\Contracts\Payable;
use App\Contracts\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Payments\Data\CheckoutSession;
use App\Payments\Data\PaymentVerification;
use App\Payments\Exceptions\PaymentException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Thawani hosted checkout.
 *
 * The flow:
 *   1. POST /checkout/session          -> we get a session_id
 *   2. redirect the customer to        {checkout}/pay/{session_id}?key={publishable_key}
 *   3. Thawani sends them back to      success_url / cancel_url
 *   4. GET /checkout/session/{id}      -> we confirm the real payment status
 *
 * Step 4 matters: the redirect back to our site proves nothing on its own, so
 * we always re-ask Thawani before marking an order as paid.
 *
 * Docs: https://thawani-technologies.stoplight.io/docs/thawani-ecommerce-api
 */
class ThawaniGateway implements PaymentGateway
{
    /** Thawani rejects product names longer than this. */
    private const MAX_PRODUCT_NAME = 40;

    public function __construct(private readonly array $config) {}

    public function name(): string
    {
        return 'thawani';
    }

    public function createSession(Payable $payable, string $successUrl, string $cancelUrl): CheckoutSession
    {
        $payload = [
            'client_reference_id' => $payable->paymentReference(),
            'mode' => 'payment',
            'products' => $this->productsFor($payable),
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => array_map('strval', $payable->paymentMetadata()),
        ];

        $response = $this->request()->post('/checkout/session', $payload);

        if ($response->failed() || ! $response->json('data.session_id')) {
            Log::error('Thawani: failed to create checkout session', [
                'reference' => $payable->paymentReference(),
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            throw PaymentException::gatewayError($this->name(), 'Unable to create checkout session.', [
                'http_status' => $response->status(),
                'description' => (string) $response->json('description'),
            ]);
        }

        $sessionId = (string) $response->json('data.session_id');

        return new CheckoutSession(
            sessionId: $sessionId,
            redirectUrl: $this->hostedCheckoutUrl($sessionId),
            raw: (array) $response->json('data'),
        );
    }

    public function verify(string $sessionId): PaymentVerification
    {
        $response = $this->request()->get("/checkout/session/{$sessionId}");

        if ($response->failed()) {
            Log::error('Thawani: failed to verify session', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            throw PaymentException::gatewayError($this->name(), 'Unable to verify checkout session.', [
                'http_status' => $response->status(),
            ]);
        }

        $data = (array) $response->json('data', []);

        return new PaymentVerification(
            status: PaymentStatus::fromThawani($data['payment_status'] ?? null),
            amount: (int) ($data['total_amount'] ?? 0),
            reference: isset($data['invoice']) ? (string) $data['invoice'] : null,
            raw: $data,
        );
    }

    /**
     * Build the URL the customer is redirected to.
     */
    public function hostedCheckoutUrl(string $sessionId): string
    {
        return sprintf(
            '%s/pay/%s?key=%s',
            rtrim($this->endpoint('checkout'), '/'),
            $sessionId,
            $this->config['publishable_key'] ?? '',
        );
    }

    /**
     * Thawani wants one entry per line item, with the price in baisa. The
     * payable already returns lines that sum to its total, so we just enforce
     * Thawani's shape (integer amounts, 40-char names) here.
     */
    private function productsFor(Payable $payable): array
    {
        $products = collect($payable->paymentLineItems())
            ->map(fn (array $line) => [
                'name' => $this->safeName((string) $line['name']),
                'quantity' => max(1, (int) $line['quantity']),
                'unit_amount' => (int) $line['unit_amount'],
            ])
            ->values()
            ->all();

        if ($products === []) {
            return [[
                'name' => $this->safeName($payable->paymentReference()),
                'quantity' => 1,
                'unit_amount' => $payable->paymentTotal(),
            ]];
        }

        return $products;
    }

    private function safeName(string $name): string
    {
        return Str::limit(trim(preg_replace('/\s+/u', ' ', $name)), self::MAX_PRODUCT_NAME, '');
    }

    private function request(): PendingRequest
    {
        $secret = $this->config['secret_key'] ?? null;

        if (blank($secret)) {
            throw PaymentException::gatewayError($this->name(), 'THAWANI_SECRET_KEY is not set.');
        }

        return Http::baseUrl($this->endpoint('api'))
            ->withHeaders([
                'thawani-api-key' => $secret,
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->config['timeout'] ?? 30)
            ->acceptJson();
    }

    private function endpoint(string $key): string
    {
        $mode = $this->config['mode'] ?? 'test';

        return $this->config['endpoints'][$mode][$key]
            ?? throw PaymentException::gatewayError($this->name(), "Unknown Thawani endpoint [{$mode}.{$key}].");
    }
}
