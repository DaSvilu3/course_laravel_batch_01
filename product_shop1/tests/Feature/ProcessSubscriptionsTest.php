<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProcessSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_expires_ended_subscriptions(): void
    {
        $expired = Subscription::factory()->expired()->create();
        $healthy = Subscription::factory()->active()->create();

        $this->artisan('subscriptions:process')->assertSuccessful();

        $this->assertSame(SubscriptionStatus::Expired, $expired->fresh()->status);
        $this->assertSame(SubscriptionStatus::Active, $healthy->fresh()->status);
    }

    public function test_it_reminds_subscriptions_ending_soon(): void
    {
        Notification::fake();
        config(['subscriptions.reminder_days' => 3]);

        $soon = Subscription::factory()->expiring(2)->for(User::factory())->create();
        $later = Subscription::factory()->active()->create(); // ends in a month

        $this->artisan('subscriptions:process')->assertSuccessful();

        Notification::assertSentTo($soon->user, SubscriptionExpiringNotification::class);
        $this->assertNotNull($soon->fresh()->renewal_reminded_at);

        Notification::assertNotSentTo($later->user, SubscriptionExpiringNotification::class);
    }

    public function test_a_reminder_is_only_sent_once(): void
    {
        Notification::fake();

        $subscription = Subscription::factory()->expiring(2)->create();

        $this->artisan('subscriptions:process');
        $this->artisan('subscriptions:process');

        Notification::assertSentToTimes($subscription->user, SubscriptionExpiringNotification::class, 1);
    }

    public function test_dry_run_changes_nothing(): void
    {
        $expired = Subscription::factory()->expired()->create();

        $this->artisan('subscriptions:process --dry-run')->assertSuccessful();

        $this->assertSame(SubscriptionStatus::Active, $expired->fresh()->status);
    }
}
