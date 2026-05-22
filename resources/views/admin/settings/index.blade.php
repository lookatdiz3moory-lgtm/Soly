@extends('layouts.admin')

@section('title', 'Settings')
@section('breadcrumb', 'Settings')
@section('page-title', 'Clinic Settings')
@section('page-sub', 'Global configuration for the clinic website and booking system')

@section('page-actions')
    <a href="{{ route('admin.settings.profile') }}" class="adm-btn adm-btn--outline">👤 My Profile</a>
@endsection

@section('content')

<form method="POST" action="{{ route('admin.settings.update') }}" novalidate>
@csrf @method('PUT')

@php
$get = fn(string $key, string $fallback = '') => $settings->firstWhere('key', $key)?->value ?? $fallback;
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    {{-- Clinic Info --}}
    <div class="adm-card">
        <div class="adm-card__header"><span class="adm-card__title">🏥 Clinic Information</span></div>
        <div class="adm-card__body">

            <div class="adm-form-group">
                <label class="adm-form-label" for="clinic_name">Clinic Name</label>
                <input type="text" id="clinic_name" name="settings[clinic.name]"
                       class="adm-form-control" value="{{ $get('clinic.name', 'Soly Clinic') }}" maxlength="120">
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="clinic_phone">Phone Number</label>
                <input type="tel" id="clinic_phone" name="settings[clinic.phone]"
                       class="adm-form-control" value="{{ $get('clinic.phone', '+20 100 582 6642') }}" maxlength="25">
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="clinic_whatsapp">WhatsApp Number</label>
                <input type="tel" id="clinic_whatsapp" name="settings[clinic.whatsapp]"
                       class="adm-form-control" value="{{ $get('clinic.whatsapp', '201005826642') }}" maxlength="25">
                <span class="adm-form-hint">International format without +, e.g. 201005826642</span>
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="clinic_email">Email Address</label>
                <input type="email" id="clinic_email" name="settings[clinic.email]"
                       class="adm-form-control" value="{{ $get('clinic.email', 'info@solyclinic.com') }}" maxlength="150">
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="clinic_address">Address</label>
                <input type="text" id="clinic_address" name="settings[clinic.address]"
                       class="adm-form-control" value="{{ $get('clinic.address', 'Zahraa Maadi, Cairo, Egypt') }}" maxlength="200">
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="clinic_instagram">Instagram URL</label>
                <input type="url" id="clinic_instagram" name="settings[clinic.instagram]"
                       class="adm-form-control" value="{{ $get('clinic.instagram') }}" maxlength="200" placeholder="https://instagram.com/solyclinic">
            </div>

        </div>
    </div>

    {{-- Booking Settings --}}
    <div>
        <div class="adm-card" style="margin-bottom:20px">
            <div class="adm-card__header"><span class="adm-card__title">📅 Booking Settings</span></div>
            <div class="adm-card__body">

                <div class="adm-form-group">
                    <label class="adm-form-label" for="booking_advance_days">Max Advance Booking (days)</label>
                    <input type="number" id="booking_advance_days" name="settings[clinic.booking_advance_days]"
                           class="adm-form-control" value="{{ $get('clinic.booking_advance_days', '60') }}" min="1" max="365">
                    <span class="adm-form-hint">Patients can book up to this many days ahead.</span>
                </div>

            </div>
        </div>

        {{-- Raw settings table (advanced) --}}
        @if($settings->isNotEmpty())
        <div class="adm-card">
            <div class="adm-card__header">
                <span class="adm-card__title">⚙️ All Configured Keys</span>
                <span style="font-size:12px;color:var(--adm-text-muted)">{{ $settings->count() }} keys in database</span>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table" aria-label="Settings keys">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($settings->sortBy('key') as $setting)
                        <tr>
                            <td class="td-mono td-primary" style="white-space:nowrap">{{ $setting->key }}</td>
                            <td class="td-muted" style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $setting->value ?? '—' }}
                            </td>
                            <td class="td-muted">{{ isset($setting->updated_at) ? \Carbon\Carbon::parse($setting->updated_at)->diffForHumans() : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

</div>

<div style="padding-top:8px">
    <button type="submit" class="adm-btn adm-btn--gold">Save Settings</button>
</div>

</form>

@endsection
