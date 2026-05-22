@extends('layouts.app')

@section('title', 'Our Doctors — Soly Clinic')
@section('meta_description', 'Meet the qualified dental specialists at Soly Clinic in Zahraa Maadi, Cairo. Expert care, transparent pricing, genuine compassion.')

@section('content')

<div class="page-hero" aria-label="Our Team">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">Our Team</div>
        <h1 class="page-hero__title">Meet Our Doctors</h1>
        <p class="page-hero__sub">
            Skilled, compassionate, and honest. Every doctor at Soly Clinic shares the same commitment — your health first, always.
        </p>
    </div>
</div>

<section style="padding:var(--sp-16) 0" aria-label="Doctors">
    <div class="container">

        @if($doctors->isEmpty())
        <div style="text-align:center;padding:var(--sp-16) 0;color:var(--text-light)">
            <div style="font-size:56px;margin-bottom:var(--sp-6)" aria-hidden="true">👨‍⚕️</div>
            <h2 style="color:var(--navy);margin-bottom:var(--sp-4)">Team profiles coming soon</h2>
            <p style="margin-bottom:var(--sp-8)">Our doctors are getting their profiles ready. Contact us to meet the team.</p>
            <a href="{{ route('contact.index') }}" class="btn btn--primary">Contact Us</a>
        </div>
        @else

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--sp-8)">
            @foreach($doctors as $doctor)
            <a href="{{ route('doctors.show', $doctor->id) }}"
               style="display:block;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;text-decoration:none;color:inherit;transition:box-shadow .2s,transform .2s;box-shadow:0 2px 12px rgba(11,21,32,.06)"
               aria-label="{{ $doctor->name }}">

                {{-- Photo / avatar --}}
                <div style="background:rgba(201,168,76,.08);aspect-ratio:4/3;overflow:hidden">
                    <img src="{{ $doctor->photo_url }}"
                         alt="{{ $doctor->name }}"
                         style="width:100%;height:100%;object-fit:cover;display:block"
                         loading="lazy">
                </div>

                <div style="padding:var(--sp-5) var(--sp-6) var(--sp-6)">
                    <h2 style="font-size:1.15rem;font-weight:700;color:var(--navy);margin:0 0 var(--sp-1)">{{ $doctor->name }}</h2>

                    @if($doctor->title)
                    <div style="font-size:.8rem;color:var(--text-light);margin-bottom:var(--sp-2)">{{ $doctor->title }}</div>
                    @endif

                    @if($doctor->specialty)
                    <div style="font-size:.85rem;font-weight:600;color:var(--gold-dark);margin-bottom:var(--sp-3)">{{ $doctor->specialty }}</div>
                    @endif

                    @if($doctor->bio_excerpt)
                    <p style="font-size:.875rem;line-height:1.6;color:var(--text-mid);margin:0 0 var(--sp-4)">
                        {{ $doctor->bio_excerpt }}
                    </p>
                    @endif

                    @if($doctor->experience)
                    <div style="display:flex;align-items:center;gap:var(--sp-2);font-size:.8rem;color:var(--text-light)">
                        <span aria-hidden="true">🏅</span>
                        <span>{{ $doctor->experience }} experience</span>
                    </div>
                    @endif
                </div>

            </a>
            @endforeach
        </div>

        @endif

        <div style="text-align:center;margin-top:var(--sp-12)">
            <p style="color:var(--text-mid);margin-bottom:var(--sp-6)">
                Ready to book? Choose a doctor and pick a time that works for you.
            </p>
            <a href="{{ route('booking.index') }}" class="btn btn--primary">Book an Appointment</a>
        </div>

    </div>
</section>

@endsection
