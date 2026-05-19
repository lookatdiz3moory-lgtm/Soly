@extends('layouts.admin')

@section('title', 'Appointment — ' . $appointment->reference)
@section('breadcrumb', 'Appointments')
@section('page-title', $appointment->reference)
@section('page-sub', 'Booked ' . $appointment->created_at->diffForHumans())

@section('page-actions')
    <a href="{{ route('admin.appointments.index') }}"
       class="adm-btn adm-btn--outline">← Back</a>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    {{-- ═══ MAIN DETAIL ═══════════════════════════════════════ --}}
    <div>

        {{-- Appointment Info --}}
        <div class="adm-card" style="margin-bottom:20px">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Appointment Details</h2>
                <span class="adm-badge adm-badge--{{ $appointment->status }}"
                      data-status-badge="{{ $appointment->id }}"
                      style="font-size:12.5px;padding:5px 13px">
                    {{ $appointment->status_label }}
                </span>
            </div>
            <div class="adm-card__body">
                @php
                    $details = [
                        ['Patient',   $appointment->patient_name],
                        ['Phone',     null],   // special render
                        ['Email',     $appointment->patient_email ?? '—'],
                        ['Service',   $appointment->service?->name ?? '—'],
                        ['Doctor',    $appointment->doctor ? 'Dr. ' . $appointment->doctor->name : '—'],
                        ['Date',      $appointment->appointment_date->format('l, d F Y')],
                        ['Time',      $appointment->slot_range],
                        ['Source',    Str::title($appointment->source)],
                        ['Booked at', $appointment->created_at->format('d M Y, h:i A')],
                    ];
                @endphp

                <dl style="display:grid;grid-template-columns:180px 1fr;gap:0">
                    @foreach($details as [$label, $value])
                    <dt style="padding:10px 0;border-bottom:1px solid var(--adm-card-border);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--adm-text-muted)">
                        {{ $label }}
                    </dt>
                    <dd style="padding:10px 0;border-bottom:1px solid var(--adm-card-border);font-size:14.5px;font-weight:500;color:var(--adm-navy);margin:0">
                        @if($label === 'Phone')
                            <a href="tel:{{ $appointment->patient_phone }}"
                               style="color:var(--adm-navy)">
                                {{ $appointment->patient_phone }}
                            </a>
                        @else
                            {{ $value }}
                        @endif
                    </dd>
                    @endforeach
                    @if($appointment->offer)
                    <dt style="padding:10px 0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--adm-text-muted)">Offer</dt>
                    <dd style="padding:10px 0;font-size:14.5px;color:var(--adm-navy);margin:0">
                        {{ $appointment->offer->title }}
                    </dd>
                    @endif
                </dl>

                @if($appointment->notes)
                <div style="margin-top:20px;padding:14px 16px;background:var(--adm-content-bg);border-radius:8px;border-left:3px solid var(--adm-gold)">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--adm-text-muted);margin-bottom:6px">
                        Patient Notes
                    </div>
                    <p style="font-size:14px;color:var(--adm-text-mid);margin:0;line-height:1.65">
                        {{ $appointment->notes }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Admin Notes --}}
        <div class="adm-card" style="margin-bottom:20px">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Internal Notes</h2>
                <span id="notesSaveStatus"
                      style="font-size:12px;color:var(--adm-green)"></span>
            </div>
            <div class="adm-card__body">
                <form id="adminNotesForm" data-appointment-id="{{ $appointment->id }}">
                    @csrf
                    <textarea name="admin_notes"
                              class="adm-form-control"
                              rows="4"
                              placeholder="Private staff notes (not visible to patient)…"
                              style="resize:vertical"
                    >{{ $appointment->admin_notes }}</textarea>
                    <p class="adm-form-hint">Auto-saves as you type.</p>
                </form>
            </div>
        </div>

        {{-- Payment --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Payment</h2>
            </div>
            <div class="adm-card__body">
                <dl style="display:grid;grid-template-columns:180px 1fr;gap:0">
                    @foreach([
                        ['Quoted Price', $appointment->price_quoted
                            ? number_format((float)$appointment->price_quoted, 0) . ' EGP' : '—'],
                        ['Amount Paid',  $appointment->price_paid
                            ? number_format((float)$appointment->price_paid, 0) . ' EGP' : '—'],
                        ['Method',       Str::title($appointment->payment_method ?? '—')],
                    ] as [$l, $v])
                    <dt style="padding:10px 0;border-bottom:1px solid var(--adm-card-border);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--adm-text-muted)">{{ $l }}</dt>
                    <dd style="padding:10px 0;border-bottom:1px solid var(--adm-card-border);font-size:14.5px;font-weight:500;color:var(--adm-navy);margin:0">{{ $v }}</dd>
                    @endforeach
                    <dt style="padding:10px 0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--adm-text-muted)">Payment Status</dt>
                    <dd style="padding:10px 0;margin:0">
                        @php
                            $pc = match($appointment->payment_status) {
                                'paid'    => 'adm-badge--confirmed',
                                'partial' => 'adm-badge--rescheduled',
                                default   => 'adm-badge--pending',
                            };
                        @endphp
                        <span class="adm-badge {{ $pc }}">
                            {{ Str::title($appointment->payment_status ?? 'unpaid') }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>

    </div>

    {{-- ═══ SIDEBAR ACTIONS ════════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Status actions --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Update Status</h2>
            </div>
            <div class="adm-card__body" style="display:flex;flex-direction:column;gap:8px">
                @php
                    $actions = [
                        'confirmed'   => ['✅ Confirm',      'background:rgba(34,197,94,.1);color:#15803D;border:1px solid rgba(34,197,94,.25)'],
                        'completed'   => ['🏆 Completed',    'background:rgba(139,92,246,.1);color:#5b21b6;border:1px solid rgba(139,92,246,.25)'],
                        'no_show'     => ['🚫 No Show',      'background:rgba(156,163,175,.1);color:#374151;border:1px solid rgba(156,163,175,.25)'],
                        'rescheduled' => ['🔄 Rescheduled',  'background:rgba(59,130,246,.1);color:#1e40af;border:1px solid rgba(59,130,246,.25)'],
                        'cancelled'   => ['❌ Cancel',       'background:rgba(239,68,68,.1);color:#991b1b;border:1px solid rgba(239,68,68,.25)'],
                    ];
                @endphp
                @foreach($actions as $status => [$label, $style])
                    @if($status !== $appointment->status)
                    <button class="adm-btn adm-btn--sm"
                            style="{{ $style }};justify-content:center;width:100%"
                            data-status-action="{{ $status }}"
                            data-appointment-id="{{ $appointment->id }}">
                        {{ $label }}
                    </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- WhatsApp --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">WhatsApp</h2>
            </div>
            <div class="adm-card__body" style="display:flex;flex-direction:column;gap:8px">
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                   class="adm-btn adm-btn--wa adm-btn--sm"
                   style="justify-content:center">
                    Open Chat ↗
                </a>
                <button class="adm-btn adm-btn--outline adm-btn--sm"
                        style="justify-content:center"
                        data-wa-action="confirmation"
                        data-appointment-id="{{ $appointment->id }}">
                    📩 Send Confirmation
                </button>
                <button class="adm-btn adm-btn--outline adm-btn--sm"
                        style="justify-content:center"
                        data-wa-action="reminder"
                        data-appointment-id="{{ $appointment->id }}">
                    🔔 Send Reminder
                </button>
                @if($appointment->status !== 'cancelled')
                <button class="adm-btn adm-btn--outline adm-btn--sm"
                        style="justify-content:center"
                        data-wa-action="cancellation"
                        data-appointment-id="{{ $appointment->id }}">
                    ❌ Send Cancellation
                </button>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Timeline</h2>
            </div>
            <div class="adm-card__body">
                @foreach([
                    ['📝', 'Created',   $appointment->created_at],
                    ['✅', 'Confirmed', $appointment->confirmed_at],
                    ['🏆', 'Completed', $appointment->completed_at],
                    ['❌', 'Cancelled', $appointment->cancelled_at],
                    ['🔔', 'Reminder',  $appointment->reminder_sent_at],
                ] as [$icon, $label, $ts])
                    @if($ts)
                    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--adm-card-border)">
                        <span style="font-size:16px" aria-hidden="true">{{ $icon }}</span>
                        <div>
                            <div style="font-size:12.5px;font-weight:600;color:var(--adm-navy)">{{ $label }}</div>
                            <div style="font-size:11.5px;color:var(--adm-text-muted);font-family:'DM Mono',monospace">
                                {{ $ts->format('d M Y, h:i A') }}
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
                @if($appointment->cancellation_reason)
                <div style="margin-top:10px;padding:10px;background:rgba(239,68,68,.06);border-radius:7px">
                    <div style="font-size:11px;font-weight:700;color:var(--adm-red);margin-bottom:4px">
                        Cancellation Reason
                    </div>
                    <p style="font-size:12.5px;color:var(--adm-red);margin:0">
                        {{ $appointment->cancellation_reason }}
                    </p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
