<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            && $booking->status !== BookingStatus::Cancelled
            && $booking->status !== BookingStatus::Completed;
    }
}
