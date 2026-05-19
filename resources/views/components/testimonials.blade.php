{{--
    Testimonials Component  (Phase 2)
    Usage: <x-testimonials :testimonials="$testimonials" />
--}}

@props(['testimonials' => null])

@php
  $defaults = [
    ['name'=>'Nadia Hassan',   'rating'=>5,'service'=>'Hollywood Smile', 'review'=>'I was embarrassed to smile for years. Dr. Salim understood exactly what I wanted and exceeded every expectation. I cannot stop smiling now!',          'avatar'=>null,'date'=>'2 weeks ago', 'source'=>'google'],
    ['name'=>'Ahmed Mostafa',  'rating'=>5,'service'=>'Dental Implants', 'review'=>'Had a missing tooth for 3 years. The implant feels completely natural — I actually forget it\'s there. Professional, painless, and fairly priced.',      'avatar'=>null,'date'=>'1 month ago', 'source'=>'google'],
    ['name'=>'Sara El-Sayed',  'rating'=>5,'service'=>'Veneers',         'review'=>'Soly Clinic is the first place where the doctor told me exactly what I needed — nothing more. The veneers are absolutely stunning.',                      'avatar'=>null,'date'=>'3 weeks ago', 'source'=>'whatsapp'],
    ['name'=>'Khaled Ibrahim', 'rating'=>5,'service'=>'Whitening',       'review'=>'Fast, effective, and the results lasted months. I whitened at two other clinics before — nothing compares to the results here.',                          'avatar'=>null,'date'=>'1 month ago', 'source'=>'google'],
    ['name'=>'Mariam Adel',    'rating'=>5,'service'=>'Root Canal',      'review'=>'Was terrified of root canal. Dr. Salim walked me through every step and I felt zero pain. Completely changed my view of dental treatment.',               'avatar'=>null,'date'=>'2 months ago','source'=>'google'],
    ['name'=>'Omar Fathy',     'rating'=>5,'service'=>'Zircon Crowns',   'review'=>'The crowns look completely natural. Cannot tell the difference from real teeth — and I\'ve gotten so many compliments. Dr. Salim is incredibly skilled.', 'avatar'=>null,'date'=>'3 weeks ago', 'source'=>'facebook'],
  ];
  $list = $testimonials && count($testimonials)
    ? (is_array($testimonials) ? $testimonials : $testimonials->toArray())
    : $defaults;
@endphp

<section class="testimonials" id="testimonials" aria-labelledby="testimonials-heading">
  <div class="testimonials__bg-gradient" aria-hidden="true"></div>

  <div class="container" style="position:relative;z-index:1">

    <div class="section-header section-header--light" data-animate="fade-up">
      <div class="section-tag section-tag--light">Patient Reviews</div>
      <h2 class="section-title section-title--light" id="testimonials-heading">
        What Our Patients<br>Say About Us
      </h2>
      <div class="section-divider" aria-hidden="true"></div>
      <p class="section-subtitle section-subtitle--light" style="margin-top:var(--sp-5)">
        Real patients. Real results. Every review is from someone in our community who
        trusted us with their smile.
      </p>
    </div>

    <div class="testimonials__carousel" id="testimonialCarousel" data-animate="fade-up" data-delay="80">
      <div class="testimonials__track" id="testimonialTrack" role="list">

        @foreach($list as $t)
        @php
          $name    = is_array($t) ? $t['name']            : $t->name;
          $rating  = (int)(is_array($t) ? $t['rating']    : $t->rating);
          $service = is_array($t) ? ($t['service'] ?? '')  : ($t->service ?? '');
          $review  = is_array($t) ? $t['review']          : $t->review;
          $avatar  = is_array($t) ? ($t['avatar'] ?? null) : $t->avatar;
          $date    = is_array($t) ? ($t['date'] ?? '')     : ($t->created_at?->diffForHumans() ?? '');
          $source  = is_array($t) ? ($t['source'] ?? 'google') : ($t->source ?? 'google');
          $initial = mb_strtoupper(mb_substr($name, 0, 1));
        @endphp

        <div class="testimonial-card" role="listitem"
             itemscope itemtype="https://schema.org/Review">
          <div class="testimonial-card__quote" aria-hidden="true">&ldquo;</div>

          <div class="testimonial-card__stars" aria-label="{{ $rating }} out of 5 stars">
            @for($s = 1; $s <= 5; $s++)
              <span class="star {{ $s <= $rating ? 'star--filled' : '' }}" aria-hidden="true">★</span>
            @endfor
          </div>

          <blockquote class="testimonial-card__text" itemprop="reviewBody">
            &ldquo;{{ $review }}&rdquo;
          </blockquote>

          <div class="testimonial-card__footer">
            <div class="testimonial-card__author">
              <div class="testimonial-card__avatar" aria-hidden="true">
                @if($avatar)
                  <img src="{{ asset('storage/'.$avatar) }}" alt="{{ $name }}" loading="lazy">
                @else
                  {{ $initial }}
                @endif
              </div>
              <div>
                <div class="testimonial-card__name" itemprop="author">{{ $name }}</div>
                @if($service)
                  <div class="testimonial-card__service">{{ $service }}</div>
                @endif
              </div>
            </div>
            <div class="testimonial-card__meta">
              @if($source === 'google')
                <div title="Google Review" aria-label="Google Review">
                  <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                  </svg>
                </div>
              @elseif($source === 'facebook')
                <div title="Facebook Review" aria-label="Facebook Review">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="#1877F2" aria-hidden="true">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                </div>
              @endif
              @if($date)
                <span class="testimonial-card__date">{{ $date }}</span>
              @endif
            </div>
          </div>
        </div>
        @endforeach

      </div>

      {{-- Carousel controls --}}
      <div class="testimonials__controls" role="group" aria-label="Testimonial navigation">
        <button class="testimonials__btn testimonials__btn--prev" id="testimonialPrev"
                aria-label="Previous testimonial">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
        </button>

        <div class="testimonials__dots" role="tablist" aria-label="Go to testimonial">
          @foreach($list as $i => $_)
            <button class="testimonials__dot {{ $i === 0 ? 'testimonials__dot--active' : '' }}"
                    data-index="{{ $i }}"
                    role="tab"
                    aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                    aria-label="Testimonial {{ $i + 1 }}">
            </button>
          @endforeach
        </div>

        <button class="testimonials__btn testimonials__btn--next" id="testimonialNext"
                aria-label="Next testimonial">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="testimonials__footer" data-animate="fade-up">
      <a href="{{ route('testimonials') }}" class="btn btn--outline-light btn--lg">
        Read All Reviews
      </a>
      @if(config('clinic.google_review_url'))
      <a href="{{ config('clinic.google_review_url') }}" target="_blank" rel="noopener noreferrer"
         class="btn btn--ghost-light btn--lg">
        <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Leave a Google Review
      </a>
      @endif
    </div>

  </div>
</section>
