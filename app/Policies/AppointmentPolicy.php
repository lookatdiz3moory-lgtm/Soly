<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

/**
 * Appointments are the day-to-day work of the secretary, so secretaries
 * have full create/update access. Doctors can only view their own
 * appointments.
 */
class AppointmentPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, $appointment = null): bool
    {
        if ($user->isAdmin() || $user->isSecretary()) {
            return true;
        }

        if ($user->isDoctor() && $appointment instanceof Appointment) {
            return $appointment->doctor_id === $user->id
                || $appointment->doctor?->email === $user->email;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSecretary();
    }

    public function update(User $user, $appointment = null): bool
    {
        return $user->isAdmin() || $user->isSecretary();
    }

    /**
     * Cancelling is a status update — same rule.
     */
    public function cancel(User $user, $appointment = null): bool
    {
        return $this->update($user, $appointment);
    }

    public function delete(User $user, $appointment = null): bool
    {
        // Only admin+ can hard-delete an appointment.
        return $user->isAdmin();
    }
}
