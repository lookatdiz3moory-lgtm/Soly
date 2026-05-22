@extends('layouts.app')

@section('title', 'Terms & Conditions — Soly Clinic')
@section('meta_description', 'Terms and Conditions for booking appointments at Soly Dental Clinic in Zahraa Maadi, Cairo.')

@section('content')

<div class="page-hero" aria-label="Terms and Conditions">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">Legal</div>
        <h1 class="page-hero__title">Terms &amp; Conditions</h1>
        <p class="page-hero__sub">Last updated: {{ date('F Y') }}</p>
    </div>
</div>

<section aria-label="Terms and Conditions content" style="padding:var(--sp-16) 0">
    <div class="container" style="max-width:780px">

        <p style="color:var(--text-mid);margin-bottom:var(--sp-8)">
            By booking an appointment at {{ config('clinic.name', 'Soly Clinic') }}, you agree
            to the following terms. Please read them carefully before submitting your booking.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            Appointments
        </h2>
        <p style="color:var(--text-mid);margin-bottom:var(--sp-4)">
            All bookings are subject to confirmation by the clinic. You will be notified via
            WhatsApp or phone within one hour of submitting your request. A booking is only
            confirmed once you receive an explicit confirmation message from us.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            Cancellations
        </h2>
        <p style="color:var(--text-mid);margin-bottom:var(--sp-4)">
            We ask that you cancel at least 24 hours before your scheduled appointment.
            Late cancellations or no-shows may affect your ability to book future appointments.
            To cancel, visit your confirmation link or contact us directly.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            Pricing
        </h2>
        <p style="color:var(--text-mid);margin-bottom:var(--sp-4)">
            Prices quoted online are indicative only. Final pricing is confirmed during your
            consultation. Prices may vary depending on your specific treatment needs.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            Contact
        </h2>
        <p style="color:var(--text-mid)">
            Questions about these terms? Reach us at
            <a href="mailto:{{ config('clinic.email', 'info@solyclinic.com') }}"
               style="color:var(--gold-dark)">
                {{ config('clinic.email', 'info@solyclinic.com') }}
            </a>
            or
            <a href="tel:{{ config('clinic.phone', '') }}" style="color:var(--gold-dark)">
                {{ config('clinic.phone', '+20 100 582 6642') }}
            </a>.
        </p>

    </div>
</section>

@endsection
