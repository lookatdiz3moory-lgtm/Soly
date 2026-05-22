@extends('layouts.admin')

@section('title', 'Doctors')
@section('breadcrumb', 'Doctors')
@section('page-title', 'Doctors')
@section('page-sub', $doctors->count() . ' ' . Str::plural('doctor', $doctors->count()) . ' registered')

@section('page-actions')
    <a href="{{ route('admin.doctors.create') }}" class="adm-btn adm-btn--gold">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Add Doctor
    </a>
@endsection

@section('content')

<div class="adm-card">

    @if($doctors->isEmpty())
    <div style="text-align:center;padding:64px 24px;color:var(--adm-text-muted)">
        <div style="font-size:48px;margin-bottom:16px" aria-hidden="true">👨‍⚕️</div>
        <p style="font-size:15px;margin:0 0 8px;font-weight:600;color:var(--adm-navy)">No doctors yet</p>
        <p style="font-size:13px;margin:0 0 20px">Add your first doctor to start accepting appointments.</p>
        <a href="{{ route('admin.doctors.create') }}" class="adm-btn adm-btn--gold">Add First Doctor</a>
    </div>
    @else
    <div class="adm-table-wrap">
        <table class="adm-table" role="table" aria-label="Doctors list">
            <thead>
                <tr>
                    <th scope="col">Doctor</th>
                    <th scope="col">Specialty</th>
                    <th scope="col">Services</th>
                    <th scope="col">Slot</th>
                    <th scope="col">Order</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doctors as $doctor)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px">
                            <div style="width:38px;height:38px;border-radius:50%;background:rgba(201,168,76,.12);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--adm-gold);font-size:15px;flex-shrink:0">
                                {{ mb_strtoupper(mb_substr($doctor->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="td-primary">{{ $doctor->name }}</div>
                                @if($doctor->title)
                                <div class="td-muted">{{ $doctor->title }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="td-muted">{{ $doctor->specialty ?? '—' }}</td>
                    <td class="td-muted">{{ $doctor->services->pluck('name')->join(', ') ?: '—' }}</td>
                    <td class="td-muted">{{ $doctor->slot_duration ?? 30 }} min</td>
                    <td class="td-muted">{{ $doctor->sort_order }}</td>
                    <td>
                        @if($doctor->is_active)
                            <span class="adm-badge adm-badge--confirmed">Active</span>
                        @else
                            <span class="adm-badge adm-badge--cancelled">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:nowrap">
                            <a href="{{ route('admin.doctors.schedule', $doctor->id) }}"
                               class="adm-btn adm-btn--outline adm-btn--sm"
                               title="Manage schedule">🗓</a>
                            <a href="{{ route('admin.doctors.edit', $doctor->id) }}"
                               class="adm-btn adm-btn--outline adm-btn--sm">Edit</a>
                            <form method="POST"
                                  action="{{ route('admin.doctors.destroy', $doctor->id) }}"
                                  onsubmit="return confirm('Remove {{ addslashes($doctor->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

@endsection
