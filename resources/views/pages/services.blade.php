@extends('layouts.app')

@section('title', 'Our Services — Soly Clinic')
@section('meta_description', 'Explore our full range of dental services at Soly Clinic in Zahraa Maadi: implants, veneers, Hollywood smile, orthodontics, whitening, and more.')

@section('content')

<div class="page-hero" aria-label="Our Services">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">What We Offer</div>
        <h1 class="page-hero__title">Our Dental Services</h1>
        <p class="page-hero__sub">
            From routine check-ups to complete smile transformations.
            Every treatment is tailored, every price is transparent.
        </p>
    </div>
</div>

<section style="padding:var(--sp-16) 0" aria-label="Services catalogue">
    <div class="container">

        @if($services->isEmpty())
        <div style="text-align:center;padding:var(--sp-16) 0;color:var(--text-light)">
            <div style="font-size:56px;margin-bottom:var(--sp-6)" aria-hidden="true">🦷</div>
            <h2 style="color:var(--navy);margin-bottom:var(--sp-4)">Services coming soon</h2>
            <p style="margin-bottom:var(--sp-8)">Our full service catalogue is being prepared. Contact us directly for any enquiries.</p>
            <a href="{{ route('contact.index') }}" class="btn btn--primary">Contact Us</a>
        </div>
        @else

        {{-- Group by category --}}
        @php $grouped = $services->groupBy('category'); @endphp

        @foreach($grouped as $category => $group)
        @if($category)
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.8px;margin-bottom:var(--sp-6);margin-top:var(--sp-12)">
            {{ $category }}
        </h2>
        @endif

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:var(--sp-6);margin-bottom:var(--sp-6)">
            @foreach($group as $service)
            <a href="{{ route('services.show', $service->slug) }}"
               class="service-card {{ $service->is_featured ? 'service-card--featured' : '' }}"
               aria-label="{{ $service->name }}">

                @if($service->is_featured)
                <div class="service-card__featured-badge">Featured</div>
                @endif

                <div class="service-card__icon-wrap">
                    <span class="service-card__icon" aria-hidden="true">
                        {{ $service->icon ?? '🦷' }}
                    </span>
                </div>

                <div class="service-card__body">
                    <h3 class="service-card__name">{{ $service->name }}</h3>
                    @if($service->short_description)
                    <p class="service-card__desc">{{ $service->short_description }}</p>
                    @endif
                </div>

                @if($service->price_from)
                <div class="service-card__price">
                    <span class="service-card__price-from">From</span>
                    <span class="service-card__price-amount">{{ number_format((float)$service->price_from) }}</span>
                    <span class="service-card__price-currency">EGP</span>
                </div>
                @endif

                <div class="service-card__actions">
                    @if($service->is_bookable)
                    <span class="btn btn--primary service-card__book" style="pointer-events:none">Book Now</span>
                    @else
                    <span class="btn btn--outline service-card__book" style="pointer-events:none">Learn More</span>
                    @endif
                </div>

            </a>
            @endforeach
        </div>
        @endforeach

        @endif

        <div style="text-align:center;margin-top:var(--sp-12)">
            <p style="color:var(--text-mid);margin-bottom:var(--sp-6)">
                Not sure which service you need? Our team will guide you through your options with zero pressure.
            </p>
            <a href="{{ route('booking.index') }}" class="btn btn--primary">Book a Consultation</a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
               target="_blank" rel="noopener"
               class="btn btn--outline" style="margin-left:var(--sp-3)">Ask on WhatsApp</a>
        </div>

    </div>
</section>

@endsection
