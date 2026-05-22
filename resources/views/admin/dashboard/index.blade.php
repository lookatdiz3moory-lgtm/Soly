@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-sub', 'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . Str::before(Auth::user()->name ?? 'Admin', ' ') . '. Here\'s today\'s overview.')

@section('page-actions')
    <a href="{{ route('admin.appointments.create') }}" class="adm-btn adm-btn--gold">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8"  y1="12" x2="16" y2="12"/>
        </svg>
        New Appointment
    </a>
@endsection

@section('content')

{{-- ═══ STATS GRID ══════════════════════════════════════════════ --}}
<div class="adm-stats-grid">

    <div class="adm-stat-card adm-stat-card--gold" data-icon="📅">
        <div class="adm-stat-card__icon adm-stat-card__icon--gold" aria-hidden="true">📅</div>
        <div class="adm-stat-card__value"
             id="stat-total"
             data-count-to="{{ $stats['total_appointments'] }}">0</div>
        <div class="adm-stat-card__label">Total Bookings</div>
    </div>

    <div class="adm-stat-card adm-stat-card--amber" data-icon="⏳">
        <div class="adm-stat-card__icon adm-stat-card__icon--amber" aria-hidden="true">⏳</div>
        <div class="adm-stat-card__value"
             id="stat-pending"
             data-count-to="{{ $stats['pending'] }}">0</div>
        <div class="adm-stat-card__label">Pending Confirmation</div>
        @if($stats['pending'] > 0)
        <div class="adm-stat-card__change adm-stat-card__change--down">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Needs attention
        </div>
        @endif
    </div>

    <div class="adm-stat-card adm-stat-card--blue" data-icon="🗓️">
        <div class="adm-stat-card__icon adm-stat-card__icon--blue" aria-hidden="true">🗓️</div>
        <div class="adm-stat-card__value"
             id="stat-today"
             data-count-to="{{ $stats['today_upcoming'] }}">0</div>
        <div class="adm-stat-card__label">Today's Appointments</div>
    </div>

    <div class="adm-stat-card adm-stat-card--green" data-icon="📊">
        <div class="adm-stat-card__icon adm-stat-card__icon--green" aria-hidden="true">📊</div>
        <div class="adm-stat-card__value"
             id="stat-month"
             data-count-to="{{ $stats['month_count'] }}">0</div>
        <div class="adm-stat-card__label">This Month</div>
    </div>

    <div class="adm-stat-card adm-stat-card--green" data-icon="✅">
        <div class="adm-stat-card__icon adm-stat-card__icon--green" aria-hidden="true">✅</div>
        <div class="adm-stat-card__value"
             id="stat-completed"
             data-count-to="{{ $stats['completed'] }}">0</div>
        <div class="adm-stat-card__label">Completed</div>
    </div>

    <div class="adm-stat-card adm-stat-card--purple" data-icon="👥">
        <div class="adm-stat-card__icon adm-stat-card__icon--purple" aria-hidden="true">👥</div>
        <div class="adm-stat-card__value"
             id="stat-patients"
             data-count-to="{{ $stats['total_patients'] }}">0</div>
        <div class="adm-stat-card__label">Registered Patients</div>
        @if($stats['new_patients_month'] > 0)
        <div class="adm-stat-card__change adm-stat-card__change--up">
            +{{ $stats['new_patients_month'] }} this month
        </div>
        @endif
    </div>

    <div class="adm-stat-card adm-stat-card--gold" data-icon="💰">
        <div class="adm-stat-card__icon adm-stat-card__icon--gold" aria-hidden="true">💰</div>
        <div class="adm-stat-card__value"
             style="font-size:1.5rem">
            {{ number_format($stats['revenue_month'], 0) }}
        </div>
        <div class="adm-stat-card__label">Month Revenue (EGP)</div>
    </div>

    <div class="adm-stat-card adm-stat-card--red" data-icon="❌">
        <div class="adm-stat-card__icon adm-stat-card__icon--red" aria-hidden="true">❌</div>
        <div class="adm-stat-card__value"
             id="stat-cancelled"
             data-count-to="{{ $stats['cancelled'] }}">0</div>
        <div class="adm-stat-card__label">Cancelled</div>
    </div>

</div>

{{-- ═══ MAIN GRID ═══════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    {{-- ── Recent Activity ────────────────────────────────── --}}
    <div class="adm-card">
        <div class="adm-card__header">
            <h2 class="adm-card__title">Recent Bookings</h2>
            <a href="{{ route('admin.appointments.index') }}"
               class="adm-btn adm-btn--outline adm-btn--sm">View All</a>
        </div>

        @if($recentActivity->isEmpty())
        <div class="adm-card__body" style="text-align:center;padding:48px 24px;color:var(--adm-text-muted)">
            <div style="font-size:40px;margin-bottom:12px" aria-hidden="true">📭</div>
            <p style="margin:0">No bookings yet. Share the booking link to get started.</p>
            <a href="{{ route('booking') }}" target="_blank" rel="noopener"
               class="adm-btn adm-btn--gold adm-btn--sm" style="margin-top:16px">
                View Booking Page ↗
            </a>
        </div>
        @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivity as $appt)
                    <tr data-href="{{ route('admin.appointments.show', $appt->id) }}">
                        <td class="td-mono td-primary">{{ $appt->reference }}</td>
                        <td>
                            <div class="td-primary">{{ $appt->patient_name }}</div>
                            <div class="td-muted">{{ $appt->patient_phone }}</div>
                        </td>
                        <td class="td-muted">{{ $appt->service?->name ?? '—' }}</td>
                        <td>
                            <div class="td-primary" style="white-space:nowrap">
                                {{ $appt->appointment_date->format('d M Y') }}
                            </div>
                            <div class="td-mono td-muted">{{ $appt->slot_start }}</div>
                        </td>
                        <td>
                            <span class="adm-badge adm-badge--{{ $appt->status }}"
                                  data-status-badge="{{ $appt->id }}">
                                {{ $appt->status_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.appointments.show', $appt->id) }}"
                               class="adm-btn adm-btn--ghost adm-btn--sm"
                               onclick="event.stopPropagation()">
                                View →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Right column ────────────────────────────────────── --}}
    <div>

        {{-- Today's Schedule --}}
        <div class="adm-card" style="margin-bottom:20px">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Today's Schedule</h2>
                <span style="font-size:12px;color:var(--adm-text-muted)">
                    {{ today()->format('l, d M') }}
                </span>
            </div>
            <div class="adm-card__body" style="padding-top:14px">
                @if($todaySchedule->isEmpty())
                    <p style="text-align:center;padding:20px 0;color:var(--adm-text-muted);font-size:13.5px;margin:0">
                        No appointments scheduled for today.
                    </p>
                @else
                    <div class="adm-schedule-list">
                        @foreach($todaySchedule as $appt)
                        <a href="{{ route('admin.appointments.show', $appt->id) }}"
                           class="adm-schedule-item"
                           style="border-left-color:{{ match($appt->status) {
                               'confirmed' => 'var(--adm-green)',
                               'pending'   => 'var(--adm-amber)',
                               default     => 'var(--adm-gold)',
                           } }}">
                            <div class="adm-schedule-time">
                                {{ date('h:i A', strtotime($appt->slot_start)) }}
                            </div>
                            <div class="adm-schedule-body">
                                <div class="adm-schedule-name">{{ $appt->patient_name }}</div>
                                <div class="adm-schedule-service">
                                    {{ $appt->service?->name ?? '' }}
                                    @if($appt->doctor)
                                        · Dr. {{ Str::before($appt->doctor->name, ' ') }}
                                    @endif
                                </div>
                            </div>
                            <span class="adm-badge adm-badge--{{ $appt->status }}"
                                  style="font-size:10.5px;padding:2px 8px">
                                {{ $appt->status_label }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                @endif
                <a href="{{ route('admin.appointments.calendar') }}"
                   class="adm-btn adm-btn--outline adm-btn--sm"
                   style="width:100%;justify-content:center;margin-top:14px">
                    Open Calendar
                </a>
            </div>
        </div>

        {{-- Weekly Bookings Chart --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">Last 7 Days</h2>
            </div>
            <div class="adm-card__body">
                <div id="weeklyChart" aria-label="Weekly booking count chart">
                    <p style="color:var(--adm-text-muted);font-size:13px">Loading chart…</p>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ═══ QUICK ACTIONS STRIP ════════════════════════════════════ --}}
<div class="adm-card" style="margin-top:20px">
    <div class="adm-card__header">
        <h2 class="adm-card__title">Quick Actions</h2>
    </div>
    <div class="adm-card__body"
         style="display:flex;flex-wrap:wrap;gap:10px">
        <a href="{{ route('admin.appointments.create') }}" class="adm-btn adm-btn--gold">
            📅 New Appointment
        </a>
        <a href="{{ route('admin.appointments.index', ['status'=>'pending']) }}"
           class="adm-btn adm-btn--outline">
            ⏳ Pending ({{ $stats['pending'] }})
        </a>
        <a href="{{ route('admin.appointments.index', ['date'=>today()->toDateString()]) }}"
           class="adm-btn adm-btn--outline">
            🗓️ Today ({{ $stats['today_upcoming'] }})
        </a>
        <a href="{{ route('booking') }}" target="_blank" rel="noopener"
           class="adm-btn adm-btn--outline">
            🔗 Booking Page ↗
        </a>
        <a href="{{ route('admin.settings.index') }}" class="adm-btn adm-btn--outline">
            ⚙️ Settings
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    window.__weeklyChart = @json($weeklyChart);
</script>
@endpush
