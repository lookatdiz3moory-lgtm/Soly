@extends('layouts.admin')

@section('title', 'Appointments')
@section('breadcrumb', 'Appointments')
@section('page-title', 'Appointments')
@section('page-sub', number_format($appointments->total()) . ' total appointments')

@section('page-actions')
    <a href="{{ route('admin.appointments.create') }}" class="adm-btn adm-btn--gold">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8"  y1="12" x2="16" y2="12"/>
        </svg>
        New Appointment
    </a>
    <a href="{{ route('admin.appointments.calendar') }}"
       class="adm-btn adm-btn--outline">
        🗓️ Calendar
    </a>
@endsection

@section('content')

<div class="adm-card">

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.appointments.index') }}"
          class="adm-filter-bar" role="search" aria-label="Filter appointments">

        <div class="adm-filter-group">
            <label class="adm-filter-label" for="filter-status">Status</label>
            <select id="filter-status" name="status" class="adm-filter-control"
                    onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}"
                            {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>
                        {{ Str::title(str_replace('_', ' ', $s)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="adm-filter-group">
            <label class="adm-filter-label" for="filter-doctor">Doctor</label>
            <select id="filter-doctor" name="doctor_id" class="adm-filter-control"
                    onchange="this.form.submit()">
                <option value="">All Doctors</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}"
                            {{ ($filters['doctor_id'] ?? '') == $doc->id ? 'selected' : '' }}>
                        {{ $doc->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="adm-filter-group">
            <label class="adm-filter-label" for="filter-date">Date</label>
            <input type="date" id="filter-date" name="date"
                   class="adm-filter-control"
                   value="{{ $filters['date'] ?? '' }}"
                   onchange="this.form.submit()">
        </div>

        <div class="adm-filter-group adm-filter-search">
            <label class="adm-filter-label" for="filter-search">Search</label>
            <div style="display:flex;gap:8px">
                <input type="text" id="filter-search" name="search"
                       class="adm-filter-control"
                       style="flex:1"
                       placeholder="Name, phone, or reference…"
                       value="{{ $filters['search'] ?? '' }}">
                <button type="submit" class="adm-btn adm-btn--primary adm-btn--sm">Search</button>
                @if(array_filter($filters))
                    <a href="{{ route('admin.appointments.index') }}"
                       class="adm-btn adm-btn--outline adm-btn--sm">Reset</a>
                @endif
            </div>
        </div>

    </form>

    {{-- Table --}}
    @if($appointments->isEmpty())
    <div style="text-align:center;padding:56px 24px;color:var(--adm-text-muted)">
        <div style="font-size:44px;margin-bottom:14px" aria-hidden="true">📭</div>
        <p style="margin:0 0 16px;font-size:15px">No appointments match your filters.</p>
        <a href="{{ route('admin.appointments.index') }}" class="adm-btn adm-btn--outline adm-btn--sm">
            Clear Filters
        </a>
    </div>
    @else
    <div class="adm-table-wrap">
        <table class="adm-table" role="table" aria-label="Appointments list">
            <thead>
                <tr>
                    <th scope="col">Reference</th>
                    <th scope="col">Patient</th>
                    <th scope="col">Service</th>
                    <th scope="col">Doctor</th>
                    <th scope="col">Date &amp; Time</th>
                    <th scope="col">Source</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                <tr data-href="{{ route('admin.appointments.show', $appt->id) }}">
                    <td class="td-mono td-primary">{{ $appt->reference }}</td>
                    <td>
                        <div class="td-primary">{{ $appt->patient_name }}</div>
                        <div class="td-muted">{{ $appt->patient_phone }}</div>
                    </td>
                    <td class="td-muted">{{ $appt->service?->name ?? '—' }}</td>
                    <td class="td-muted">{{ $appt->doctor?->name ?? '—' }}</td>
                    <td>
                        <div class="td-primary" style="white-space:nowrap">
                            {{ $appt->appointment_date->format('d M Y') }}
                        </div>
                        <div class="td-mono td-muted">
                            {{ date('h:i A', strtotime($appt->slot_start)) }}
                        </div>
                    </td>
                    <td>
                        <span style="font-size:12px;text-transform:capitalize;color:var(--adm-text-muted)">
                            {{ $appt->source }}
                        </span>
                    </td>
                    <td>
                        <span class="adm-badge adm-badge--{{ $appt->status }}"
                              data-status-badge="{{ $appt->id }}">
                            {{ $appt->status_label }}
                        </span>
                    </td>
                    <td onclick="event.stopPropagation()"
                        style="white-space:nowrap">
                        <a href="{{ route('admin.appointments.show', $appt->id) }}"
                           class="adm-btn adm-btn--ghost adm-btn--sm adm-btn--icon"
                           title="View details" aria-label="View {{ $appt->reference }}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                        @if(in_array($appt->status, ['pending', 'confirmed']))
                        <button class="adm-btn adm-btn--ghost adm-btn--sm adm-btn--icon"
                                title="WhatsApp {{ $appt->patient_name }}"
                                data-wa-action="confirmation"
                                data-appointment-id="{{ $appt->id }}"
                                style="color:#25D366"
                                aria-label="WhatsApp {{ $appt->patient_name }}">
                            <svg width="15" height="15" viewBox="0 0 24 24"
                                 fill="currentColor" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.83 5.83 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </button>
                        @endif
                        @if($appt->status === 'pending')
                        <button class="adm-btn adm-btn--sm"
                                style="background:rgba(34,197,94,.1);color:#15803D;border:1px solid rgba(34,197,94,.25)"
                                data-status-action="confirmed"
                                data-appointment-id="{{ $appt->id }}"
                                title="Confirm appointment"
                                aria-label="Confirm appointment {{ $appt->reference }}">
                            ✓ Confirm
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($appointments->hasPages())
    <div class="adm-card__footer">
        <div class="adm-pagination" role="navigation" aria-label="Pagination">
            {{-- Prev --}}
            @if($appointments->onFirstPage())
                <button class="adm-page-btn" disabled aria-disabled="true">‹</button>
            @else
                <a href="{{ $appointments->previousPageUrl() }}" class="adm-page-btn" aria-label="Previous page">‹</a>
            @endif

            {{-- Page numbers --}}
            @foreach($appointments->getUrlRange(
                max(1, $appointments->currentPage() - 2),
                min($appointments->lastPage(), $appointments->currentPage() + 2)
            ) as $page => $url)
                <a href="{{ $url }}"
                   class="adm-page-btn {{ $page == $appointments->currentPage() ? 'is-active' : '' }}"
                   aria-label="Page {{ $page }}"
                   aria-current="{{ $page == $appointments->currentPage() ? 'page' : 'false' }}">
                    {{ $page }}
                </a>
            @endforeach

            {{-- Next --}}
            @if($appointments->hasMorePages())
                <a href="{{ $appointments->nextPageUrl() }}" class="adm-page-btn" aria-label="Next page">›</a>
            @else
                <button class="adm-page-btn" disabled aria-disabled="true">›</button>
            @endif
        </div>
        <p style="text-align:center;font-size:12px;color:var(--adm-text-muted);margin-top:4px">
            Showing {{ $appointments->firstItem() }}–{{ $appointments->lastItem() }}
            of {{ number_format($appointments->total()) }} appointments
        </p>
    </div>
    @endif
    @endif

</div>

@endsection
