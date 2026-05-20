<?php

namespace App\Policies;

use App\Models\User;

/**
 * BasePolicy
 *
 * Shared role-based helpers used by every concrete policy.
 *
 * Role capability matrix (defaults — policies may override):
 *
 *   capability     | superadmin | admin | secretary | doctor
 *   --------------|------------|-------|-----------|-------
 *   viewAny       |    yes     | yes   |   yes     |  yes
 *   view          |    yes     | yes   |   yes     |  yes
 *   create        |    yes     | yes   |   no      |  no
 *   update        |    yes     | yes   |   no      |  no
 *   delete        |    yes     | no    |   no      |  no
 *   forceDelete   |    yes     | no    |   no      |  no
 *   restore       |    yes     | yes   |   no      |  no
 *
 * Individual policies (AppointmentPolicy, GalleryPolicy, etc.) widen
 * permissions for specific cases — e.g. secretaries can update
 * appointments, doctors can view their own schedule.
 */
abstract class BasePolicy
{
    /**
     * Laravel calls before() on every gate check.
     * Returning true grants permission unconditionally; returning null
     * lets the individual ability method decide.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (!$user->is_active) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /* ── Defaults — admin can do most things ────────────────── */

    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, $model = null): bool
    {
        return $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, $model = null): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, $model = null): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, $model = null): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, $model = null): bool
    {
        return false; // superadmin only via before()
    }
}
