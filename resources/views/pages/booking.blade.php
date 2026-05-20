@extends('layouts.app')

@section('title', 'Book an Appointment — Soly Clinic')
@section('meta_description', 'Book your dental appointment at Soly Clinic in Zahraa Maadi, Cairo. Choose your service, doctor, date, and time online in under 2 minutes.')

@push('styles')
    @vite('resources/css/booking.css')
@endpush

@section('content')

{{-- ── Page Hero ─────────────────────────────────────────────── --}}
<div class="page-hero" aria-label="Book an appointment">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">
            Online Booking
        </div>
        <h1 class="page-hero__title">Book Your Appointment</h1>
        <p class="page-hero__sub">
            Choose your service, pick a time, and we confirm within the hour.
            No waiting, no paper forms.
        </p>
    </div>
</div>

{{-- ── Main Content ─────────────────────────────────────────── --}}
<section class="booking-page" aria-label="Appointment booking form">
    <div class="container">
        <div class="booking-layout">

            {{-- ══ FORM CARD ════════════════════════════════════ --}}
            <div class="booking-card">
                <div class="booking-card__header">
                    <h2 class="booking-card__title">New Appointment</h2>
                    <p class="booking-card__sub">Complete all 4 steps below</p>
                </div>

                <div class="booking-card__body">

                    {{-- Progress Steps --}}
                    <div class="booking-steps" role="list" aria-label="Booking progress">
                        @foreach(['Service & Doctor', 'Date & Time', 'Your Details', 'Confirm'] as $i => $label)
                        <div class="booking-step {{ $i === 0 ? 'is-active' : '' }}"
                             role="listitem"
                             aria-current="{{ $i === 0 ? 'step' : 'false' }}">
                            <div class="booking-step__circle">
                                {{ $i + 1 }}
                            </div>
                            <div class="booking-step__label">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>

                    {{-- ── FORM ─────────────────────────────── --}}
                    <form
                        id="bookingForm"
                        novalidate
                        data-pre-service-id="{{ $preService?->id ?? '' }}"
                        data-pre-doctor-id="{{ $preDoctor?->id ?? '' }}"
                    >
                        @csrf

                        {{-- ── PANEL 1: Service + Doctor ─────── --}}
                        <div class="booking-panel is-active" id="panel1">
                            <h3 class="booking-panel__title">1. Choose a Service &amp; Doctor</h3>

                            {{-- Service select --}}
                            <div class="form-group">
                                <label class="form-label form-label--required"
                                       for="service_id">
                                    Dental Service
                                </label>
                                <select
                                    id="service_id"
                                    name="service_id"
                                    class="form-control form-control-lg"
                                    required
                                    aria-required="true"
                                    aria-describedby="service-hint"
                                >
                                    <option value="">— Select a service —</option>
                                    @foreach($services as $svc)
                                    <option
                                        value="{{ $svc->id }}"
                                        data-slug="{{ $svc->slug }}"
                                        data-price="{{ $svc->price_from ? number_format((float)$svc->price_from) . ' EGP' : '' }}"
                                        {{ optional($preService)->id == $svc->id ? 'selected' : '' }}
                                    >
                                        {{ $svc->name }}
                                        @if($svc->price_from)
                                            — From {{ number_format((float)$svc->price_from) }} EGP
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <span id="service-hint" class="form-field-note">
                                    Can't find your service?
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
                                       target="_blank" rel="noopener" style="color:var(--gold-dark)">
                                        Ask us on WhatsApp
                                    </a>
                                </span>
                            </div>

                            {{-- Doctor selector (loaded by JS) --}}
                            <div id="doctorSection" class="form-group" style="display:none">
                                <label class="form-label form-label--required">
                                    Choose Doctor
                                </label>
                                <div id="doctorGrid" class="doctor-grid" role="radiogroup"
                                     aria-label="Available doctors">
                                    {{-- Populated by booking.js --}}
                                </div>
                            </div>

                            {{-- Pre-selected offer notice --}}
                            @if($preOffer)
                            <div style="background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.22);border-radius:var(--r);padding:var(--sp-4) var(--sp-5);margin-bottom:var(--sp-5)">
                                <div style="font-size:13px;font-weight:700;color:var(--gold-dark);margin-bottom:3px">
                                    🎁 Offer Applied: {{ $preOffer->title }}
                                </div>
                                <div style="font-size:12.5px;color:var(--text-mid)">
                                    {{ number_format((float)$preOffer->original_price) }} EGP
                                    → <strong>{{ number_format((float)$preOffer->offer_price) }} EGP</strong>
                                    (Save {{ $preOffer->discount_percent }}%)
                                </div>
                                <input type="hidden" name="offer_id" value="{{ $preOffer->id }}">
                            </div>
                            @endif

                            <div class="booking-nav">
                                <span></span>{{-- spacer --}}
                                <button type="button" class="btn btn--primary booking-nav__next" data-next>
                                    Next: Pick a Date
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ── PANEL 2: Date + Time ───────────── --}}
                        <div class="booking-panel" id="panel2">
                            <h3 class="booking-panel__title">2. Choose Date &amp; Time</h3>

                            <div class="form-group">
                                <label class="form-label form-label--required"
                                       for="appointment_date">
                                    Preferred Date
                                </label>
                                <input
                                    type="date"
                                    id="appointment_date"
                                    name="appointment_date"
                                    class="form-control form-control-lg"
                                    required
                                    aria-required="true"
                                >
                                <span class="form-field-note">
                                    Bookings available up to
                                    {{ config('clinic.booking_advance_days', 60) }} days in advance.
                                </span>
                            </div>

                            <div class="form-group">
                                <label class="form-label form-label--required">
                                    Available Time Slots
                                </label>
                                <div id="slotsGrid" aria-live="polite" aria-label="Available time slots">
                                    <div class="slots-empty">
                                        <div class="slots-empty__icon" aria-hidden="true">📅</div>
                                        <p>Select a doctor and date above to see available times.</p>
                                    </div>
                                </div>
                                {{-- Hidden inputs written by JS --}}
                                <input type="hidden" name="slot_start" id="slot_start">
                                <input type="hidden" name="slot_end"   id="slot_end">
                            </div>

                            <div class="booking-nav">
                                <button type="button" class="booking-nav__back" data-back>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                    Back
                                </button>
                                <button type="button" class="btn btn--primary booking-nav__next" data-next>
                                    Next: Your Details
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ── PANEL 3: Patient Details ───────── --}}
                        <div class="booking-panel" id="panel3">
                            <h3 class="booking-panel__title">3. Your Contact Details</h3>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label form-label--required"
                                           for="patient_name">
                                        Full Name
                                    </label>
                                    <input
                                        type="text"
                                        id="patient_name"
                                        name="patient_name"
                                        class="form-control"
                                        placeholder="Your full name"
                                        autocomplete="name"
                                        maxlength="100"
                                        required
                                    >
                                </div>
                                <div class="form-group">
                                    <label class="form-label form-label--required"
                                           for="patient_phone">
                                        Mobile Number
                                    </label>
                                    <input
                                        type="tel"
                                        id="patient_phone"
                                        name="patient_phone"
                                        class="form-control"
                                        placeholder="01x xxxx xxxx"
                                        autocomplete="tel"
                                        inputmode="numeric"
                                        maxlength="15"
                                        required
                                    >
                                    <span class="form-field-note">
                                        We'll send your confirmation via WhatsApp
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="patient_email">
                                    Email Address
                                    <span style="font-weight:400;color:var(--text-light)">
                                        (optional)
                                    </span>
                                </label>
                                <input
                                    type="email"
                                    id="patient_email"
                                    name="patient_email"
                                    class="form-control"
                                    placeholder="your@email.com"
                                    autocomplete="email"
                                    maxlength="150"
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="notes">
                                    Any concerns or special requests?
                                    <span style="font-weight:400;color:var(--text-light)">
                                        (optional)
                                    </span>
                                </label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Tell us about any pain, dental anxiety, or specific concerns…"
                                    maxlength="1000"
                                ></textarea>
                            </div>

                            <div class="booking-nav">
                                <button type="button" class="booking-nav__back" data-back>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                    Back
                                </button>
                                <button type="button" class="btn btn--primary booking-nav__next" data-next>
                                    Review Booking
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ── PANEL 4: Confirm & Submit ──────── --}}
                        <div class="booking-panel" id="panel4">
                            <h3 class="booking-panel__title">4. Confirm Your Booking</h3>

                            <p style="font-size:14px;color:var(--text-muted);margin-bottom:var(--sp-6)">
                                Please review your details below. Once submitted, we'll send a
                                WhatsApp confirmation within the hour.
                            </p>

                            {{-- Review summary (mirrors sidebar on desktop) --}}
                            <div class="confirm-details" style="margin-bottom:var(--sp-6)">
                                @foreach([
                                    ['Service',   'review-service'],
                                    ['Doctor',    'review-doctor'],
                                    ['Date',      'review-date'],
                                    ['Time',      'review-slot'],
                                    ['Price',     'review-price'],
                                ] as [$label, $id])
                                <div class="confirm-row">
                                    <span class="confirm-row__label">{{ $label }}</span>
                                    <span class="confirm-row__value"
                                          id="{{ $id }}"
                                          style="color:var(--text-muted);font-style:italic">
                                        Not selected
                                    </span>
                                </div>
                                @endforeach
                            </div>

                            {{-- Terms --}}
                            <label class="booking-terms">
                                <input type="checkbox"
                                       id="agree_terms"
                                       name="agree_terms"
                                       value="1">
                                <span class="booking-terms__label">
                                    I agree to the
                                    <a href="{{ route('privacy') }}" target="_blank" rel="noopener">
                                        Privacy Policy
                                    </a>
                                    and understand that my booking is subject to confirmation.
                                    Cancellations must be made at least 24 hours in advance.
                                </span>
                            </label>

                            <div class="booking-nav">
                                <button type="button" class="booking-nav__back" data-back>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                    Back
                                </button>
                                <button type="submit"
                                        class="btn btn--primary btn--lg booking-nav__next"
                                        id="submitBookingBtn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    Confirm Booking
                                </button>
                            </div>
                        </div>

                    </form>
                </div>{{-- /booking-card__body --}}
            </div>{{-- /booking-card --}}

            {{-- ══ SIDEBAR ═════════════════════════════════════ --}}
            <aside class="booking-sidebar" aria-label="Booking summary">

                {{-- Live Summary --}}
                <div class="booking-summary" aria-live="polite" aria-label="Your booking so far">
                    <h3 class="booking-summary__title">Your Booking</h3>

                    @foreach([
                        ['Service',  'summary-service'],
                        ['Doctor',   'summary-doctor'],
                        ['Date',     'summary-date'],
                        ['Time',     'summary-slot'],
                        ['Price',    'summary-price'],
                    ] as [$label, $id])
                    <div class="booking-summary__row">
                        <span class="booking-summary__label">{{ $label }}</span>
                        <span class="booking-summary__empty" id="{{ $id }}">—</span>
                    </div>
                    @endforeach
                </div>

                {{-- Clinic Info --}}
                <div class="booking-info-card">
                    <div class="booking-info-card__title">Clinic Details</div>

                    <div class="booking-info-row">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>{{ config('clinic.address', 'Zahraa Maadi, Cairo, Egypt') }}</span>
                    </div>

                    <div class="booking-info-row">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>
                            Sun–Thu: 9 AM – 9 PM<br>
                            Sat: 10 AM – 6 PM
                        </span>
                    </div>

                    <div class="booking-info-row">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 0112 19a19.5 19.5 0 01-7-7"/>
                        </svg>
                        <a href="tel:{{ config('clinic.phone','') }}"
                           style="color:var(--navy);font-weight:500">
                            {{ config('clinic.phone', '+20 100 582 6642') }}
                        </a>
                    </div>
                </div>

                {{-- WhatsApp Alternative --}}
                <div class="booking-wa-alt">
                    <p class="booking-wa-alt__text">
                        <strong>Prefer to book by chat?</strong>
                        Message us on WhatsApp and we'll find you the perfect slot.
                    </p>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}?text={{ urlencode('Hello! I would like to book an appointment at Soly Clinic.') }}"
                       target="_blank" rel="noopener noreferrer"
                       class="btn btn--wa btn--full">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"
                             aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.83 5.83 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Book via WhatsApp
                    </a>
                </div>

            </aside>{{-- /booking-sidebar --}}

        </div>{{-- /booking-layout --}}
    </div>{{-- /container --}}
</section>

@endsection

@push('scripts')
    @vite('resources/js/booking.js')
    <script>
    /* Keep panel 4 review rows in sync with sidebar summary */
    (function () {
        const pairs = [
            ['summary-service', 'review-service'],
            ['summary-doctor',  'review-doctor'],
            ['summary-date',    'review-date'],
            ['summary-slot',    'review-slot'],
            ['summary-price',   'review-price'],
        ];
        const obs = new MutationObserver(() => {
            pairs.forEach(([src, dst]) => {
                const s = document.getElementById(src);
                const d = document.getElementById(dst);
                if (s && d) {
                    d.textContent = s.textContent;
                    d.className   = s.classList.contains('booking-summary__empty')
                        ? 'confirm-row__value' + ' booking-summary__empty'
                        : 'confirm-row__value';
                }
            });
        });
        pairs.forEach(([src]) => {
            const el = document.getElementById(src);
            if (el) obs.observe(el, { characterData: true, childList: true, subtree: true });
        });
    })();
    </script>
@endpush
