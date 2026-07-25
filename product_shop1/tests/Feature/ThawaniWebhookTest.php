<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThawaniWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function payment(array $attributes = []): Payment
    {
        $order = Order::factory()->create(['total' => 25_000]);

        return $order->payments()->create(array_merge([
            'user_id' => $order->user_id,
            'gateway' => 'fake',
            'status' => PaymentStatus::Pending,
            'session_id' => 'sess_hook',
            'amount' => 25_000,
            'currency' => 'OMR',
            'payload' => ['simulated_status' => 'paid'],
        ], $attributes));
    }

    public function test_it_settles_a_payment_from_the_session_id(): void
    {
        $payment = $this->payment();

        $this->postJson(route('webhooks.thawani'), [
            'data' => ['session_id' => 'sess_hook'],
        ])->assertOk();

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Paid, $payment->order->fresh()->status);
    }

    public function test_unknown_sessions_are_ignored(): void
    {
        $this->postJson(route('webhooks.thawani'), [
            'data' => ['session_id' => 'nope'],
        ])->assertOk();
    }

    public function test_a_missing_session_id_is_a_422(): void
    {
        $this->postJson(route('webhooks.thawani'), [])->assertStatus(422);
    }

    public function test_a_configured_secret_is_enforced(): void
    {
        config(['payments.thawani.webhook_secret' => 's3cret']);

        $payment = $this->payment();

        $this->postJson(route('webhooks.thawani'), ['data' => ['session_id' => 'sess_hook']])
            ->assertUnauthorized();

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);

        $this->withHeader('X-Webhook-Secret', 's3cret')
            ->postJson(route('webhooks.thawani'), ['data' => ['session_id' => 'sess_hook']])
            ->assertOk();

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
    }
}
