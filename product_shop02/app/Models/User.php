<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
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

    // ---------------------------------------------------------------- relations

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
