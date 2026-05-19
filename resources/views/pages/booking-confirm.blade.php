@extends('layouts.app')

@section('title', 'Booking Confirmed — ' . $appointment->reference . ' — Soly Clinic')
@section('meta_description', 'Your appointment at Soly Clinic has been received. Reference: ' . $appointment->reference)

@push('styles')
    @vite('resources/css/booking.css')
@endpush

@section('content')

<section class="booking-page" aria-labelledby="confirm-heading">
    <div class="container">
        <div class="confirm-wrap">
            <div class="confirm-card">

                {{-- ── Top: success icon + heading ──────────── --}}
                <div class="confirm-card__top">
                    <span class="confirm-card__icon" aria-hidden="true">
                        {{ $appointment->status === 'confirmed' ? '✅' : '📩' }}
                    </span>
                    <h1 class="confirm-card__heading" id="confirm-heading">
                        {{ $appointment->status === 'confirmed'
                            ? 'Appointment Confirmed!'
                            : 'Booking Request Received!' }}
                    </h1>
                    <p class="confirm-card__sub">
                        Thank you, <strong style="color:var(--white)">
                            {{ $appointment->patient_name }}
                        </strong>.
                        {{ $appointment->status === 'confirmed'
                            ? 'Your appointment is confirmed. See you soon!'
                            : 'We will confirm your slot within the hour via WhatsApp.' }}
                    </p>
                </div>

                {{-- ── Body ──────────────────────────────────── --}}
                <div class="confirm-card__body">

                    {{-- Reference badge --}}
                    <div style="text-align:center;margin-bottom:var(--sp-6)">
                        <div class="confirm-ref-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                            </svg>
                            Reference: {{ $appointment->reference }}
                        </div>
                    </div>

                    {{-- Appointment details --}}
                    <div class="confirm-details">
                        @php
                            $rows = [
                                ['Service',  $appointment->service?->name ?? '—'],
                                ['Doctor',   optional($appointment->doctor)->name ? 'Dr. ' . $appointment->doctor->name : '—'],
                                ['Date',     $appointment->appointment_date->format('l, d F Y')],
                                ['Time',     $appointment->slot_range],
                                ['Status',   null],   // rendered separately
                            ];
                        @endphp

                        @foreach($rows as [$label, $value])
                        <div class="confirm-row">
                            <span class="confirm-row__label">{{ $label }}</span>
                            @if($label === 'Status')
                                <span>
                                    <span class="confirm-status confirm-status--{{ $appointment->status }}">
                                        {{ $appointment->status_label }}
                                    </span>
                                </span>
                            @else
                                <span class="confirm-row__value">{{ $value }}</span>
                            @endif
                        </div>
                        @endforeach

                        @if($appointment->patient_phone)
                        <div class="confirm-row">
                            <span class="confirm-row__label">Phone</span>
                            <span class="confirm-row__value">{{ $appointment->patient_phone }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- What happens next --}}
                    <div class="confirm-next">
                        <div class="confirm-next__title">What happens next?</div>
                        <ol>
                            @if($appointment->status === 'pending')
                            <li>Our team will review your booking and confirm within 1 hour</li>
                            <li>You will receive a WhatsApp message with your confirmation</li>
                            @else
                            <li>Your appointment is confirmed — add it to your calendar</li>
                            @endif
                            <li>
                                We will send a reminder 24 hours before your visit to
                                <strong>{{ $appointment->patient_phone }}</strong>
                            </li>
                            <li>
                                To cancel or reschedule, please contact us at least 24 hours
                                before your appointment
                            </li>
                        </ol>
                    </div>

                    {{-- Action buttons --}}
                    <div class="confirm-actions">
                        {{-- WhatsApp confirmation link --}}
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}?text={{ urlencode('Hello! I just booked appointment ' . $appointment->reference . ' and wanted to confirm.') }}"
                           target="_blank" rel="noopener noreferrer"
                           class="btn btn--wa">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"
                                 aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.83 5.83 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Message Us on WhatsApp
                        </a>

                        <a href="{{ route('home') }}" class="btn btn--outline">
                            Back to Home
                        </a>
                    </div>

                    {{-- Cancel link --}}
                    @if(in_array($appointment->status, ['pending','confirmed']))
                    <p style="text-align:center;margin-top:var(--sp-6);font-size:13px;color:var(--text-light)">
                        Need to cancel?
                        <a href="{{ route('booking.cancel.form', $appointment->reference) }}"
                           style="color:var(--error);text-decoration:underline">
                            Cancel this appointment
                        </a>
                    </p>
                    @endif

                </div>{{-- /confirm-card__body --}}
            </div>{{-- /confirm-card --}}
        </div>{{-- /confirm-wrap --}}
    </div>
</section>

@endsection
