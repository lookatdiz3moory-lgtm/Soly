@extends('layouts.admin')

@php
    use Carbon\Carbon;

    $monthCarbon = Carbon::createFromFormat('Y-m-d', $month . '-01');
    $monthLabel  = $monthCarbon->format('F Y');
    $prevMonth   = $monthCarbon->copy()->subMonth()->format('Y-m');
    $nextMonth   = $monthCarbon->copy()->addMonth()->format('Y-m');
    $todayMonth  = today()->format('Y-m');

    // Group events by Y-m-d
    $byDay = [];
    foreach ($events as $evt) {
        $byDay[$evt->appointment_date->format('Y-m-d')][] = $evt;
    }

    $daysInMonth = $monthCarbon->daysInMonth;
    $startDow    = (int) $monthCarbon->copy()->startOfMonth()->format('N'); // 1=Mon … 7=Sun
    $leadBlanks  = $startDow - 1;
    $totalCells  = $leadBlanks + $daysInMonth;
    $trailBlanks = (7 - ($totalCells % 7)) % 7;

    $statusStyles = [
        'pending'     => 'background:rgba(245,158,11,.14);color:#92400e',
        'confirmed'   => 'background:rgba(34,197,94,.14);color:#15803D',
        'completed'   => 'background:rgba(139,92,246,.14);color:#5b21b6',
        'cancelled'   => 'background:rgba(239,68,68,.12);color:#991b1b',
        'no_show'     => 'background:rgba(156,163,175,.18);color:#4b5563',
        'rescheduled' => 'background:rgba(59,130,246,.14);color:#1e40af',
    ];
@endphp

@section('title', 'Calendar — ' . $monthLabel)
@section('breadcrumb', 'Appointments')
@section('page-title', 'Appointment Calendar')
@section('page-sub', $monthLabel . ' · ' . $events->count() . ' appointment' . ($events->count() === 1 ? '' : 's'))

@section('page-actions')
    <a href="{{ route('admin.appointments.calendar', ['month' => $prevMonth]) }}"
       class="adm-btn adm-btn--outline adm-btn--sm" aria-label="Previous month">‹ Prev</a>
    <a href="{{ route('admin.appointments.calendar', ['month' => $todayMonth]) }}"
       class="adm-btn adm-btn--outline adm-btn--sm">Today</a>
    <a href="{{ route('admin.appointments.calendar', ['month' => $nextMonth]) }}"
       class="adm-btn adm-btn--outline adm-btn--sm" aria-label="Next month">Next ›</a>
    <a href="{{ route('admin.appointments.index') }}" class="adm-btn adm-btn--outline adm-btn--sm">List View</a>
    <a href="{{ route('admin.appointments.create') }}" class="adm-btn adm-btn--gold adm-btn--sm">+ New</a>
@endsection

@section('content')

{{-- ═══ STATUS LEGEND ═══════════════════════════════════════════ --}}
<div class="adm-card" style="margin-bottom:16px">
    <div class="adm-card__body" style="padding:12px 18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--adm-text-muted)">
            Legend
        </span>
        @foreach(['confirmed' => 'Confirmed', 'pending' => 'Pending', 'completed' => 'Completed',
                  'cancelled' => 'Cancelled', 'no_show' => 'No Show', 'rescheduled' => 'Rescheduled'] as $key => $label)
            <span style="display:inline-flex;align-items:center;gap:6px;font-size:11.5px;color:var(--adm-text-mid)">
                <span style="display:inline-block;width:10px;height:10px;border-radius:3px;{{ $statusStyles[$key] }}"></span>
                {{ $label }}
            </span>
        @endforeach
    </div>
</div>

{{-- ═══ CALENDAR GRID (desktop / tablet) ════════════════════════ --}}
<div class="adm-card apt-cal" style="margin-bottom:20px">
    <div class="adm-card__body" style="padding:0;overflow:hidden">

        {{-- Day-of-week headers --}}
        <div class="apt-cal__dow">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
                <div>{{ $dow }}</div>
            @endforeach
        </div>

        {{-- Day cells --}}
        <div class="apt-cal__grid">
            {{-- Leading blanks --}}
            @for($i = 0; $i < $leadBlanks; $i++)
                <div class="apt-cal__cell apt-cal__cell--blank"></div>
            @endfor

            {{-- Days --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dateStr  = $monthCarbon->copy()->setDay($d)->format('Y-m-d');
                    $isToday  = $dateStr === today()->format('Y-m-d');
                    $isPast   = $dateStr < today()->format('Y-m-d');
                    $appts    = $byDay[$dateStr] ?? [];
                    $visible  = array_slice($appts, 0, 3);
                    $overflow = count($appts) - count($visible);
                @endphp
                <div class="apt-cal__cell {{ $isToday ? 'is-today' : '' }} {{ $isPast ? 'is-past' : '' }}">
                    <div class="apt-cal__date {{ $isToday ? 'is-today' : '' }}">{{ $d }}</div>

                    @foreach($visible as $appt)
                        <a href="{{ route('admin.appointments.show', $appt->id) }}"
                           class="apt-cal__chip"
                           title="{{ $appt->patient_name }} — {{ $appt->service?->name ?? 'Service' }} ({{ $appt->status_label }})"
                           style="{{ $statusStyles[$appt->status] ?? $statusStyles['pending'] }}">
                            <span class="apt-cal__chip-time">{{ substr($appt->slot_start, 0, 5) }}</span>
                            <span class="apt-cal__chip-name">{{ $appt->patient_name }}</span>
                        </a>
                    @endforeach

                    @if($overflow > 0)
                        <div class="apt-cal__more">+{{ $overflow }} more</div>
                    @endif
                </div>
            @endfor

            {{-- Trailing blanks --}}
            @for($i = 0; $i < $trailBlanks; $i++)
                <div class="apt-cal__cell apt-cal__cell--blank"></div>
            @endfor
        </div>
    </div>
</div>

{{-- ═══ EVENTS LIST ═════════════════════════════════════════════ --}}
@if($events->isEmpty())
    <div class="adm-card">
        <div class="adm-card__body" style="text-align:center;padding:56px 24px;color:var(--adm-text-muted)">
            <div style="font-size:44px;margin-bottom:14px" aria-hidden="true">📭</div>
            <p style="margin:0 0 16px;font-size:15px">No appointments scheduled for {{ $monthLabel }}.</p>
            <a href="{{ route('admin.appointments.create') }}" class="adm-btn adm-btn--gold adm-btn--sm">
                + Add Appointment
            </a>
        </div>
    </div>
@else
    <div class="adm-card">
        <div class="adm-card__header">
            <h2 class="adm-card__title">All Appointments — {{ $monthLabel }}</h2>
            <span style="font-size:12px;color:var(--adm-text-muted)">{{ $events->count() }} total</span>
        </div>
        <div class="adm-table-wrap">
            <table class="adm-table" role="table" aria-label="Appointments this month">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col">Patient</th>
                        <th scope="col">Doctor</th>
                        <th scope="col">Service</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $appt)
                        <tr data-href="{{ route('admin.appointments.show', $appt->id) }}">
                            <td class="td-primary" style="white-space:nowrap">
                                {{ $appt->appointment_date->format('d M') }}
                                <span class="td-muted">· {{ $appt->appointment_date->format('D') }}</span>
                            </td>
                            <td class="td-mono td-muted" style="white-space:nowrap">
                                {{ substr($appt->slot_start, 0, 5) }}
                            </td>
                            <td>
                                <div class="td-primary">{{ $appt->patient_name }}</div>
                                <div class="td-muted">{{ $appt->patient_phone }}</div>
                            </td>
                            <td class="td-muted">{{ $appt->doctor?->name ?? '—' }}</td>
                            <td class="td-muted">{{ $appt->service?->name ?? '—' }}</td>
                            <td>
                                <span class="adm-badge adm-badge--{{ $appt->status }}">
                                    {{ $appt->status_label }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection

@push('styles')
<style>
.apt-cal__dow {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-bottom: 1px solid var(--adm-card-border);
}
.apt-cal__dow > div {
    padding: 10px 0;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--adm-text-muted);
}
.apt-cal__dow > div + div {
    border-left: 1px solid var(--adm-card-border);
}

.apt-cal__grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}
.apt-cal__cell {
    min-height: 112px;
    padding: 6px;
    border-bottom: 1px solid var(--adm-card-border);
    background: var(--adm-card-bg);
    position: relative;
}
.apt-cal__cell + .apt-cal__cell:not(:nth-child(7n+1)) {
    border-left: 1px solid var(--adm-card-border);
}
.apt-cal__cell:nth-child(7n+1) {
    border-left: 0;
}
.apt-cal__cell--blank {
    background: var(--adm-content-bg);
    opacity: .55;
}
.apt-cal__cell.is-today { background: rgba(232,197,130,.07); }
.apt-cal__cell.is-past  { background: rgba(0,0,0,.015); }

.apt-cal__date {
    font-size: 12px;
    font-weight: 500;
    color: var(--adm-text-muted);
    margin-bottom: 4px;
}
.apt-cal__date.is-today {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    background: var(--adm-navy);
    border-radius: 50%;
    color: #fff;
    font-weight: 700;
}

.apt-cal__chip {
    display: block;
    font-size: 11px;
    line-height: 1.35;
    padding: 3px 6px;
    margin-bottom: 3px;
    border-radius: 4px;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.apt-cal__chip:hover { filter: brightness(.95); }
.apt-cal__chip-time {
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    font-weight: 600;
    margin-right: 4px;
}
.apt-cal__chip-name { font-weight: 500; }
.apt-cal__more {
    font-size: 10.5px;
    color: var(--adm-text-muted);
    font-weight: 600;
    margin-top: 2px;
    padding-left: 6px;
}

/* Mobile: collapse grid to a vertical list of dates with events only */
@media (max-width: 720px) {
    .apt-cal { display: none; }
}
</style>
@endpush
