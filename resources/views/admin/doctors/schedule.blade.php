@extends('layouts.admin')

@section('title', 'Schedule — ' . $doctor->name)
@section('breadcrumb', 'Schedule')
@section('page-title', $doctor->name . ' — Weekly Schedule')
@section('page-sub', 'Define working hours per day. Slots are generated automatically.')

@section('page-actions')
    <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="adm-btn adm-btn--outline">← Edit Profile</a>
    <a href="{{ route('admin.doctors.index') }}" class="adm-btn adm-btn--outline">All Doctors</a>
@endsection

@section('content')

@php
$days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
$labels = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
$schedule = $doctor->schedule ?? [];

$defaults = [
    'monday'    => ['open'=>'09:00','close'=>'21:00','active'=>true],
    'tuesday'   => ['open'=>'09:00','close'=>'21:00','active'=>true],
    'wednesday' => ['open'=>'09:00','close'=>'21:00','active'=>true],
    'thursday'  => ['open'=>'09:00','close'=>'21:00','active'=>true],
    'friday'    => ['open'=>'09:00','close'=>'21:00','active'=>false],
    'saturday'  => ['open'=>'10:00','close'=>'18:00','active'=>true],
    'sunday'    => ['open'=>'09:00','close'=>'21:00','active'=>true],
];
@endphp

<form method="POST" action="{{ route('admin.doctors.schedule.update', $doctor->id) }}" id="scheduleForm">
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <div class="adm-card">
        <div class="adm-card__header">
            <span class="adm-card__title">Weekly Availability</span>
            <span style="font-size:12px;color:var(--adm-text-muted)">Slot duration: {{ $doctor->slot_duration ?? 30 }} min</span>
        </div>
        <div class="adm-card__body" style="padding:0">

            @foreach($days as $i => $day)
            @php
                $cfg = $schedule[$day] ?? $defaults[$day];
                $active = !empty($cfg['active']);
                $open  = $cfg['open']  ?? '09:00';
                $close = $cfg['close'] ?? '21:00';
            @endphp
            <div class="schedule-day-row" id="row-{{ $day }}"
                 style="display:flex;align-items:center;gap:16px;padding:14px 22px;border-bottom:1px solid var(--adm-card-border);{{ !$active ? 'opacity:.45' : '' }}">

                <label style="display:flex;align-items:center;gap:9px;min-width:130px;cursor:pointer">
                    <input type="checkbox" name="{{ $day }}_active" value="1"
                           {{ $active ? 'checked' : '' }}
                           onchange="toggleDay('{{ $day }}', this.checked)"
                           style="accent-color:var(--adm-gold)">
                    <span style="font-size:14px;font-weight:600;color:var(--adm-navy)">{{ $labels[$i] }}</span>
                </label>

                <div style="display:flex;align-items:center;gap:10px;flex:1">
                    <div>
                        <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--adm-text-muted);display:block;margin-bottom:4px">Opens</label>
                        <input type="time" name="{{ $day }}_open" value="{{ $open }}"
                               id="{{ $day }}_open"
                               class="adm-form-control" style="width:120px;padding:7px 10px">
                    </div>
                    <span style="color:var(--adm-text-muted);font-size:13px;margin-top:18px">→</span>
                    <div>
                        <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--adm-text-muted);display:block;margin-bottom:4px">Closes</label>
                        <input type="time" name="{{ $day }}_close" value="{{ $close }}"
                               id="{{ $day }}_close"
                               class="adm-form-control" style="width:120px;padding:7px 10px">
                    </div>
                </div>

                <div style="min-width:80px;text-align:right">
                    @if($active)
                    <span class="adm-badge adm-badge--confirmed" id="badge-{{ $day }}">Working</span>
                    @else
                    <span class="adm-badge adm-badge--cancelled" id="badge-{{ $day }}">Off</span>
                    @endif
                </div>

            </div>
            @endforeach

        </div>
    </div>

    <div>
        <div class="adm-card" style="margin-bottom:16px">
            <div class="adm-card__body">
                <p style="font-size:13px;color:var(--adm-text-mid);margin:0 0 12px">
                    Slots are generated automatically from the working hours based on the
                    <strong>{{ $doctor->slot_duration ?? 30 }}-minute</strong> duration.
                </p>
                <p style="font-size:13px;color:var(--adm-text-mid);margin:0">
                    Booked appointments and manual blocks are excluded in real time.
                </p>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px">
            <button type="submit" class="adm-btn adm-btn--gold" style="justify-content:center">
                Save Schedule
            </button>
            <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="adm-btn adm-btn--outline" style="justify-content:center">
                Cancel
            </a>
        </div>
    </div>

</div>

</form>

@push('scripts')
<script>
function toggleDay(day, active) {
    const row   = document.getElementById('row-'   + day);
    const badge = document.getElementById('badge-' + day);
    row.style.opacity = active ? '1' : '.45';
    if (badge) {
        badge.className = 'adm-badge ' + (active ? 'adm-badge--confirmed' : 'adm-badge--cancelled');
        badge.textContent = active ? 'Working' : 'Off';
    }
}
</script>
@endpush

@endsection
