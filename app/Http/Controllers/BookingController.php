<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Offer;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * BookingController
 *
 * Handles the patient-facing booking flow:
 *   GET  /book            → show booking form
 *   GET  /booking/confirm — show success screen after AJAX submission
 *   GET  /booking/cancel  — show cancel confirmation form
 *   POST /booking/cancel  — patient cancels their own appointment
 */
class BookingController extends Controller
{
    /* ── Booking Form ───────────────────────────────────────── */

    /**
     * Show the appointment booking page.
     *
     * Pre-selects service / offer from query string when the patient
     * arrives via a "Book Now" button on a service or offer card.
     */
    public function index(Request $request): View
    {
        // All bookable services for the form dropdown
        $services = Service::bookable()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'name_ar', 'slug', 'price_from', 'duration_minutes', 'icon']);

        // Pre-selected service from ?service=slug or ?service=id
        $preService = null;
        if ($request->filled('service')) {
            $raw = $request->query('service');
            $preService = is_numeric($raw)
                ? $services->firstWhere('id', (int)$raw)
                : $services->firstWhere('slug', $raw);
        }

        // Pre-selected offer
        $preOffer = null;
        if ($request->filled('offer')) {
            $preOffer = Offer::active()
                ->with('service:id,name,slug')
                ->find((int)$request->query('offer'));

            // Also pre-select the linked service if one exists
            if ($preOffer && $preOffer->service && !$preService) {
                $preService = $services->firstWhere('id', $preOffer->service_id);
            }
        }

        // Pre-selected doctor
        $preDoctor = null;
        if ($request->filled('doctor')) {
            $preDoctor = Doctor::active()
                ->find((int)$request->query('doctor'), ['id', 'name', 'specialty', 'photo', 'slot_duration']);
        }

        return view('pages.booking', [
            'services'   => $services,
            'preService' => $preService,
            'preOffer'   => $preOffer,
            'preDoctor'  => $preDoctor,
        ]);
    }

    /* ── Booking Success ────────────────────────────────────── */

    /**
     * Show the booking confirmation screen.
     * Reached after a successful AJAX form submission.
     */
    public function confirm(string $reference): View
    {
        $appointment = Appointment::with(['doctor:id,name,name_ar,title', 'service:id,name,name_ar'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('pages.booking-confirm', compact('appointment'));
    }

    /* ── Patient Cancellation ───────────────────────────────── */

    /**
     * Show the "are you sure?" cancellation form.
     */
    public function cancelForm(string $reference): View|RedirectResponse
    {
        $appointment = Appointment::where('reference', $reference)->firstOrFail();

        // Can only cancel pending or confirmed appointments
        if (!in_array($appointment->status, [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_CONFIRMED,
        ], true)) {
            return redirect()->route('home')
                ->with('info', 'This appointment cannot be cancelled — its current status is "'
                    . $appointment->status_label . '".');
        }

        return view('pages.booking-cancel', compact('appointment'));
    }

    /**
     * Handle the patient's cancellation POST.
     */
    public function cancel(Request $request, string $reference): RedirectResponse
    {
        $request->validate([
            'patient_phone' => ['required', 'string'],
        ]);

        $appointment = Appointment::where('reference', $reference)->firstOrFail();

        // Security: patient must provide their own phone to confirm identity
        $submittedPhone = preg_replace('/[\s\-]/', '', $request->input('patient_phone'));
        $storedPhone    = preg_replace('/[\s\-]/', '', $appointment->patient_phone);

        if (!str_ends_with($storedPhone, substr($submittedPhone, -8))) {
            return back()->withErrors(['patient_phone' => 'Phone number does not match our records.']);
        }

        if (!in_array($appointment->status, [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_CONFIRMED,
        ], true)) {
            return redirect()->route('home')
                ->with('info', 'This appointment is already ' . $appointment->status_label . '.');
        }

        $appointment->update([
            'status'              => Appointment::STATUS_CANCELLED,
            'cancellation_reason' => 'Cancelled by patient via website.',
            'cancelled_at'        => now(),
        ]);

        return redirect()->route('home')
            ->with('success', 'Your appointment (' . $reference . ') has been cancelled. We hope to see you again soon.');
    }
}
