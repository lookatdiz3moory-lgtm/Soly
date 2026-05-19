<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * AppointmentManagementController
 *
 * All admin appointment operations:
 *   index     — filterable, paginated list
 *   show      — single appointment detail
 *   create    — manual booking form
 *   store     — persist manual booking
 *   updateStatus — AJAX status change
 *   updateNotes  — AJAX notes auto-save
 *   openWhatsApp — generate wa.me link
 *   calendar  — calendar view (data only; view rendered by JS)
 */
class AppointmentManagementController extends Controller
{
    /* ── List ───────────────────────────────────────────────── */

    public function index(Request $request): View
    {
        $query = Appointment::with(['doctor:id,name', 'service:id,name'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('slot_start');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', (int)$request->input('doctor_id'));
        }

        if ($request->filled('date')) {
            $query->where('appointment_date', $request->input('date'));
        }

        if ($request->filled('search')) {
            $term = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('patient_name',  'like', $term)
                  ->orWhere('patient_phone','like', $term)
                  ->orWhere('reference',    'like', $term);
            });
        }

        $appointments = $query->paginate(20)->withQueryString();
        $doctors      = Doctor::active()->get(['id', 'name']);

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'doctors'      => $doctors,
            'statuses'     => Appointment::VALID_STATUSES,
            'filters'      => $request->only(['status', 'doctor_id', 'date', 'search']),
        ]);
    }

    /* ── Show ───────────────────────────────────────────────── */

    public function show(int $id): View
    {
        $appointment = Appointment::with([
            'doctor:id,name,phone,title',
            'service:id,name,price_from',
            'offer:id,title,offer_price',
            'user:id,name,email',
        ])->findOrFail($id);

        return view('admin.appointments.show', [
            'appointment' => $appointment,
            'statuses'    => Appointment::VALID_STATUSES,
            'waLink'      => $this->buildWhatsAppLink($appointment->patient_phone),
        ]);
    }

    /* ── Manual Create ──────────────────────────────────────── */

    public function create(): View
    {
        return view('admin.appointments.create', [
            'services' => Service::bookable()->get(['id', 'name']),
            'doctors'  => Doctor::active()->get(['id', 'name', 'slot_duration']),
            'sources'  => ['phone', 'whatsapp', 'walkin', 'admin'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_name'     => ['required', 'string', 'max:100'],
            'patient_phone'    => ['required', 'string', 'max:25'],
            'patient_email'    => ['nullable', 'email', 'max:150'],
            'service_id'       => ['required', 'exists:services,id'],
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date'],
            'slot_start'       => ['required', 'date_format:H:i'],
            'slot_end'         => ['required', 'date_format:H:i'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'admin_notes'      => ['nullable', 'string', 'max:2000'],
            'price_quoted'     => ['nullable', 'numeric', 'min:0'],
            'source'           => ['required', 'in:phone,whatsapp,walkin,admin,website'],
            'status'           => ['required', 'in:' . implode(',', Appointment::VALID_STATUSES)],
        ]);

        $appointment = Appointment::create(array_merge($data, [
            'reference'    => Appointment::generateReference(),
            'confirmed_by' => Auth::id(),
            'confirmed_at' => in_array($data['status'], ['confirmed'])
                ? now() : null,
        ]));

        return redirect()->route('admin.appointments.show', $appointment->id)
            ->with('success', "Appointment {$appointment->reference} created successfully.");
    }

    /* ── Status Update (AJAX) ───────────────────────────────── */

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Appointment::VALID_STATUSES)],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment = Appointment::findOrFail($id);
        $newStatus   = $data['status'];

        $updates = ['status' => $newStatus];

        match ($newStatus) {
            Appointment::STATUS_CONFIRMED   => $updates['confirmed_at']  = now(),
            Appointment::STATUS_CANCELLED   => $updates['cancelled_at']  = now(),
            Appointment::STATUS_COMPLETED   => $updates['completed_at']  = now(),
            default                         => null,
        };

        if ($newStatus === Appointment::STATUS_CONFIRMED) {
            $updates['confirmed_by'] = Auth::id();
        }

        if ($newStatus === Appointment::STATUS_CANCELLED && !empty($data['reason'])) {
            $updates['cancellation_reason'] = $data['reason'];
            $updates['cancelled_by']        = Auth::id();
        }

        $appointment->update($updates);

        return response()->json([
            'success'      => true,
            'new_status'   => $newStatus,
            'status_label' => $appointment->fresh()->status_label,
        ]);
    }

    /* ── Notes Auto-save (AJAX) ─────────────────────────────── */

    public function updateNotes(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Appointment::findOrFail($id)->update($data);

        return response()->json(['success' => true]);
    }

    /* ── WhatsApp Quick Action ──────────────────────────────── */

    public function openWhatsApp(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::with(['doctor:id,name', 'service:id,name'])
            ->findOrFail($id);

        $template = $request->input('template', 'confirmation');

        $message = match ($template) {
            'confirmation' => $this->confirmationMessage($appointment),
            'reminder'     => $this->reminderMessage($appointment),
            'cancellation' => $this->cancellationMessage($appointment),
            default        => '',
        };

        $url = $this->buildWhatsAppLink($appointment->patient_phone, $message);

        return response()->json(['success' => true, 'url' => $url]);
    }

    /* ── Calendar View ──────────────────────────────────────── */

    public function calendar(Request $request): View
    {
        $month = $request->input('month', today()->format('Y-m'));
        [$year, $mon] = explode('-', $month . '-01');

        $start = "{$year}-{$mon}-01";
        $end   = date('Y-m-t', strtotime($start));

        $events = Appointment::with(['doctor:id,name', 'service:id,name'])
            ->whereBetween('appointment_date', [$start, $end])
            ->orderBy('appointment_date')
            ->orderBy('slot_start')
            ->get();

        return view('admin.appointments.calendar', [
            'month'   => $month,
            'events'  => $events,
            'doctors' => Doctor::active()->get(['id', 'name']),
        ]);
    }

    /* ── Private message builders ───────────────────────────── */

    private function buildWhatsAppLink(string $phone, string $message = ''): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '01') && strlen($clean) === 11) {
            $clean = '2' . $clean;
        }
        $url = "https://wa.me/{$clean}";
        return $message ? $url . '?text=' . rawurlencode($message) : $url;
    }

    private function confirmationMessage(Appointment $a): string
    {
        return "✅ *Appointment Confirmed — Soly Clinic*\n\n"
             . "Dear {$a->patient_name},\n\n"
             . "Your appointment has been confirmed.\n\n"
             . "📋 *Details:*\n"
             . "• Service: {$a->service?->name}\n"
             . "• Doctor: Dr. {$a->doctor?->name}\n"
             . "• Date: " . $a->appointment_date->format('l, d F Y') . "\n"
             . "• Time: {$a->slot_range}\n"
             . "• Ref: #{$a->reference}\n\n"
             . "We look forward to seeing you! 😊\n"
             . config('clinic.name', 'Soly Clinic');
    }

    private function reminderMessage(Appointment $a): string
    {
        return "🔔 *Appointment Reminder — Soly Clinic*\n\n"
             . "Dear {$a->patient_name},\n\n"
             . "Reminder: your appointment is *tomorrow*.\n\n"
             . "• Date: " . $a->appointment_date->format('l, d F Y') . "\n"
             . "• Time: {$a->slot_range}\n"
             . "• Ref: #{$a->reference}\n\n"
             . "If you need to cancel, please let us know ASAP.\n"
             . config('clinic.name', 'Soly Clinic');
    }

    private function cancellationMessage(Appointment $a): string
    {
        return "❌ *Appointment Cancelled — Soly Clinic*\n\n"
             . "Dear {$a->patient_name},\n\n"
             . "Your appointment #{$a->reference} has been cancelled.\n\n"
             . "To reschedule: " . config('clinic.phone', '') . "\n"
             . config('clinic.name', 'Soly Clinic');
    }
}
