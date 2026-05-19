<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * StoreAppointmentRequest
 *
 * Validates the JSON payload sent by the booking form's JS.
 * On failure returns JSON (not a redirect) because the form uses AJAX.
 */
class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public booking — no auth required
    }

    public function rules(): array
    {
        $maxDate = now()->addDays((int) config('clinic.booking_advance_days', 60))->toDateString();

        return [
            // Patient identity
            'patient_name'  => ['required', 'string', 'min:2', 'max:100'],
            'patient_phone' => ['required', 'string', 'regex:/^(\+2)?01[0125]\d{8}$/'],
            'patient_email' => ['nullable', 'email', 'max:150'],

            // Appointment
            'service_id'       => ['required', 'integer', 'exists:services,id'],
            'doctor_id'        => ['required', 'integer', 'exists:doctors,id'],
            'offer_id'         => ['nullable', 'integer', 'exists:offers,id'],
            'appointment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', "before_or_equal:{$maxDate}"],
            'slot_start'       => ['required', 'date_format:H:i'],

            // Optional
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_name.required'      => 'Your full name is required.',
            'patient_name.min'           => 'Please enter your full name.',
            'patient_phone.required'     => 'A phone number is required.',
            'patient_phone.regex'        => 'Please enter a valid Egyptian mobile number (e.g. 0100 000 0000).',
            'patient_email.email'        => 'Please enter a valid email address.',
            'service_id.required'        => 'Please select a service.',
            'service_id.exists'          => 'The selected service is not available.',
            'doctor_id.required'         => 'Please select a doctor.',
            'doctor_id.exists'           => 'The selected doctor is not available.',
            'appointment_date.required'  => 'Please select an appointment date.',
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past.',
            'appointment_date.before_or_equal'=> 'Appointments can only be booked up to ' . config('clinic.booking_advance_days', 60) . ' days in advance.',
            'slot_start.required'        => 'Please select a time slot.',
            'slot_start.date_format'     => 'Invalid time format.',
        ];
    }

    /**
     * Return JSON errors instead of redirecting.
     * The booking form is AJAX-only — a redirect would break it.
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Please check your details and try again.',
                'errors'  => $validator->errors()->toArray(),
            ], 422)
        );
    }

    /**
     * Sanitise inputs before validation runs.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'patient_name'  => trim($this->input('patient_name', '')),
            'patient_phone' => preg_replace('/[\s\-\(\)]/', '', $this->input('patient_phone', '')),
            'patient_email' => strtolower(trim($this->input('patient_email', ''))) ?: null,
        ]);
    }
}
