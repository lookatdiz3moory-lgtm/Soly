@extends('layouts.app')

@section('title', 'Soly Clinic — Premier Dental Care in Zahraa Maadi, Cairo')
@section('meta_description', 'Soly Clinic offers premium dental care in Zahraa Maadi, Cairo. Implants, veneers, Hollywood smile, whitening, and more. Honest treatment, transparent pricing. Book today.')

@section('content')

{{-- 1. HERO ──────────────────────────────────────────────────── --}}
<x-hero />

{{-- 2. TRUST BAR ─────────────────────────────────────────────── --}}
<x-trust-bar />

{{-- 3. SERVICES ─────────────────────────────────────────────── --}}
<x-services :services="$featuredServices ?? null" :limit="6" />

{{-- 4. WHY US ──────────────────────────────────────────────────── --}}
<section class="why-us" id="why-us" aria-labelledby="why-heading">
  <div class="container">
    <div class="section-header section-header--light" data-animate="fade-up">
      <div class="section-tag section-tag--light">{{ __('messages.home.why_tag') }}</div>
      <h2 class="section-title section-title--light" id="why-heading">
        {{ __('messages.home.why_title') }}
      </h2>
      <div class="section-divider" aria-hidden="true"></div>
      <p class="section-subtitle section-subtitle--light" style="margin-top:var(--sp-5)">
        {{ __('messages.home.why_sub') }}
      </p>
    </div>

    <div class="why-grid">
      @foreach([
        ['icon'=>'⚖️', 'title'=> __('messages.home.why1_title'), 'desc'=> __('messages.home.why1_desc')],
        ['icon'=>'💰', 'title'=> __('messages.home.why2_title'), 'desc'=> __('messages.home.why2_desc')],
        ['icon'=>'🏆', 'title'=> __('messages.home.why3_title'), 'desc'=> __('messages.home.why3_desc')],
        ['icon'=>'😌', 'title'=> __('messages.home.why4_title'), 'desc'=> __('messages.home.why4_desc')],
        ['icon'=>'👨‍👩‍👧','title'=> __('messages.home.why5_title'), 'desc'=> __('messages.home.why5_desc')],
        ['icon'=>'📍', 'title'=> __('messages.home.why6_title'), 'desc'=> __('messages.home.why6_desc')],
      ] as $i => $item)
      <div class="why-card" data-animate="fade-up" data-delay="{{ $i * 75 }}">
        <div class="why-card__icon" aria-hidden="true">{{ $item['icon'] }}</div>
        <h3>{{ $item['title'] }}</h3>
        <p>{{ $item['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- 5. BEFORE / AFTER ─────────────────────────────────────────── --}}
<section class="before-after" id="gallery" aria-labelledby="gallery-heading">
  <div class="container">
    <div class="section-header" data-animate="fade-up">
      <div class="section-tag">{{ __('messages.home.gallery_tag') }}</div>
      <h2 class="section-title" id="gallery-heading">
        {{ __('messages.home.gallery_title') }}
      </h2>
      <div class="section-divider" aria-hidden="true"></div>
      <p class="section-subtitle" style="margin-top:var(--sp-5)">
        {{ __('messages.home.gallery_sub') }}
        <small style="display:block;margin-top:4px;font-size:12px;color:var(--text-light)">
          {{ __('messages.home.gallery_consent') }}
        </small>
      </p>
    </div>

    <div class="before-after__grid">
      @forelse($beforeAfterItems ?? [] as $i => $item)
        <div class="ba-item" data-animate="fade-up" data-delay="{{ ($i % 2) * 100 }}">
          <div class="ba-images">
            <div class="ba-image">
              <img data-src="{{ $item->before_image_url ?? asset('images/placeholder-ba.svg') }}"
                   src="{{ asset('images/placeholder-1x1.svg') }}"
                   alt="{{ __('messages.gallery_page.before') }} {{ $item->treatment ?? '' }}" loading="lazy">
              <div class="ba-label">{{ __('messages.gallery_page.before') }}</div>
            </div>
            <div class="ba-image">
              <img data-src="{{ $item->after_image_url ?? asset('images/placeholder-ba.svg') }}"
                   src="{{ asset('images/placeholder-1x1.svg') }}"
                   alt="{{ __('messages.gallery_page.after') }} {{ $item->treatment ?? '' }}" loading="lazy">
              <div class="ba-label">{{ __('messages.gallery_page.after') }}</div>
            </div>
          </div>
          <div class="ba-caption">
            <div class="ba-treatment">{{ $item->treatment ?? __('messages.hero.card_hollywood') }}</div>
            @if(!empty($item->description))
              <p class="ba-note">{{ $item->description }}</p>
            @endif
          </div>
        </div>
      @empty
        @foreach([
          __('messages.nav.veneers'),
          __('messages.nav.hollywood_smile'),
          __('messages.nav.zircon_crowns'),
          __('messages.nav.dental_implants'),
        ] as $i => $label)
          <div class="ba-item" data-animate="fade-up" data-delay="{{ ($i % 2) * 100 }}">
            <div class="ba-images">
              <div class="ba-image" style="background:var(--navy-mid);display:flex;align-items:center;justify-content:center;">
                <span style="font-size:44px;opacity:.25">🦷</span>
                <div class="ba-label">{{ __('messages.gallery_page.before') }}</div>
              </div>
              <div class="ba-image" style="background:var(--navy-light);display:flex;align-items:center;justify-content:center;">
                <span style="font-size:44px;opacity:.35">😁</span>
                <div class="ba-label">{{ __('messages.gallery_page.after') }}</div>
              </div>
            </div>
            <div class="ba-caption">
              <div class="ba-treatment">{{ $label }}</div>
              <p class="ba-note">{{ __('messages.common.real_consent') }}</p>
            </div>
          </div>
        @endforeach
      @endforelse
    </div>

    <div style="display:flex;justify-content:center;margin-top:var(--sp-12)" data-animate="fade-up">
      <a href="{{ route('gallery') }}" class="btn btn--outline btn--lg">
        {{ __('messages.home.gallery_view_all') }}
      </a>
    </div>
  </div>
</section>

{{-- 6. DOCTORS ───────────────────────────────────────────────── --}}
<x-doctors :doctors="$doctors ?? null" :limit="3" />

{{-- 7. TESTIMONIALS ─────────────────────────────────────────── --}}
<x-testimonials :testimonials="$testimonials ?? null" />

{{-- 8. CTA BANNER ──────────────────────────────────────────── --}}
<x-cta-banner />

{{-- 9. FAQ ─────────────────────────────────────────────────── --}}
@if(!empty($faqs) && count($faqs))
<section class="section" id="faq" style="background:var(--white)"
         aria-labelledby="faq-heading">
  <div class="container">
    <div style="display:grid;grid-template-columns:300px 1fr;gap:var(--sp-16);align-items:start"
         class="faq-layout">

      <div data-animate="fade-up" style="position:sticky;top:calc(var(--navbar-total,108px) + var(--sp-8))">
        <div class="section-tag">{{ __('messages.home.faq_tag') }}</div>
        <h2 class="section-title" id="faq-heading" style="margin-top:var(--sp-3)">
          {{ __('messages.home.faq_heading') }}
        </h2>
        <div class="section-divider section-divider--left" aria-hidden="true"
             style="margin-top:var(--sp-4);margin-bottom:var(--sp-5)"></div>
        <p class="section-subtitle" style="text-align:start">
          {{ __('messages.home.faq_still_have') }}
        </p>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
           target="_blank" rel="noopener noreferrer"
           class="btn btn--wa" style="margin-top:var(--sp-6)">
          {{ __('messages.home.faq_ask_wa') }}
        </a>
      </div>

      <div class="faq__list" data-animate="fade-up" data-delay="80">
        @foreach($faqs as $faq)
        <div class="faq__item" itemscope itemtype="https://schema.org/Question">
          <button class="faq__question" aria-expanded="false" itemprop="name">
            {{ is_array($faq) ? $faq['question'] : $faq->localized_question }}
            <svg class="faq__icon" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
          <div class="faq__answer" itemscope itemtype="https://schema.org/Answer">
            <div class="faq__answer-inner" itemprop="text">
              {{ is_array($faq) ? $faq['answer'] : $faq->localized_answer }}
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div style="display:flex;justify-content:center;margin-top:var(--sp-10)"
         data-animate="fade-up">
      <a href="{{ route('faq') }}" class="btn btn--outline btn--lg">
        {{ __('messages.home.faq_view_all') }}
      </a>
    </div>
  </div>
</section>
@endif

{{-- 10. LOCATION ─────────────────────────────────────────────── --}}
<section class="section section--sm" id="location"
         style="background:var(--cream)"
         aria-labelledby="location-heading">
  <div class="container">
    <div class="section-header" data-animate="fade-up">
      <div class="section-tag">{{ __('messages.home.location_tag') }}</div>
      <h2 class="section-title" id="location-heading">
        {{ __('messages.home.location_heading') }}
      </h2>
    </div>

    <div style="display:grid;grid-template-columns:360px 1fr;gap:var(--sp-12);align-items:start"
         data-animate="fade-up" data-delay="80"
         class="location-layout">

      <div style="display:flex;flex-direction:column;gap:var(--sp-6)">
        @foreach([
          ['svg'=>'<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>','label'=> __('messages.home.location_address'),'value'=>config('clinic.address','Zahraa Maadi, Cairo, Egypt'),'href'=>null],
          ['svg'=>'<path d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 0112 19a19.5 19.5 0 01-7-7 2 2 0 011.85-2.12h3"/>','label'=> __('messages.home.location_phone'),'value'=>config('clinic.phone','+20 100 582 6642'),'href'=>'tel:'.config('clinic.phone','')],
          ['svg'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>','label'=> __('messages.home.location_hours'),'value'=> __('messages.home.location_hours_val'),'href'=>null],
        ] as $det)
        <div style="display:flex;gap:var(--sp-4);align-items:flex-start">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5"
               style="color:var(--gold-dark);flex-shrink:0;margin-top:2px" aria-hidden="true">
            {!! $det['svg'] !!}
          </svg>
          <div>
            <div style="font-size:11.5px;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light);font-weight:700;margin-bottom:3px">
              {{ $det['label'] }}
            </div>
            <div style="font-size:14.5px;color:var(--navy);line-height:1.6">
              @if($det['href'])
                <a href="{{ $det['href'] }}" style="color:var(--navy)">{{ $det['value'] }}</a>
              @else
                {!! nl2br(e($det['value'])) !!}
              @endif
            </div>
          </div>
        </div>
        @endforeach

        <a href="{{ config('clinic.google_maps_url','#') }}" target="_blank" rel="noopener noreferrer"
           class="btn btn--primary" style="margin-top:var(--sp-2)">
          {{ __('messages.home.location_directions') }}
        </a>
      </div>

      <div style="border-radius:var(--r-lg);overflow:hidden;min-height:380px;background:var(--gray-100)">
        @if(config('clinic.google_maps_embed'))
          <div class="map-embed-placeholder"
               data-src="{{ config('clinic.google_maps_embed') }}"
               style="min-height:380px;border-radius:inherit;background:var(--gray-100)">
          </div>
        @else
          <div style="min-height:380px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--text-light);gap:var(--sp-4);text-align:center;padding:var(--sp-8)">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width=".8" aria-hidden="true">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <p style="font-size:14px;color:var(--text-light)">
              {{ __('messages.home.location_map_placeholder') }}
            </p>
            <a href="{{ config('clinic.google_maps_url','#') }}" target="_blank"
               rel="noopener noreferrer" class="btn btn--outline btn--sm">
              {{ __('messages.home.location_open_maps') }}
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
window.__clinicData = {
  whatsapp:   "{{ config('clinic.whatsapp','') }}",
  bookingUrl: "{{ route('booking') }}"
};
</script>
@endpush
