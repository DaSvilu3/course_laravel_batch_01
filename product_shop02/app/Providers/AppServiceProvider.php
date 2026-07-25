<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Models\Subscription;
use App\Payments\PaymentManager;
use App\Policies\OrderPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, fn ($app) => new PaymentManager($app['config']['payments']));

        // Type-hint PaymentGateway anywhere and you get the configured driver.
        $this->app->bind(PaymentGateway::class, fn ($app) => $app->make(PaymentManager::class)->driver());
    }

    public function boot(): void
    {
        /*
         * Store a short alias in payable_type instead of the full class name,
         * so renaming or moving a model does not break old rows.
         */
        Relation::enforceMorphMap([
            'order' => Order::class,
            'subscription' => Subscription::class,
        ]);

        Gate::policy(Order::class, OrderPolicy::class);

        // An admin passes every authorization check.
        Gate::before(fn ($user) => $user->isAdmin() ? true : null);
    }
}
