<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Free tier: how many orders per day before an upgrade is required. */
    public const DEFAULT_DAILY_ORDER_LIMIT = 10;

    protected $fillable = [
        'name',
        'store_name',
        'store_slug',
        'logo_path',
        'email',
        'password',
        'role',
        'phone',
        'whatsapp',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Give every merchant a unique public handle for their intake link.
        static::creating(function (User $user) {
            $user->store_slug ??= self::generateStoreSlug($user->store_name ?: $user->name);
        });
    }

    /** A unique, URL-safe handle derived from a name (falls back to random). */
    public static function generateStoreSlug(?string $from = null): string
    {
        $base = Str::slug((string) $from);

        // Arabic names slug to empty — fall back to a readable random handle.
        if ($base === '') {
            $base = 'store';
        }

        $slug = $base;
        while (self::where('store_slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(4));
        }

        return $slug;
    }

    public function publicIntakeUrl(): ?string
    {
        return $this->store_slug ? route('intake.show', $this->store_slug) : null;
    }

    public function displayStoreName(): string
    {
        return $this->store_name ?: $this->name;
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->logo_path) : null;
    }

    // ---------------------------------------------------------------- relations

    public function merchantOrders(): HasMany
    {
        return $this->hasMany(MerchantOrder::class)->latest();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    // ----------------------------------------------------------- subscriptions

    /** The subscription that currently grants access, if any. */
    public function activeSubscription(): ?Subscription
    {
        return $this->relationLoaded('subscriptions')
            ? $this->subscriptions->first(fn (Subscription $s) => $s->isActive())
            : $this->subscriptions()->active()->latest('ends_at')->get()
                ->first(fn (Subscription $s) => $s->isActive());
    }

    public function subscribed(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function onPlan(string $slug): bool
    {
        return $this->activeSubscription()?->plan?->slug === $slug;
    }

    public function currentPlan(): ?Plan
    {
        return $this->activeSubscription()?->plan;
    }

    /**
     * Read a plan feature for the active subscription.
     * $user->planFeature('max_projects', 0)
     */
    public function planFeature(string $key, mixed $default = null): mixed
    {
        return $this->currentPlan()?->feature($key, $default) ?? $default;
    }

    /** Truthy feature flag on the active plan (e.g. 'api_access'). */
    public function hasFeature(string $key): bool
    {
        return (bool) $this->planFeature($key, false);
    }

    // ------------------------------------------------------------ order quotas

    /** Orders allowed per day. -1 means unlimited. Free tier defaults to 10. */
    public function dailyOrderLimit(): int
    {
        return (int) $this->planFeature('daily_orders', self::DEFAULT_DAILY_ORDER_LIMIT);
    }

    /** Orders allowed per month. -1 means unlimited. */
    public function monthlyOrderLimit(): int
    {
        return (int) $this->planFeature('monthly_orders', -1);
    }

    public function ordersTodayCount(): int
    {
        return $this->merchantOrders()->today()->count();
    }

    public function ordersThisMonthCount(): int
    {
        return $this->merchantOrders()->thisMonth()->count();
    }

    /** Whether the merchant can still receive an order under their plan limits. */
    public function canAcceptOrder(): bool
    {
        $daily = $this->dailyOrderLimit();
        if ($daily !== -1 && $this->ordersTodayCount() >= $daily) {
            return false;
        }

        $monthly = $this->monthlyOrderLimit();
        if ($monthly !== -1 && $this->ordersThisMonthCount() >= $monthly) {
            return false;
        }

        return true;
    }

    // ------------------------------------------------------------------- roles

    public function hasRole(UserRole|string $role): bool
    {
        $role = $role instanceof UserRole ? $role : UserRole::tryFrom($role);

        return $role !== null && $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    // ------------------------------------------------------------------ scopes

    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::Admin);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
