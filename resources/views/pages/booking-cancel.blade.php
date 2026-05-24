@extends('layouts.app')

@section('title', __('messages.booking_cancel.page_title') . ' — ' . $appointment->reference . ' — Soly Clinic')

@push('styles')
    @vite('resources/css/booking.css')
@endpush

@section('content')

<section class="booking-page" aria-labelledby="cancel-heading">
    <div class="container">
        <div class="cancel-wrap">
            <div class="cancel-card">

                {{-- Header --}}
                <div class="cancel-card__header">
                    <div class="cancel-card__icon" aria-hidden="true">⚠️</div>
                    <h1 class="cancel-card__title" id="cancel-heading">
                        {{ __('messages.booking_cancel.heading') }}
                    </h1>
                </div>

                {{-- Body --}}
                <div class="cancel-card__body">

                    <p style="font-size:14px;color:var(--text-mid);margin-bottom:var(--sp-6)">
                        {{ __('messages.booking_cancel.warning') }}
                    </p>

                    {{-- Appointment summary --}}
                    @php $drPrefix = __('messages.booking_cancel.dr_prefix'); @endphp
                    <div class="cancel-card__details">
                        @foreach([
                            [__('messages.booking_cancel.row_reference'), $appointment->reference],
                            [__('messages.booking_cancel.row_service'),   $appointment->service?->localized_name ?? $appointment->service?->name ?? '—'],
                            [__('messages.booking_cancel.row_doctor'),    optional($appointment->doctor)->name ? $drPrefix . ' ' . $appointment->doctor->name : '—'],
                            [__('messages.booking_cancel.row_date'),      $appointment->appointment_date->format('l, d F Y')],
                            [__('messages.booking_cancel.row_time'),      $appointment->slot_range],
                        ] as [$label, $value])
                        <div class="cancel-card__detail-row">
                            <span class="cancel-card__detail-label">{{ $label }}</span>
                            <span class="cancel-card__detail-value">{{ $value }}</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Verification form --}}
                    <form method="POST"
                          action="{{ route('booking.cancel', $appointment->reference) }}"
                          novalidate>
                        @csrf

                        <div class="form-group">
                            <label class="form-label form-label--required"
                                   for="patient_phone">
                                {{ __('messages.booking_cancel.phone_label') }}
                            </label>
                            <input
                                type="tel"
                                id="patient_phone"
                                name="patient_phone"
                                class="form-control @error('patient_phone') form-control--error @enderror"
                                placeholder="01x xxxx xxxx"
                                autocomplete="tel"
                                inputmode="numeric"
                                dir="ltr"
                                required
                                autofocus
                            >
                            @error('patient_phone')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            <span class="form-field-note">
                                {{ __('messages.booking_cancel.phone_note') }}
                            </span>
                        </div>

                        <div class="cancel-card__actions">
                            <button type="submit"
                                    class="btn btn--primary"
                                    style="background:var(--error);box-shadow:none;border-color:var(--error)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9"  y1="9" x2="15" y2="15"/>
                                </svg>
                                {{ __('messages.booking_cancel.confirm_btn') }}
                            </button>

                            <a href="{{ route('home') }}" class="btn btn--outline">
                                {{ __('messages.booking_cancel.keep_btn') }}
                            </a>
                        </div>

                        <p style="margin-top:var(--sp-5);font-size:13px;color:var(--text-light);text-align:center">
                            {{ __('messages.booking_cancel.reschedule_text') }}
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}?text={{ urlencode('Hello! I would like to reschedule appointment ' . $appointment->reference . '.') }}"
                               target="_blank" rel="noopener noreferrer"
                               style="color:var(--whatsapp-dark)">
                                {{ __('messages.booking_cancel.reschedule_wa') }}
                            </a>
                        </p>
                    </form>

                </div>{{-- /cancel-card__body --}}
            </div>{{-- /cancel-card --}}
        </div>{{-- /cancel-wrap --}}
    </div>
</section>

@endsection
