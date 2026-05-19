<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AvailabilityController
 *
 * JSON API — consumed exclusively by the booking form's JavaScript.
 *
 * GET /api/available-slots
 *   ?doctor_id=1&date=2025-01-15
 *   → { success, slots: [{ start, end }] }
 *
 * GET /api/doctors-by-service/{serviceId}
 *   → { success, doctors: [...] }
 */
class AvailabilityController extends Controller
{
    /* ── Available Slots ────────────────────────────────────── */

    /**
     * Return available time slots for a doctor on a given date.
     */
    public function slots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'integer', 'min:1'],
            'date'      => ['required', 'date_format:Y-m-d'],
        ]);

        $date = $validated['date'];

        // Reject past dates
        if ($date < now()->toDateString()) {
            return $this->error('Cannot check availability for past dates.', 422);
        }

        // Reject dates too far in advance
        $maxDate = now()->addDays((int) config('clinic.booking_advance_days', 60))->toDateString();
        if ($date > $maxDate) {
            return $this->error("Appointments can only be booked up to " . config('clinic.booking_advance_days', 60) . " days in advance.", 422);
        }

        $doctor = Doctor::active()->find((int)$validated['doctor_id']);

        if (!$doctor) {
            return $this->error('Doctor not found or not available.', 404);
        }

        // availableSlots() subtracts booked + blocked slots
        $slots = $doctor->availableSlots($date);

        return $this->success([
            'slots'     => array_values($slots),
            'count'     => count($slots),
            'doctor_id' => $doctor->id,
            'date'      => $date,
        ]);
    }

    /* ── Doctors by Service ─────────────────────────────────── */

    /**
     * Return active doctors who perform a given service.
     * Used to populate the doctor selector after the patient picks a service.
     */
    public function doctorsByService(Request $request, int $serviceId): JsonResponse
    {
        $service = Service::bookable()->find($serviceId);

        if (!$service) {
            return $this->error('Service not found or not available for booking.', 404);
        }

        $doctors = Doctor::forService($serviceId)
            ->with('services:id,name,slug')
            ->get()
            ->map(fn(Doctor $d) => [
                'id'            => $d->id,
                'name'          => $d->name,
                'title'         => $d->title,
                'specialty'     => $d->specialty,
                'photo_url'     => $d->photo_url,
                'experience'    => $d->experience,
                'slot_duration' => $d->slot_duration ?? 30,
            ]);

        if ($doctors->isEmpty()) {
            return $this->error('No doctors are currently available for this service.', 404);
        }

        return $this->success([
            'doctors'    => $doctors->values(),
            'service_id' => $serviceId,
        ]);
    }

    /* ── Response Helpers ───────────────────────────────────── */

    private function success(array $data, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }

    private function error(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
