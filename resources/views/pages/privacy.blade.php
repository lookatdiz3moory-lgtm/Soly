@extends('layouts.app')

@section('title', 'Privacy Policy — Soly Clinic')
@section('meta_description', 'Privacy Policy for Soly Dental Clinic. Learn how we collect, use, and protect your personal information.')

@section('content')

<div class="page-hero" aria-label="Privacy Policy">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">Legal</div>
        <h1 class="page-hero__title">Privacy Policy</h1>
        <p class="page-hero__sub">Last updated: {{ date('F Y') }}</p>
    </div>
</div>

<section aria-label="Privacy Policy content" style="padding:var(--sp-16) 0">
    <div class="container" style="max-width:780px">

        <p style="color:var(--text-mid);margin-bottom:var(--sp-8)">
            At {{ config('clinic.name', 'Soly Clinic') }}, we are committed to protecting your
            personal information. This policy explains what data we collect, how we use it,
            and your rights as a patient.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            Information We Collect
        </h2>
        <p style="color:var(--text-mid);margin-bottom:var(--sp-4)">
            When you book an appointment we collect your name, mobile number, and optionally
            your email address. This information is used solely to manage your appointment
            and contact you about it.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            How We Use Your Information
        </h2>
        <p style="color:var(--text-mid);margin-bottom:var(--sp-4)">
            Your information is used to confirm, remind, and follow up on your appointment.
            We do not sell or share your personal data with third parties.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            Data Retention
        </h2>
        <p style="color:var(--text-mid);margin-bottom:var(--sp-4)">
            Appointment records are retained for a minimum of five years in accordance with
            Egyptian medical records regulations. You may request deletion of non-medical
            personal data by contacting us directly.
        </p>

        <h2 style="font-size:1.25rem;font-weight:700;color:var(--navy);margin:var(--sp-8) 0 var(--sp-3)">
            Contact
        </h2>
        <p style="color:var(--text-mid)">
            For any privacy-related questions, contact us at
            <a href="mailto:{{ config('clinic.email', 'info@solyclinic.com') }}"
               style="color:var(--gold-dark)">
                {{ config('clinic.email', 'info@solyclinic.com') }}
            </a>
            or call
            <a href="tel:{{ config('clinic.phone', '') }}" style="color:var(--gold-dark)">
                {{ config('clinic.phone', '+20 100 582 6642') }}
            </a>.
        </p>

    </div>
</section>

@endsection
