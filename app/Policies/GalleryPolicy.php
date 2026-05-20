<?php

namespace App\Policies;

use App\Models\User;

class GalleryPolicy extends BasePolicy
{
    // Secretaries can upload gallery items but not delete them.
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSecretary();
    }

    public function update(User $user, $gallery = null): bool
    {
        return $user->isAdmin() || $user->isSecretary();
    }
}
