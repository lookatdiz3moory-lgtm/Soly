@extends('layouts.admin')

@section('title', 'Services')
@section('breadcrumb', 'Services')
@section('page-title', 'Services')
@section('page-sub', $services->count() . ' ' . Str::plural('service', $services->count()) . ' configured')

@section('page-actions')
    <a href="{{ route('admin.services.create') }}" class="adm-btn adm-btn--gold">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Add Service
    </a>
@endsection

@section('content')

<div class="adm-card">

    @if($services->isEmpty())
    <div style="text-align:center;padding:64px 24px;color:var(--adm-text-muted)">
        <div style="font-size:48px;margin-bottom:16px" aria-hidden="true">🦷</div>
        <p style="font-size:15px;margin:0 0 8px;font-weight:600;color:var(--adm-navy)">No services yet</p>
        <p style="font-size:13px;margin:0 0 20px">Add your first dental service to populate the booking form.</p>
        <a href="{{ route('admin.services.create') }}" class="adm-btn adm-btn--gold">Add First Service</a>
    </div>
    @else
    <div class="adm-table-wrap">
        <table class="adm-table" role="table" aria-label="Services list">
            <thead>
                <tr>
                    <th scope="col">Service</th>
                    <th scope="col">Category</th>
                    <th scope="col">Price (EGP)</th>
                    <th scope="col">Duration</th>
                    <th scope="col">Order</th>
                    <th scope="col">Flags</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        <div class="td-primary">{{ $service->name }}</div>
                        @if($service->name_ar)
                        <div class="td-muted" dir="rtl">{{ $service->name_ar }}</div>
                        @endif
                    </td>
                    <td class="td-muted">{{ $service->category ?? '—' }}</td>
                    <td class="td-muted">
                        @if($service->price_from)
                            From {{ number_format((float)$service->price_from) }}
                            @if($service->price_to)
                             – {{ number_format((float)$service->price_to) }}
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td class="td-muted">
                        {{ $service->duration_minutes ? $service->duration_minutes . ' min' : '—' }}
                    </td>
                    <td class="td-muted">{{ $service->sort_order }}</td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap">
                            @if($service->is_featured)
                                <span class="adm-badge adm-badge--pending" title="Featured">⭐ Featured</span>
                            @endif
                            @if($service->is_bookable)
                                <span class="adm-badge adm-badge--confirmed" title="Bookable">📅 Bookable</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($service->is_active)
                            <span class="adm-badge adm-badge--confirmed">Active</span>
                        @else
                            <span class="adm-badge adm-badge--cancelled">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.services.edit', $service->id) }}"
                               class="adm-btn adm-btn--outline adm-btn--sm">Edit</a>
                            <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}"
                                  onsubmit="return confirm('Delete service \'{{ addslashes($service->name) }}\'?')">
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
