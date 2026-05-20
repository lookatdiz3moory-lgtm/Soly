{{--
    Hero Component  (Phase 2 — full premium markup)
    Usage: <x-hero />
    Reuses CSS from components.css .hero* classes.
--}}

<section class="hero" id="hero" aria-label="Welcome to Soly Clinic">

  {{-- Layered background --}}
  <div class="hero__bg" aria-hidden="true">
    <div class="hero__bg-gradient"></div>
    <div class="hero__bg-grid"></div>
    <div class="hero__bg-orb hero__bg-orb--1"></div>
    <div class="hero__bg-orb hero__bg-orb--2"></div>
    <div class="hero__bg-orb hero__bg-orb--3"></div>
  </div>

  <div class="container hero__inner">

    {{-- ── LEFT: Content ───────────────────────────────────────── --}}
    <div class="hero__content">

      <div class="hero__badge" data-animate="fade-up" data-delay="0">
        <span class="hero__badge-dot" aria-hidden="true"></span>
        Now Accepting New Patients
      </div>

      <h1 class="hero__headline" data-animate="fade-up" data-delay="80">
        Your Smile.<br>
        <em>Our Passion.</em><br>
        Expert Care.
      </h1>

      <p class="hero__subtext" data-animate="fade-up" data-delay="160">
        Premium dental care in Zahraa Maadi, Cairo. Honest diagnoses, transparent pricing,
        and results that transform your confidence — because you deserve a dentist who
        truly cares.
      </p>

      <div class="hero__actions" data-animate="fade-up" data-delay="240">
        <a href="{{ route('booking') }}" class="btn btn--primary btn--lg">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8"  y1="2" x2="8"  y2="6"/>
            <line x1="3"  y1="10" x2="21" y2="10"/>
          </svg>
          Book Appointment
        </a>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp', '201000000000')) }}?text={{ urlencode('Hello! I would like to book an appointment at Soly Clinic.') }}"
           target="_blank" rel="noopener noreferrer"
           class="btn btn--outline-gold btn--lg">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          WhatsApp Us
        </a>
      </div>

      {{-- Trust Stats --}}
      <div class="hero__stats" data-animate="fade-up" data-delay="320">
        <div class="hero__stat">
          <div class="hero__stat-number">
            <span data-count="500">500</span>
            <span class="hero__stat-suffix">+</span>
          </div>
          <span class="hero__stat-label">Happy Patients</span>
        </div>

        <div class="hero__stat-divider" aria-hidden="true"></div>

        <div class="hero__stat">
          <div class="hero__stat-number">
            <span data-count="10">10</span>
            <span class="hero__stat-suffix">+</span>
          </div>
          <span class="hero__stat-label">Years Experience</span>
        </div>

        <div class="hero__stat-divider" aria-hidden="true"></div>

        <div class="hero__stat">
          <div class="hero__stat-number">
            <span data-count="4.9" data-float="true">4.9</span>
            <span class="hero__stat-suffix">★</span>
          </div>
          <span class="hero__stat-label">Google Rating</span>
        </div>
      </div>
    </div>

    {{-- ── RIGHT: Floating appointment card ────────────────────── --}}
    <div class="hero__visual" data-animate="fade-left" data-delay="120" aria-hidden="true">
      <div class="hero__card">
        <div class="hero__card-header">
          <div class="hero__card-icon">🦷</div>
          <div>
            <div class="hero__card-title">Quick Appointment</div>
            <div class="hero__card-sub">Available Today</div>
          </div>
          <div class="hero__card-badge">Open Now</div>
        </div>

        <div class="hero__card-services">
          @foreach([
            ['✨', 'Hollywood Smile',  'From 8,000 EGP'],
            ['💎', 'Zircon Crowns',    'From 1,800 EGP'],
            ['🏆', 'Dental Implants',  'From 6,000 EGP'],
            ['⭐', 'Teeth Whitening',  'From 500 EGP'],
          ] as [$icon, $name, $price])
          <div class="hero__card-service">
            <span class="hero__card-service-icon">{{ $icon }}</span>
            <div>
              <span class="hero__card-service-name">{{ $name }}</span>
              <span class="hero__card-service-price">{{ $price }}</span>
            </div>
          </div>
          @endforeach
        </div>

        <a href="{{ route('booking') }}" class="btn btn--primary btn--full hero__card-cta">
          Book Free Consultation
        </a>
      </div>

      {{-- Micro-floating badges --}}
      <div class="hero__float hero__float--1">✓ No Hidden Fees</div>
      <div class="hero__float hero__float--2">★★★★★</div>
      <div class="hero__float hero__float--3">🔒 Safe &amp; Sterile</div>
    </div>

  </div>

  {{-- Scroll indicator --}}
  <div class="hero__scroll" aria-hidden="true">
    <div class="hero__scroll-line"></div>
    <span class="hero__scroll-text">Scroll</span>
  </div>

</section>
