<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Payments\Exceptions\PaymentException;
use App\Payments\Gateways\ThawaniGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The Thawani driver is tested against a faked HTTP layer — no network,
 * no keys, but the exact request shape Thawani expects is asserted.
 */
class ThawaniGatewayTest extends TestCase
{
    use RefreshDatabase;

    private function gateway(): ThawaniGateway
    {
        return new ThawaniGateway([
            'mode' => 'test',
            'secret_key' => 'test_secret',
            'publishable_key' => 'test_pub',
            'timeout' => 10,
            'endpoints' => config('payments.thawani.endpoints'),
        ]);
    }

    private function order(): Order
    {
        $order = Order::factory()->create(['subtotal' => 25_000, 'total' => 25_000]);

        $order->items()->create([
            'purchasable_type' => 'service',
            'purchasable_id' => 1,
            'name' => 'Business consultation',
            'unit_price' => 25_000,
            'quantity' => 1,
            'total' => 25_000,
        ]);

        return $order->load('items');
    }

    public function test_it_creates_a_checkout_session(): void
    {
        Http::fake([
            '*/checkout/session' => Http::response([
                'success' => true,
                'code' => 2004,
                'data' => ['session_id' => 'sess_123', 'payment_status' => 'unpaid'],
            ]),
        ]);

        $order = $this->order();

        $session = $this->gateway()->createSession($order, 'https://app.test/ok', 'https://app.test/cancel');

        $this->assertSame('sess_123', $session->sessionId);
        $this->assertSame('https://uatcheckout.thawani.om/pay/sess_123?key=test_pub', $session->redirectUrl);

        Http::assertSent(function (Request $request) use ($order) {
            $body = $request->data();

            return $request->hasHeader('thawani-api-key', 'test_secret')
                && $body['client_reference_id'] === $order->number
                && $body['mode'] === 'payment'
                // Amounts go over the wire in baisa, as integers.
                && $body['products'][0]['unit_amount'] === 25_000
                && $body['products'][0]['quantity'] === 1
                && $body['success_url'] === 'https://app.test/ok';
        });
    }

    public function test_it_throws_when_thawani_rejects_the_request(): void
    {
        Http::fake([
            '*/checkout/session' => Http::response(['description' => 'Invalid key'], 401),
        ]);

        $this->expectException(PaymentException::class);

        $this->gateway()->createSession($this->order(), 'https://app.test/ok', 'https://app.test/cancel');
    }

    public function test_it_throws_when_no_secret_key_is_configured(): void
    {
        $gateway = new ThawaniGateway([
            'mode' => 'test',
            'secret_key' => null,
            'endpoints' => config('payments.thawani.endpoints'),
        ]);

        $this->expectException(PaymentException::class);

        $gateway->createSession($this->order(), 'https://app.test/ok', 'https://app.test/cancel');
    }

    public function test_it_maps_thawani_statuses(): void
    {
        Http::fake([
            '*/checkout/session/sess_123' => Http::response([
                'data' => [
                    'payment_status' => 'paid',
                    'total_amount' => 25_000,
                    'invoice' => 'INV-9',
                ],
            ]),
        ]);

        $result = $this->gateway()->verify('sess_123');

        $this->assertTrue($result->isPaid());
        $this->assertSame(PaymentStatus::Paid, $result->status);
        $this->assertSame(25_000, $result->amount);
        $this->assertSame('INV-9', $result->reference);

        $this->assertSame(PaymentStatus::Cancelled, PaymentStatus::fromThawani('cancelled'));
        $this->assertSame(PaymentStatus::Failed, PaymentStatus::fromThawani('expired'));
        $this->assertSame(PaymentStatus::Pending, PaymentStatus::fromThawani('unpaid'));
    }

    public function test_live_mode_uses_the_production_endpoints(): void
    {
        Http::fake(['*' => Http::response(['data' => ['session_id' => 'sess_live']])]);

        $gateway = new ThawaniGateway([
            'mode' => 'live',
            'secret_key' => 'live_secret',
            'publishable_key' => 'live_pub',
            'endpoints' => config('payments.thawani.endpoints'),
        ]);

        $session = $gateway->createSession($this->order(), 'https://app.test/ok', 'https://app.test/cancel');

        $this->assertSame('https://checkout.thawani.om/pay/sess_live?key=live_pub', $session->redirectUrl);
        Http::assertSent(fn (Request $r) => str_starts_with($r->url(), 'https://checkout.thawani.om/api/v1'));
    }
}
