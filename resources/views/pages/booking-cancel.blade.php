@extends('layouts.app')

@section('title', 'Cancel Appointment — ' . $appointment->reference . ' — Soly Clinic')

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
                        Cancel Appointment?
                    </h1>
                </div>

                {{-- Body --}}
                <div class="cancel-card__body">

                    <p style="font-size:14px;color:var(--text-mid);margin-bottom:var(--sp-6)">
                        You are about to cancel the following appointment.
                        This action cannot be undone.
                    </p>

                    {{-- Appointment summary --}}
                    <div class="cancel-card__details">
                        @foreach([
                            ['Reference', $appointment->reference],
                            ['Service',   $appointment->service?->name ?? '—'],
                            ['Doctor',    optional($appointment->doctor)->name ? 'Dr. ' . $appointment->doctor->name : '—'],
                            ['Date',      $appointment->appointment_date->format('l, d F Y')],
                            ['Time',      $appointment->slot_range],
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
                                Confirm your mobile number to cancel
                            </label>
                            <input
                                type="tel"
                                id="patient_phone"
                                name="patient_phone"
                                class="form-control @error('patient_phone') form-control--error @enderror"
                                placeholder="01x xxxx xxxx"
                                autocomplete="tel"
                                inputmode="numeric"
                                required
                                autofocus
                            >
                            @error('patient_phone')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            <span class="form-field-note">
                                Enter the mobile number used when booking to verify your identity.
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
                                Yes, Cancel My Appointment
                            </button>

                            <a href="{{ route('home') }}" class="btn btn--outline">
                                No, Keep My Appointment
                            </a>
                        </div>

                        <p style="margin-top:var(--sp-5);font-size:13px;color:var(--text-light);text-align:center">
                            Need to reschedule instead?
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}?text={{ urlencode('Hello! I would like to reschedule appointment ' . $appointment->reference . '.') }}"
                               target="_blank" rel="noopener noreferrer"
                               style="color:var(--whatsapp-dark)">
                                Message us on WhatsApp
                            </a>
                        </p>
                    </form>

                </div>{{-- /cancel-card__body --}}
            </div>{{-- /cancel-card --}}
        </div>{{-- /cancel-wrap --}}
    </div>
</section>

@endsection
