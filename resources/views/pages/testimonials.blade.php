@extends('layouts.app')

@section('title', 'Patient Reviews — Soly Clinic')
@section('meta_description', 'Read genuine patient reviews for Soly Clinic in Zahraa Maadi. Verified feedback from Google, Facebook, and WhatsApp.')

@section('content')

<div class="page-hero" aria-label="Testimonials">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">Patient Stories</div>
        <h1 class="page-hero__title">What Our Patients Say</h1>
        <p class="page-hero__sub">
            Real reviews from real patients. We never edit or filter feedback — you deserve the full picture.
        </p>
    </div>
</div>

<section style="padding:var(--sp-16) 0" aria-label="Patient testimonials">
    <div class="container">

        @if($testimonials->isEmpty())
        <div style="text-align:center;padding:var(--sp-16) 0;color:var(--text-light)">
            <div style="font-size:56px;margin-bottom:var(--sp-6)" aria-hidden="true">⭐</div>
            <h2 style="color:var(--navy);margin-bottom:var(--sp-4)">Reviews coming soon</h2>
            <p style="margin-bottom:var(--sp-8)">Patient reviews will appear here. Contact us or find us on Google Maps.</p>
            <a href="{{ route('contact.index') }}" class="btn btn--primary">Contact Us</a>
        </div>
        @else

        @php
            $featured = $testimonials->where('is_featured', true);
            $rest     = $testimonials->where('is_featured', false);
        @endphp

        {{-- Featured --}}
        @if($featured->isNotEmpty())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:var(--sp-6);margin-bottom:var(--sp-12)">
            @foreach($featured as $item)
            <blockquote style="border:1px solid rgba(201,168,76,.35);border-radius:12px;padding:var(--sp-7);background:rgba(201,168,76,.04);box-shadow:0 4px 20px rgba(201,168,76,.1);margin:0;position:relative">
                <div style="position:absolute;top:var(--sp-5);right:var(--sp-5);font-size:.7rem;font-weight:700;padding:3px 8px;background:rgba(201,168,76,.15);color:var(--gold-dark);border-radius:4px;text-transform:uppercase;letter-spacing:.5px">Featured</div>
                <div style="color:var(--gold-dark);font-size:1.1rem;letter-spacing:2px;margin-bottom:var(--sp-4)" aria-label="{{ $item->rating }} out of 5 stars">{{ $item->stars_html }}</div>
                @if($item->title)
                <div style="font-weight:700;color:var(--navy);margin-bottom:var(--sp-3)">{{ $item->title }}</div>
                @endif
                <p style="font-size:.95rem;line-height:1.7;color:var(--text-mid);margin:0 0 var(--sp-5);font-style:italic">"{{ $item->excerpt }}"</p>
                <footer style="display:flex;align-items:center;gap:var(--sp-3)">
                    <div style="width:38px;height:38px;border-radius:50%;background:rgba(201,168,76,.15);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.95rem;color:var(--gold-dark);flex-shrink:0">
                        {{ $item->initial }}
                    </div>
                    <div>
                        <div style="font-size:.9rem;font-weight:700;color:var(--navy)">{{ $item->name }}</div>
                        @if($item->service)
                        <div style="font-size:.75rem;color:var(--text-light)">{{ $item->service }}</div>
                        @endif
                    </div>
                </footer>
            </blockquote>
            @endforeach
        </div>
        @endif

        {{-- All other reviews --}}
        @if($rest->isNotEmpty())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--sp-5)">
            @foreach($rest as $item)
            <blockquote style="border:1px solid var(--border);border-radius:10px;padding:var(--sp-5) var(--sp-6);background:#fff;margin:0">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--sp-3)">
                    <div style="color:var(--gold-dark);font-size:1rem;letter-spacing:1.5px" aria-label="{{ $item->rating }} out of 5 stars">{{ $item->stars_html }}</div>
                    @if($item->source !== 'internal')
                    <span style="font-size:.7rem;color:var(--text-light);text-transform:capitalize">via {{ $item->source }}</span>
                    @endif
                </div>
                <p style="font-size:.875rem;line-height:1.7;color:var(--text-mid);margin:0 0 var(--sp-4);font-style:italic">"{{ $item->excerpt }}"</p>
                <footer style="display:flex;align-items:center;gap:var(--sp-3)">
                    <div style="width:34px;height:34px;border-radius:50%;background:rgba(11,21,32,.07);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:var(--navy);flex-shrink:0">
                        {{ $item->initial }}
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--navy)">{{ $item->name }}</div>
                        @if($item->service)
                        <div style="font-size:.75rem;color:var(--text-light)">{{ $item->service }}</div>
                        @endif
                    </div>
                </footer>
            </blockquote>
            @endforeach
        </div>
        @endif

        @endif

        <div style="text-align:center;margin-top:var(--sp-12)">
            <p style="color:var(--text-mid);margin-bottom:var(--sp-6)">
                Join hundreds of happy patients. Book your appointment today.
            </p>
            <a href="{{ route('booking.index') }}" class="btn btn--primary">Book Now</a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
               target="_blank" rel="noopener"
               class="btn btn--outline" style="margin-left:var(--sp-3)">Ask on WhatsApp</a>
        </div>

    </div>
</section>

@endsection
