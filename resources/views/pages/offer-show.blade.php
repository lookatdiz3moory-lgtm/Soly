@extends('layouts.app')

@section('title', $offer->title . ' — Soly Clinic')
@section('meta_description', $offer->description ? Str::limit($offer->description, 160) : 'Special offer at Soly Clinic in Zahraa Maadi, Cairo.')

@section('content')

<div class="page-hero" aria-label="{{ $offer->title }}">
    <div class="container" style="position:relative;z-index:1">
        @if($offer->badge_text)
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">{{ $offer->badge_text }}</div>
        @endif
        <h1 class="page-hero__title">{{ $offer->title }}</h1>
        @if($offer->service)
        <p class="page-hero__sub">{{ $offer->service->name }}</p>
        @endif
    </div>
</div>

<section style="padding:var(--sp-16) 0">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 320px;gap:var(--sp-12);align-items:start">

            {{-- Main --}}
            <div>
                @if($offer->description)
                <div style="font-size:1.05rem;line-height:1.8;color:var(--text-mid);margin-bottom:var(--sp-10)">
                    {!! nl2br(e($offer->description)) !!}
                </div>
                @endif

                @if($offer->terms)
                <div style="background:rgba(11,21,32,.03);border-radius:10px;padding:var(--sp-6);border-left:3px solid var(--gold-dark)">
                    <h3 style="font-size:.95rem;font-weight:700;color:var(--navy);margin:0 0 var(--sp-3)">Terms &amp; Conditions</h3>
                    <div style="font-size:.875rem;line-height:1.7;color:var(--text-mid)">
                        {!! nl2br(e($offer->terms)) !!}
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div style="position:sticky;top:calc(var(--nav-h, 72px) + var(--sp-6))">
                <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 4px 24px rgba(11,21,32,.07)">

                    @if($offer->discount_percent > 0)
                    <div style="background:var(--navy);padding:var(--sp-5);text-align:center">
                        <div style="font-size:3rem;font-weight:900;color:var(--gold-dark);line-height:1">{{ $offer->discount_percent }}%</div>
                        <div style="font-size:.8rem;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.5px">Discount</div>
                    </div>
                    @endif

                    <div style="padding:var(--sp-6)">

                        {{-- Pricing --}}
                        @if($offer->original_price && (float)$offer->original_price > (float)$offer->offer_price)
                        <div style="margin-bottom:var(--sp-5);padding-bottom:var(--sp-5);border-bottom:1px solid var(--border)">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--sp-2)">
                                <span style="font-size:.8rem;color:var(--text-light)">Original price</span>
                                <span style="font-size:.95rem;color:var(--text-light);text-decoration:line-through">{{ number_format((float)$offer->original_price) }} EGP</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--sp-2)">
                                <span style="font-size:.8rem;color:var(--text-light)">Offer price</span>
                                <div style="display:flex;align-items:baseline;gap:4px">
                                    <span style="font-size:1.6rem;font-weight:800;color:var(--gold-dark)">{{ number_format((float)$offer->offer_price) }}</span>
                                    <span style="font-size:.8rem;color:var(--text-light)">EGP</span>
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <span style="font-size:.8rem;color:var(--text-light)">You save</span>
                                <span style="font-size:.9rem;font-weight:700;color:var(--navy)">{{ number_format($offer->saving) }} EGP</span>
                            </div>
                        </div>
                        @else
                        <div style="text-align:center;margin-bottom:var(--sp-5);padding-bottom:var(--sp-5);border-bottom:1px solid var(--border)">
                            <div style="display:flex;align-items:baseline;gap:4px;justify-content:center">
                                <span style="font-size:2rem;font-weight:800;color:var(--gold-dark)">{{ number_format((float)$offer->offer_price) }}</span>
                                <span style="font-size:.85rem;color:var(--text-light)">EGP</span>
                            </div>
                        </div>
                        @endif

                        {{-- Expiry --}}
                        @if($offer->expires_at)
                        <div style="display:flex;align-items:center;gap:var(--sp-2);margin-bottom:var(--sp-4);font-size:.85rem;color:var(--text-mid)">
                            <span aria-hidden="true">⏰</span>
                            <span>Expires {{ $offer->expires_at->format('d M Y') }}</span>
                        </div>
                        @endif

                        <a href="{{ route('booking.index') }}{{ $offer->service_id ? '?service=' . $offer->service_id : '' }}"
                           class="btn btn--primary" style="width:100%;text-align:center;display:block;margin-bottom:var(--sp-3)">
                            Book This Offer
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
                           target="_blank" rel="noopener"
                           class="btn btn--outline" style="width:100%;text-align:center;display:block">
                            Ask on WhatsApp
                        </a>

                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top:var(--sp-12);padding-top:var(--sp-8);border-top:1px solid var(--border)">
            <a href="{{ route('offers.index') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;font-size:.9rem">
                ← All Offers
            </a>
        </div>

    </div>
</section>

@endsection
