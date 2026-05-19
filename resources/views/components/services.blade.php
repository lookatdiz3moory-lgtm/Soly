{{--
    Services Component  (Phase 2)
    Usage: <x-services :services="$featuredServices" :limit="6" />
--}}

@props([
    'services' => null,
    'limit'    => 6,
    'showAll'  => true,
])

@php
  $defaults = [
    ['slug'=>'teeth-cleaning',  'icon'=>'🦷','name'=>'Teeth Cleaning',   'short_description'=>'Professional scaling and polishing for a healthy, fresh mouth. Removes tartar and plaque buildup.', 'price_from'=>150,  'is_featured'=>false],
    ['slug'=>'veneers',         'icon'=>'✨','name'=>'Veneers',          'short_description'=>'Ultra-thin porcelain shells that transform your smile. Natural-looking, stain-resistant, long-lasting.','price_from'=>2500, 'is_featured'=>true],
    ['slug'=>'zircon-crowns',   'icon'=>'💎','name'=>'Zircon Crowns',    'short_description'=>'Metal-free zirconia crowns — exceptional strength with a completely natural look and feel.',             'price_from'=>1800, 'is_featured'=>true],
    ['slug'=>'hollywood-smile', 'icon'=>'😁','name'=>'Hollywood Smile',  'short_description'=>'Complete smile transformation: veneers, whitening, and contouring for a camera-ready result every day.','price_from'=>8000, 'is_featured'=>true],
    ['slug'=>'dental-implants', 'icon'=>'🏆','name'=>'Dental Implants',  'short_description'=>'Permanent titanium implants that look, feel, and function exactly like your natural teeth.',           'price_from'=>6000, 'is_featured'=>true],
    ['slug'=>'teeth-whitening', 'icon'=>'⭐','name'=>'Teeth Whitening',  'short_description'=>'Professional in-office whitening — up to 8 shades brighter in a single 60-minute session.',           'price_from'=>500,  'is_featured'=>true],
    ['slug'=>'orthodontics',    'icon'=>'📐','name'=>'Orthodontics',     'short_description'=>'Traditional braces and clear aligners for a perfectly aligned, confident smile at any age.',            'price_from'=>5000, 'is_featured'=>false],
    ['slug'=>'root-canal',      'icon'=>'🔬','name'=>'Root Canal',       'short_description'=>'Pain-free root canal therapy to save your natural tooth and eliminate infection permanently.',          'price_from'=>800,  'is_featured'=>false],
    ['slug'=>'facial-skincare', 'icon'=>'💆','name'=>'Facial Skin Care', 'short_description'=>'Advanced facial treatments to complement your smile — HydraFacial, PRP, and rejuvenation.',            'price_from'=>400,  'is_featured'=>false],
  ];

  $list = $services && count($services)
    ? array_slice(is_array($services) ? $services : $services->toArray(), 0, $limit)
    : array_slice($defaults, 0, $limit);

  $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', config('clinic.whatsapp', '201000000000'));
@endphp

<section class="services" id="services" aria-labelledby="services-heading">
  <div class="container">

    <div class="section-header" data-animate="fade-up">
      <div class="section-tag">Our Services</div>
      <h2 class="section-title" id="services-heading">
        Expert Dental Care<br>for Every Smile
      </h2>
      <div class="section-divider" aria-hidden="true"></div>
      <p class="section-subtitle" style="margin-top:var(--sp-5)">
        From routine cleanings to complete smile transformations — every modern treatment
        with transparent pricing and genuine care.
      </p>
    </div>

    <div class="services__grid">
      @foreach($list as $i => $svc)
      @php
        $slug  = is_array($svc) ? $svc['slug']              : $svc->slug;
        $icon  = is_array($svc) ? ($svc['icon'] ?? '🦷')    : ($svc->icon ?? '🦷');
        $name  = is_array($svc) ? $svc['name']              : $svc->name;
        $desc  = is_array($svc) ? $svc['short_description'] : $svc->short_description;
        $price = is_array($svc) ? ($svc['price_from'] ?? null) : $svc->price_from;
        $feat  = is_array($svc) ? ($svc['is_featured'] ?? false) : (bool)$svc->is_featured;
      @endphp

      <article class="service-card"
               data-animate="fade-up"
               data-delay="{{ min($i * 70, 280) }}"
               itemscope itemtype="https://schema.org/MedicalProcedure">

        @if($feat)
          <div class="service-card__featured-badge" aria-label="Popular service">Popular</div>
        @endif

        <div class="service-card__icon-wrap" aria-hidden="true">
          <span class="service-card__icon">{{ $icon }}</span>
        </div>

        <div class="service-card__body">
          <h3 class="service-card__name" itemprop="name">{{ $name }}</h3>
          <p class="service-card__desc" itemprop="description">{{ $desc }}</p>

          @if($price)
          <div class="service-card__price"
               itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <span class="service-card__price-from">From</span>
            <span class="service-card__price-amount" itemprop="price">{{ number_format($price) }}</span>
            <span class="service-card__price-currency" itemprop="priceCurrency">EGP</span>
          </div>
          @endif
        </div>

        <div class="service-card__actions">
          <a href="{{ route('booking') }}?service={{ $slug }}"
             class="btn btn--primary btn--sm service-card__book">
            Book Now
          </a>
          <a href="{{ route('services.show', $slug) }}"
             class="btn btn--ghost btn--sm">
            Learn More
          </a>
        </div>

        <a href="{{ $wa }}?text={{ urlencode('Hello! I\'m interested in ' . $name . '.') }}"
           target="_blank" rel="noopener noreferrer"
           class="service-card__wa"
           aria-label="Enquire about {{ $name }} on WhatsApp">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.83 5.83 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          Ask on WhatsApp
        </a>
      </article>
      @endforeach
    </div>

    @if($showAll)
    <div class="services__footer" data-animate="fade-up">
      <a href="{{ route('services') }}" class="btn btn--outline btn--lg">
        View All Services
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" aria-hidden="true">
          <line x1="5" y1="12" x2="19" y2="12"/>
          <polyline points="12 5 19 12 12 19"/>
        </svg>
      </a>
    </div>
    @endif

  </div>
</section>
