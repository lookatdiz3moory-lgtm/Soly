<?php

namespace App\Policies;

use App\Models\User;

class TestimonialPolicy extends BasePolicy
{
    // Secretaries can add testimonials and approve them, but cannot delete.
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSecretary();
    }

    public function update(User $user, $testimonial = null): bool
    {
        return $user->isAdmin() || $user->isSecretary();
    }

    public function approve(User $user): bool
    {
        return $user->isAdmin() || $user->isSecretary();
    }
}
