@extends('layouts.app')

@section('title', 'Special Offers — Soly Clinic')
@section('meta_description', 'Browse current special offers and promotional pricing at Soly Clinic in Zahraa Maadi, Cairo. Limited-time deals on dental treatments.')

@section('content')

<div class="page-hero" aria-label="Special Offers">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">Limited Time</div>
        <h1 class="page-hero__title">Special Offers</h1>
        <p class="page-hero__sub">
            Quality care at exceptional value. Our offers are transparent — no hidden conditions, no surprises.
        </p>
    </div>
</div>

<section style="padding:var(--sp-16) 0" aria-label="Current offers">
    <div class="container">

        @if($offers->isEmpty())
        <div style="text-align:center;padding:var(--sp-16) 0;color:var(--text-light)">
            <div style="font-size:56px;margin-bottom:var(--sp-6)" aria-hidden="true">🎁</div>
            <h2 style="color:var(--navy);margin-bottom:var(--sp-4)">No active offers right now</h2>
            <p style="margin-bottom:var(--sp-8)">Check back soon — we regularly run promotions. Or contact us to ask about pricing.</p>
            <a href="{{ route('contact.index') }}" class="btn btn--primary">Ask About Pricing</a>
        </div>
        @else

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:var(--sp-6)">
            @foreach($offers as $offer)
            <a href="{{ route('offers.show', $offer->id) }}"
               style="display:flex;flex-direction:column;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;text-decoration:none;color:inherit;box-shadow:0 2px 12px rgba(11,21,32,.06);transition:box-shadow .2s,transform .2s"
               aria-label="{{ $offer->title }}">

                @if($offer->is_featured)
                <div style="background:var(--gold-dark);color:#fff;font-size:.75rem;font-weight:700;text-align:center;padding:var(--sp-2);letter-spacing:.5px;text-transform:uppercase">
                    ⭐ Featured Offer
                </div>
                @endif

                @if($offer->badge_text)
                <div style="padding:var(--sp-4) var(--sp-6) 0">
                    <span style="font-size:.7rem;font-weight:700;padding:3px 10px;background:rgba(201,168,76,.15);color:var(--gold-dark);border-radius:4px;text-transform:uppercase;letter-spacing:.5px">
                        {{ $offer->badge_text }}
                    </span>
                </div>
                @endif

                <div style="padding:var(--sp-5) var(--sp-6);flex:1">
                    <h2 style="font-size:1.1rem;font-weight:700;color:var(--navy);margin:0 0 var(--sp-2)">{{ $offer->title }}</h2>

                    @if($offer->service)
                    <div style="font-size:.8rem;color:var(--text-light);margin-bottom:var(--sp-3)">{{ $offer->service->name }}</div>
                    @endif

                    @if($offer->description)
                    <p style="font-size:.875rem;line-height:1.6;color:var(--text-mid);margin:0 0 var(--sp-5)">
                        {{ Str::limit($offer->description, 120) }}
                    </p>
                    @endif
                </div>

                <div style="padding:var(--sp-4) var(--sp-6) var(--sp-6);border-top:1px solid var(--border);display:flex;align-items:flex-end;justify-content:space-between;gap:var(--sp-4)">
                    <div>
                        @if($offer->original_price && (float)$offer->original_price > (float)$offer->offer_price)
                        <div style="font-size:.8rem;color:var(--text-light);text-decoration:line-through">
                            {{ number_format((float)$offer->original_price) }} EGP
                        </div>
                        @endif
                        <div style="display:flex;align-items:baseline;gap:var(--sp-1)">
                            <span style="font-size:1.5rem;font-weight:800;color:var(--gold-dark)">{{ number_format((float)$offer->offer_price) }}</span>
                            <span style="font-size:.8rem;color:var(--text-light);font-weight:600">EGP</span>
                        </div>
                    </div>

                    @if($offer->discount_percent > 0)
                    <div style="background:var(--navy);color:#fff;border-radius:8px;padding:var(--sp-2) var(--sp-3);text-align:center;flex-shrink:0">
                        <div style="font-size:1.1rem;font-weight:800;line-height:1">{{ $offer->discount_percent }}%</div>
                        <div style="font-size:.6rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;opacity:.8">off</div>
                    </div>
                    @endif
                </div>

                @if($offer->expires_at)
                <div style="background:rgba(11,21,32,.03);padding:var(--sp-3) var(--sp-6);font-size:.75rem;color:var(--text-light);border-top:1px solid var(--border)">
                    Expires {{ $offer->expires_at->format('d M Y') }}
                </div>
                @endif

            </a>
            @endforeach
        </div>

        @endif

        <div style="text-align:center;margin-top:var(--sp-12)">
            <p style="color:var(--text-mid);margin-bottom:var(--sp-6)">
                Offers are subject to availability. Contact us to confirm before booking.
            </p>
            <a href="{{ route('booking.index') }}" class="btn btn--primary">Book Now</a>
            <a href="{{ route('services.index') }}" class="btn btn--outline" style="margin-left:var(--sp-3)">All Services</a>
        </div>

    </div>
</section>

@endsection
