<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            || in_array($user->role, ['admin', 'manager'], true);
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            && in_array($booking->status, ['pending', 'approved'], true)
            && $booking->check_in_at->isFuture();
    }
}
