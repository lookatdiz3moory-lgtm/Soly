@extends('layouts.app')

@section('title', ($pageTitle ?? 'Coming Soon') . ' — Soly Clinic')

@section('content')
<section style="padding: 6rem 1.5rem; text-align: center; max-width: 720px; margin: 0 auto;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 1rem;">
        {{ $pageTitle ?? 'Coming Soon' }}
    </h1>
    <p style="color: #555; font-size: 1.05rem; line-height: 1.7;">
        {{ $pageMessage ?? "This page is being prepared. Please check back shortly or contact us directly." }}
    </p>
    <p style="margin-top: 2rem;">
        <a href="{{ route('home') }}" class="btn btn--primary">Back to Home</a>
        <a href="{{ route('booking') }}" class="btn btn--outline" style="margin-left: .5rem;">Book Appointment</a>
    </p>
</section>
@endsection
