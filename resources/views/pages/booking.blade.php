@extends('layouts.app')

@section('title', 'Book an Appointment — Soly Clinic')
@section('meta_description', 'Book your dental appointment at Soly Clinic in Zahraa Maadi, Cairo. Choose your service, doctor, date, and time online in under 2 minutes.')

@push('styles')
    @vite('resources/css/booking.css')
@endpush

@section('content')

{{-- ── Page Hero ─────────────────────────────────────────────── --}}
<div class="page-hero" aria-label="{{ __('messages.booking_page.page_heading') }}">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">
            {{ __('messages.booking_page.page_tag') }}
        </div>
        <h1 class="page-hero__title">{{ __('messages.booking_page.page_heading') }}</h1>
        <p class="page-hero__sub">{{ __('messages.booking_page.page_sub') }}</p>
    </div>
</div>

{{-- ── Main Content ─────────────────────────────────────────── --}}
<section class="booking-page" aria-label="{{ __('messages.booking_page.page_heading') }}">
    <div class="container">
        <div class="booking-layout">

            {{-- ══ FORM CARD ════════════════════════════════════ --}}
            <div class="booking-card">
                <div class="booking-card__header">
                    <h2 class="booking-card__title">{{ __('messages.booking_page.card_title') }}</h2>
                    <p class="booking-card__sub">{{ __('messages.booking_page.card_sub') }}</p>
                </div>

                <div class="booking-card__body">

                    {{-- Progress Steps --}}
                    <div class="booking-steps" role="list" aria-label="{{ __('messages.booking_page.card_title') }}">
                        @foreach([
                            __('messages.booking_page.step1'),
                            __('messages.booking_page.step2'),
                            __('messages.booking_page.step3'),
                            __('messages.booking_page.step4'),
                        ] as $i => $label)
                        <div class="booking-step {{ $i === 0 ? 'is-active' : '' }}"
                             role="listitem"
                             aria-current="{{ $i === 0 ? 'step' : 'false' }}">
                            <div class="booking-step__circle">{{ $i + 1 }}</div>
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
                            <h3 class="booking-panel__title">{{ __('messages.booking_page.panel1_heading') }}</h3>

                            <div class="form-group">
                                <label class="form-label form-label--required" for="service_id">
                                    {{ __('messages.booking_page.service_label') }}
                                </label>
                                <select
                                    id="service_id"
                                    name="service_id"
                                    class="form-control form-control-lg"
                                    required
                                    aria-required="true"
                                    aria-describedby="service-hint"
                                >
                                    <option value="">{{ __('messages.booking_page.service_placeholder') }}</option>
                                    @foreach($services as $svc)
                                    <option
                                        value="{{ $svc->id }}"
                                        data-slug="{{ $svc->slug }}"
                                        data-price="{{ $svc->price_from ? number_format((float)$svc->price_from) . ' EGP' : '' }}"
                                        {{ optional($preService)->id == $svc->id ? 'selected' : '' }}
                                    >
                                        {{ $svc->localized_name }}
                                        @if($svc->price_from)
                                            — {{ __('messages.booking_page.service_from', ['price' => number_format((float)$svc->price_from)]) }}
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <span id="service-hint" class="form-field-note">
                                    {{ __('messages.booking_page.cant_find') }}
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
                                       target="_blank" rel="noopener" style="color:var(--gold-dark)">
                                        {{ __('messages.booking_page.ask_wa') }}
                                    </a>
                                </span>
                            </div>

                            <div id="doctorSection" class="form-group" style="display:none">
                                <label class="form-label form-label--required">
                                    {{ __('messages.booking_page.choose_doctor') }}
                                </label>
                                <div id="doctorGrid" class="doctor-grid" role="radiogroup"
                                     aria-label="{{ __('messages.booking_page.available_doctors') }}">
                                    {{-- Populated by booking.js --}}
                                </div>
                            </div>

                            @if($preOffer)
                            <div style="background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.22);border-radius:var(--r);padding:var(--sp-4) var(--sp-5);margin-bottom:var(--sp-5)">
                                <div style="font-size:13px;font-weight:700;color:var(--gold-dark);margin-bottom:3px">
                                    {{ __('messages.booking_page.offer_applied') }} {{ $preOffer->title }}
                                </div>
                                <div style="font-size:12.5px;color:var(--text-mid)">
                                    {{ number_format((float)$preOffer->original_price) }} EGP
                                    → <strong>{{ number_format((float)$preOffer->offer_price) }} EGP</strong>
                                    ({{ __('messages.booking_page.save_label', ['pct' => $preOffer->discount_percent]) }})
                                </div>
                                <input type="hidden" name="offer_id" value="{{ $preOffer->id }}">
                            </div>
                            @endif

                            <div class="booking-nav">
                                <span></span>
                                <button type="button" class="btn btn--primary booking-nav__next" data-next>
                                    {{ __('messages.booking_page.next_date') }}
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ── PANEL 2: Date + Time ───────────── --}}
                        <div class="booking-panel" id="panel2">
                            <h3 class="booking-panel__title">{{ __('messages.booking_page.panel2_heading') }}</h3>

                            <div class="form-group">
                                <label class="form-label form-label--required" for="appointment_date">
                                    {{ __('messages.booking_page.date_label') }}
                                </label>
                                <input
                                    type="date"
                                    id="appointment_date"
                                    name="appointment_date"
                                    class="form-control form-control-lg"
                                    required
                                    aria-required="true"
                                    dir="ltr"
                                >
                                <span class="form-field-note">
                                    {{ __('messages.booking_page.date_hint', ['days' => config('clinic.booking_advance_days', 60)]) }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label class="form-label form-label--required">
                                    {{ __('messages.booking_page.time_label') }}
                                </label>
                                <div id="slotsGrid" aria-live="polite" aria-label="{{ __('messages.booking_page.time_label') }}">
                                    <div class="slots-empty">
                                        <div class="slots-empty__icon" aria-hidden="true">📅</div>
                                        <p>{{ __('messages.booking_page.time_empty') }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="slot_start" id="slot_start">
                                <input type="hidden" name="slot_end"   id="slot_end">
                            </div>

                            <div class="booking-nav">
                                <button type="button" class="booking-nav__back" data-back>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                    {{ __('messages.booking_page.back') }}
                                </button>
                                <button type="button" class="btn btn--primary booking-nav__next" data-next>
                                    {{ __('messages.booking_page.next_details') }}
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ── PANEL 3: Patient Details ───────── --}}
                        <div class="booking-panel" id="panel3">
                            <h3 class="booking-panel__title">{{ __('messages.booking_page.panel3_heading') }}</h3>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label form-label--required" for="patient_name">
                                        {{ __('messages.booking_page.name_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="patient_name"
                                        name="patient_name"
                                        class="form-control"
                                        placeholder="{{ __('messages.booking_page.name_placeholder') }}"
                                        autocomplete="name"
                                        maxlength="100"
                                        required
                                    >
                                </div>
                                <div class="form-group">
                                    <label class="form-label form-label--required" for="patient_phone">
                                        {{ __('messages.booking_page.phone_label') }}
                                    </label>
                                    <input
                                        type="tel"
                                        id="patient_phone"
                                        name="patient_phone"
                                        class="form-control"
                                        placeholder="{{ __('messages.booking_page.phone_placeholder') }}"
                                        autocomplete="tel"
                                        inputmode="numeric"
                                        maxlength="15"
                                        required
                                        dir="ltr"
                                    >
                                    <span class="form-field-note">
                                        {{ __('messages.booking_page.phone_note') }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="patient_email">
                                    {{ __('messages.booking_page.email_label') }}
                                    <span style="font-weight:400;color:var(--text-light)">
                                        {{ __('messages.booking_page.optional') }}
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
                                    dir="ltr"
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="notes">
                                    {{ __('messages.booking_page.notes_label') }}
                                    <span style="font-weight:400;color:var(--text-light)">
                                        {{ __('messages.booking_page.optional') }}
                                    </span>
                                </label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    class="form-control"
                                    rows="3"
                                    placeholder="{{ __('messages.booking_page.notes_placeholder') }}"
                                    maxlength="1000"
                                ></textarea>
                            </div>

                            <div class="booking-nav">
                                <button type="button" class="booking-nav__back" data-back>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                    {{ __('messages.booking_page.back') }}
                                </button>
                                <button type="button" class="btn btn--primary booking-nav__next" data-next>
                                    {{ __('messages.booking_page.review_booking') }}
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ── PANEL 4: Confirm & Submit ──────── --}}
                        <div class="booking-panel" id="panel4">
                            <h3 class="booking-panel__title">{{ __('messages.booking_page.panel4_heading') }}</h3>

                            <p style="font-size:14px;color:var(--text-muted);margin-bottom:var(--sp-6)">
                                {{ __('messages.booking_page.confirm_text') }}
                            </p>

                            <div class="confirm-details" style="margin-bottom:var(--sp-6)">
                                @foreach([
                                    [__('messages.booking_page.row_service'), 'review-service'],
                                    [__('messages.booking_page.row_doctor'),  'review-doctor'],
                                    [__('messages.booking_page.row_date'),    'review-date'],
                                    [__('messages.booking_page.row_time'),    'review-slot'],
                                    [__('messages.booking_page.row_price'),   'review-price'],
                                ] as [$label, $id])
                                <div class="confirm-row">
                                    <span class="confirm-row__label">{{ $label }}</span>
                                    <span class="confirm-row__value"
                                          id="{{ $id }}"
                                          style="color:var(--text-muted);font-style:italic">
                                        {{ __('messages.booking_page.not_selected') }}
                                    </span>
                                </div>
                                @endforeach
                            </div>

                            <label class="booking-terms">
                                <input type="checkbox"
                                       id="agree_terms"
                                       name="agree_terms"
                                       value="1">
                                <span class="booking-terms__label">
                                    {{ __('messages.booking_page.terms_prefix') }}
                                    <a href="{{ route('privacy') }}" target="_blank" rel="noopener">
                                        {{ __('messages.booking_page.terms_policy') }}
                                    </a>
                                    {{ __('messages.booking_page.terms_suffix') }}
                                </span>
                            </label>

                            <div class="booking-nav">
                                <button type="button" class="booking-nav__back" data-back>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                    {{ __('messages.booking_page.back') }}
                                </button>
                                <button type="submit"
                                        class="btn btn--primary btn--lg booking-nav__next"
                                        id="submitBookingBtn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    {{ __('messages.booking_page.confirm_booking') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            {{-- ══ SIDEBAR ═════════════════════════════════════ --}}
            <aside class="booking-sidebar" aria-label="{{ __('messages.booking_page.summary_title') }}">

                <div class="booking-summary" aria-live="polite" aria-label="{{ __('messages.booking_page.summary_title') }}">
                    <h3 class="booking-summary__title">{{ __('messages.booking_page.summary_title') }}</h3>

                    @foreach([
                        [__('messages.booking_page.row_service'), 'summary-service'],
                        [__('messages.booking_page.row_doctor'),  'summary-doctor'],
                        [__('messages.booking_page.row_date'),    'summary-date'],
                        [__('messages.booking_page.row_time'),    'summary-slot'],
                        [__('messages.booking_page.row_price'),   'summary-price'],
                    ] as [$label, $id])
                    <div class="booking-summary__row">
                        <span class="booking-summary__label">{{ $label }}</span>
                        <span class="booking-summary__empty" id="{{ $id }}">—</span>
                    </div>
                    @endforeach
                </div>

                <div class="booking-info-card">
                    <div class="booking-info-card__title">{{ __('messages.booking_page.clinic_details') }}</div>

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
                            {{ __('messages.booking_page.hours_sun_thu') }}<br>
                            {{ __('messages.booking_page.hours_sat') }}
                        </span>
                    </div>

                    <div class="booking-info-row">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 0112 19a19.5 19.5 0 01-7-7"/>
                        </svg>
                        <a href="tel:{{ config('clinic.phone','') }}"
                           style="color:var(--navy);font-weight:500" dir="ltr">
                            {{ config('clinic.phone', '+20 100 582 6642') }}
                        </a>
                    </div>
                </div>

                <div class="booking-wa-alt">
                    <p class="booking-wa-alt__text">
                        <strong>{{ __('messages.booking_page.wa_prefer') }}</strong>
                        {{ __('messages.booking_page.wa_prefer_sub') }}
                    </p>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}?text={{ urlencode('Hello! I would like to book an appointment at Soly Clinic.') }}"
                       target="_blank" rel="noopener noreferrer"
                       class="btn btn--wa btn--full">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"
                             aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.83 5.83 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        {{ __('messages.booking_page.wa_book_btn') }}
                    </a>
                </div>

            </aside>

        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script>
    window.bookingI18n = {
        locale:        "{{ app()->getLocale() }}",
        errorService:  "{{ addslashes(__('messages.booking_page.error_service')) }}",
        errorDoctor:   "{{ addslashes(__('messages.booking_page.error_doctor')) }}",
        errorDate:     "{{ addslashes(__('messages.booking_page.error_date')) }}",
        errorSlot:     "{{ addslashes(__('messages.booking_page.error_slot')) }}",
        errorName:     "{{ addslashes(__('messages.booking_page.error_name')) }}",
        errorPhone:    "{{ addslashes(__('messages.booking_page.error_phone')) }}",
        errorPhoneFmt: "{{ addslashes(__('messages.booking_page.error_phone_fmt')) }}",
        errorEmail:    "{{ addslashes(__('messages.booking_page.error_email')) }}",
        errorTerms:    "{{ addslashes(__('messages.booking_page.error_terms')) }}",
        errorNetwork:  "{{ addslashes(__('messages.booking_page.error_network')) }}",
        errorSession:  "{{ addslashes(__('messages.booking_page.error_session')) }}",
        errorTooMany:  "{{ addslashes(__('messages.booking_page.error_too_many')) }}",
        confirming:    "{{ addslashes(__('messages.booking_page.confirming')) }}",
        priceFrom:     "{{ addslashes(__('messages.booking_page.price_from')) }}",
    };
    </script>
    @vite('resources/js/booking.js')
    <script>
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
