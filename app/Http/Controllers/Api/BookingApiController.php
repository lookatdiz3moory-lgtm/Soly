<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Offer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * BookingApiController
 *
 * JSON API — consumed exclusively by the booking form's JavaScript.
 *
 * POST /api/booking
 *   → { success, reference, redirect } on success
 *   → { success:false, errors } on validation failure
 *
 * POST /api/booking/validate-slot
 *   → { success, available } — non-destructive slot check
 */
class BookingApiController extends Controller
{
    /* ── Create Appointment ─────────────────────────────────── */

    /**
     * Validate, create, and persist a new appointment.
     * Returns JSON — the booking form JS handles the redirect.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $doctor  = Doctor::active()->findOrFail((int)$data['doctor_id']);
        $service = Service::bookable()->findOrFail((int)$data['service_id']);

        // Compute slot_end from doctor's slot_duration
        $slotEnd = date('H:i', strtotime($data['slot_start']) + (($doctor->slot_duration ?? 30) * 60));

        // Final slot availability check — race condition guard
        $occupied = Appointment::forDoctor($doctor->id)
            ->forDate($data['appointment_date'])
            ->active()
            ->where('slot_start', '<', $slotEnd)
            ->where('slot_end',   '>', $data['slot_start'])
            ->exists();

        if ($occupied) {
            return $this->error(
                'This time slot was just taken. Please select another time.',
                ['slot_start' => ['This slot is no longer available.']]
            );
        }

        // Find or create a patient User record
        $patientUser = $this->resolvePatient($data);

        // Wrap in a transaction — both the user upsert and appointment must succeed together
        $appointment = DB::transaction(function () use ($data, $doctor, $service, $slotEnd, $patientUser): Appointment {

            $autoConfirm = config('clinic.booking_confirmation', 'auto') === 'auto';

            return Appointment::create([
                'reference'        => Appointment::generateReference(),
                'doctor_id'        => $doctor->id,
                'service_id'       => $service->id,
                'offer_id'         => isset($data['offer_id']) ? (int)$data['offer_id'] : null,
                'user_id'          => $patientUser?->id,
                'patient_name'     => $data['patient_name'],
                'patient_phone'    => $this->normalisePhone($data['patient_phone']),
                'patient_email'    => $data['patient_email'] ?? null,
                'appointment_date' => $data['appointment_date'],
                'slot_start'       => $data['slot_start'],
                'slot_end'         => $slotEnd,
                'notes'            => $data['notes'] ?? null,
                'source'           => Appointment::SOURCE_WEBSITE,
                'status'           => $autoConfirm
                    ? Appointment::STATUS_CONFIRMED
                    : Appointment::STATUS_PENDING,
                'confirmed_at'     => $autoConfirm ? now() : null,
                'price_quoted'     => $service->price_from,
                'payment_status'   => 'unpaid',
            ]);
        });

        return response()->json([
            'success'   => true,
            'message'   => 'Appointment booked successfully!',
            'reference' => $appointment->reference,
            'redirect'  => route('booking.confirm', $appointment->reference),
        ], 201);
    }

    /* ── Validate Slot (non-destructive) ────────────────────── */

    /**
     * Check if a slot is still free without creating an appointment.
     * Called by the booking form when the user selects a time slot.
     */
    public function validateSlot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id'        => ['required', 'integer', 'min:1'],
            'appointment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'slot_start'       => ['required', 'date_format:H:i'],
        ]);

        $doctor  = Doctor::active()->find((int)$validated['doctor_id']);
        if (!$doctor) {
            return $this->error('Doctor not found.', 404);
        }

        $slotEnd = date('H:i', strtotime($validated['slot_start']) + (($doctor->slot_duration ?? 30) * 60));

        $available = !Appointment::forDoctor($doctor->id)
            ->forDate($validated['appointment_date'])
            ->active()
            ->where('slot_start', '<', $slotEnd)
            ->where('slot_end',   '>', $validated['slot_start'])
            ->exists();

        return response()->json([
            'success'   => true,
            'available' => $available,
        ]);
    }

    /* ── Private Helpers ────────────────────────────────────── */

    /**
     * Find or create a patient user record.
     * Never overwrites password or role of an existing user.
     */
    private function resolvePatient(array $data): ?User
    {
        try {
            $phone = $this->normalisePhone($data['patient_phone']);
            $email = $data['patient_email'] ?? null;

            // Try to match by phone first
            $user = User::where('phone', $phone)->first();

            if (!$user && $email) {
                $user = User::where('email', $email)->where('role', User::ROLE_PATIENT)->first();
            }

            if ($user) {
                // Keep profile fresh — never touch password or role
                $user->fill(array_filter([
                    'name'  => $data['patient_name'],
                    'phone' => $phone,
                    'email' => $email ?: $user->email,
                ]))->save();
                return $user;
            }

            return User::create([
                'name'     => $data['patient_name'],
                'phone'    => $phone,
                'email'    => $email ?? $phone . '@patient.local',
                'password' => bcrypt(bin2hex(random_bytes(16))), // random, not for login
                'role'     => User::ROLE_PATIENT,
                'is_active'=> true,
            ]);
        } catch (\Throwable) {
            // Never let user resolution fail the booking
            return null;
        }
    }

    /**
     * Strip spaces/dashes, add Egypt country code where missing.
     */
    private function normalisePhone(string $phone): string
    {
        $clean = preg_replace('/[\s\-\(\)]/', '', $phone);

        if (str_starts_with($clean, '01') && strlen($clean) === 11) {
            $clean = '+2' . $clean;
        }

        return $clean;
    }

    private function success(array $data, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }

    private function error(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
