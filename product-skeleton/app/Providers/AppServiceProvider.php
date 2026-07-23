<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Payments\PaymentManager;
use App\Policies\BookingPolicy;
use App\Policies\OrderPolicy;
use App\Support\Cart;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The cart lives in the session, so one instance per request is enough.
        $this->app->scoped(Cart::class, fn ($app) => new Cart($app['session.store']));

        $this->app->singleton(PaymentManager::class, fn ($app) => new PaymentManager($app['config']['payments']));

        // Type-hint PaymentGateway anywhere and you get the configured driver.
        $this->app->bind(PaymentGateway::class, fn ($app) => $app->make(PaymentManager::class)->driver());
    }

    public function boot(): void
    {
        /*
         * Store "service" / "product" in purchasable_type instead of the full
         * class name, so renaming or moving a model does not break old rows.
         */
        Relation::enforceMorphMap([
            'service' => Service::class,
            'product' => Product::class,
        ]);

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);

        // An admin passes every authorization check.
        Gate::before(fn ($user) => $user->isAdmin() ? true : null);
    }
}
