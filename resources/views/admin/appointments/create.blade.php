@extends('layouts.admin')

@section('title', 'New Appointment')
@section('breadcrumb', 'Appointments')
@section('page-title', 'New Appointment')
@section('page-sub', 'Create a manual booking on behalf of a patient')

@section('page-actions')
    <a href="{{ route('admin.appointments.index') }}" class="adm-btn adm-btn--outline">← Back</a>
@endsection

@section('content')

@if($errors->any())
    <div class="adm-card" style="margin-bottom:16px;border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.04)">
        <div class="adm-card__body" style="padding:14px 18px">
            <strong style="color:var(--adm-red);font-size:13px">
                Please fix the following before submitting:
            </strong>
            <ul style="margin:6px 0 0 18px;padding:0;font-size:13px;color:var(--adm-red)">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('admin.appointments.store') }}" novalidate>
@csrf

<div class="apt-create-grid">

    {{-- ═══ MAIN FORM ════════════════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Patient --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Patient Details</h2>
            </div>
            <div class="adm-card__body">
                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="patient_name">Full Name</label>
                        <input type="text" id="patient_name" name="patient_name"
                               class="adm-form-control @error('patient_name') adm-form-control--error @enderror"
                               value="{{ old('patient_name') }}"
                               placeholder="e.g. Sara Ahmed"
                               maxlength="100" required autocomplete="off">
                        @error('patient_name')<p class="adm-form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="patient_phone">Phone</label>
                        <input type="tel" id="patient_phone" name="patient_phone"
                               class="adm-form-control @error('patient_phone') adm-form-control--error @enderror"
                               value="{{ old('patient_phone') }}"
                               placeholder="01X XXXX XXXX"
                               maxlength="25" required autocomplete="off">
                        <p class="adm-form-hint">Egyptian mobile preferred (01X…).</p>
                        @error('patient_phone')<p class="adm-form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="adm-form-group" style="margin-bottom:0">
                    <label class="adm-form-label" for="patient_email">Email Address</label>
                    <input type="email" id="patient_email" name="patient_email"
                           class="adm-form-control @error('patient_email') adm-form-control--error @enderror"
                           value="{{ old('patient_email') }}"
                           placeholder="optional"
                           maxlength="150" autocomplete="off">
                    @error('patient_email')<p class="adm-form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Appointment Details --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Appointment Details</h2>
            </div>
            <div class="adm-card__body">

                @if($services->isEmpty() || $doctors->isEmpty())
                    <div style="padding:14px 16px;background:rgba(239,68,68,.06);border-radius:8px;border-left:3px solid var(--adm-red);margin-bottom:16px">
                        <strong style="color:var(--adm-red);font-size:13px">Setup incomplete</strong>
                        <p style="font-size:13px;color:var(--adm-text-mid);margin:4px 0 0;line-height:1.5">
                            @if($services->isEmpty()) No bookable services found — add one in
                                <a href="{{ route('admin.services.index') }}">Services</a>.
                            @endif
                            @if($doctors->isEmpty()) No active doctors found — add one in
                                <a href="{{ route('admin.doctors.index') }}">Doctors</a>.
                            @endif
                        </p>
                    </div>
                @endif

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="service_id">Service</label>
                        <select id="service_id" name="service_id"
                                class="adm-form-control @error('service_id') adm-form-control--error @enderror"
                                required>
                            <option value="">— Select service —</option>
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}"
                                        {{ old('service_id') == $svc->id ? 'selected' : '' }}>
                                    {{ $svc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')<p class="adm-form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="doctor_id">Doctor</label>
                        <select id="doctor_id" name="doctor_id"
                                class="adm-form-control @error('doctor_id') adm-form-control--error @enderror"
                                required>
                            <option value="">— Select doctor —</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}"
                                        data-slot-duration="{{ $doc->slot_duration ?? 30 }}"
                                        {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                    Dr. {{ $doc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')<p class="adm-form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="appointment_date">Date</label>
                        <input type="date" id="appointment_date" name="appointment_date"
                               class="adm-form-control @error('appointment_date') adm-form-control--error @enderror"
                               value="{{ old('appointment_date', today()->format('Y-m-d')) }}"
                               min="{{ today()->subDay()->format('Y-m-d') }}"
                               required>
                        @error('appointment_date')<p class="adm-form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label">Time Slot <span class="adm-form-label--req"></span></label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                            <input type="time" id="slot_start" name="slot_start"
                                   class="adm-form-control @error('slot_start') adm-form-control--error @enderror"
                                   value="{{ old('slot_start', '09:00') }}"
                                   aria-label="Start time"
                                   required>
                            <input type="time" id="slot_end" name="slot_end"
                                   class="adm-form-control @error('slot_end') adm-form-control--error @enderror"
                                   value="{{ old('slot_end', '09:30') }}"
                                   aria-label="End time"
                                   required>
                        </div>
                        <p class="adm-form-hint">End time auto-fills based on doctor's slot duration.</p>
                        @error('slot_start')<p class="adm-form-error">{{ $message }}</p>@enderror
                        @error('slot_end')<p class="adm-form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="notes">Patient Notes</label>
                    <textarea id="notes" name="notes"
                              class="adm-form-control @error('notes') adm-form-control--error @enderror"
                              rows="3" maxlength="1000"
                              placeholder="Visible to patient — special requests or allergies…"
                              style="resize:vertical">{{ old('notes') }}</textarea>
                    @error('notes')<p class="adm-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="adm-form-group" style="margin-bottom:0">
                    <label class="adm-form-label" for="admin_notes">Internal Notes</label>
                    <textarea id="admin_notes" name="admin_notes"
                              class="adm-form-control @error('admin_notes') adm-form-control--error @enderror"
                              rows="3" maxlength="2000"
                              placeholder="Private staff-only — not shown to patient…"
                              style="resize:vertical">{{ old('admin_notes') }}</textarea>
                    @error('admin_notes')<p class="adm-form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ SIDEBAR ═══════════════════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Booking Settings --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Booking Settings</h2>
            </div>
            <div class="adm-card__body" style="display:flex;flex-direction:column;gap:14px">

                <div class="adm-form-group" style="margin-bottom:0">
                    <label class="adm-form-label adm-form-label--req" for="status">Status</label>
                    <select id="status" name="status"
                            class="adm-form-control @error('status') adm-form-control--error @enderror"
                            required>
                        @php
                            $statusLabels = [
                                'pending'     => 'Pending',
                                'confirmed'   => 'Confirmed',
                                'completed'   => 'Completed',
                                'cancelled'   => 'Cancelled',
                                'no_show'     => 'No Show',
                                'rescheduled' => 'Rescheduled',
                            ];
                        @endphp
                        @foreach($statusLabels as $val => $label)
                            <option value="{{ $val }}"
                                    {{ old('status', 'confirmed') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<p class="adm-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="adm-form-group" style="margin-bottom:0">
                    <label class="adm-form-label adm-form-label--req" for="source">Source</label>
                    <select id="source" name="source"
                            class="adm-form-control @error('source') adm-form-control--error @enderror"
                            required>
                        @foreach($sources as $src)
                            <option value="{{ $src }}"
                                    {{ old('source', 'admin') === $src ? 'selected' : '' }}>
                                {{ Str::title($src) }}
                            </option>
                        @endforeach
                    </select>
                    @error('source')<p class="adm-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="adm-form-group" style="margin-bottom:0">
                    <label class="adm-form-label" for="price_quoted">Quoted Price (EGP)</label>
                    <input type="number" id="price_quoted" name="price_quoted"
                           class="adm-form-control @error('price_quoted') adm-form-control--error @enderror"
                           value="{{ old('price_quoted') }}"
                           placeholder="optional" min="0" step="1">
                    @error('price_quoted')<p class="adm-form-error">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Submit --}}
        <div class="adm-card">
            <div class="adm-card__body" style="display:flex;flex-direction:column;gap:10px">
                <button type="submit"
                        class="adm-btn adm-btn--gold"
                        style="justify-content:center;width:100%;padding:11px 16px">
                    Create Appointment
                </button>
                <a href="{{ route('admin.appointments.index') }}"
                   class="adm-btn adm-btn--outline"
                   style="justify-content:center;width:100%">
                    Cancel
                </a>
            </div>
        </div>

        {{-- Help note --}}
        <div class="adm-card" style="background:rgba(201,168,76,.04);border-color:rgba(201,168,76,.25)">
            <div class="adm-card__body" style="padding:14px 16px">
                <p style="font-size:12.5px;color:var(--adm-text-mid);line-height:1.6;margin:0">
                    <strong style="color:var(--adm-navy)">Manual booking</strong> — for phone calls,
                    walk-ins, or WhatsApp. A unique reference is generated automatically.
                </p>
            </div>
        </div>

    </div>

</div>

</form>

@endsection

@push('styles')
<style>
.apt-create-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 960px) {
    .apt-create-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const doctorSel = document.getElementById('doctor_id');
    const startIn   = document.getElementById('slot_start');
    const endIn     = document.getElementById('slot_end');
    if (!doctorSel || !startIn || !endIn) return;

    function autoFillEnd() {
        const opt   = doctorSel.options[doctorSel.selectedIndex];
        const mins  = parseInt(opt?.dataset?.slotDuration ?? '30', 10);
        const start = startIn.value;
        if (!start || !mins) return;
        const [h, m] = start.split(':').map(Number);
        const total  = h * 60 + m + mins;
        const eh = String(Math.floor(total / 60) % 24).padStart(2, '0');
        const em = String(total % 60).padStart(2, '0');
        endIn.value = `${eh}:${em}`;
    }

    startIn.addEventListener('change', autoFillEnd);
    doctorSel.addEventListener('change', autoFillEnd);
})();
</script>
@endpush
