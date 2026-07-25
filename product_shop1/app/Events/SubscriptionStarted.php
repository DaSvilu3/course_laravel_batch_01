<?php

namespace App\Events;

use App\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a subscription becomes active (new signup or a renewal that
 * reactivates). Hook provisioning, welcome emails, etc. onto this.
 */
class SubscriptionStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(public Subscription $subscription) {}
}
