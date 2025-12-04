<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

class ReservationPolicy
{
    public function update(User $user, Reservation $reservation)
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
