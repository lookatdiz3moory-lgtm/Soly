{{--
    Doctors Component  (Phase 2)
    Usage: <x-doctors :doctors="$doctors" :limit="3" />
--}}

@props(['doctors' => null, 'limit' => 3])

@php
  $defaults = [[
    'id'         => 1,
    'name'       => 'Dr. Salim',
    'title'      => 'BDS, Military Medical Corps',
    'specialty'  => 'General & Cosmetic Dentistry',
    'bio'        => 'Founder of Soly Clinic, trusted by hundreds of patients across Zahraa Maadi. Known for honest diagnoses, precision technique, and fair pricing. Proud graduate of military medical service.',
    'photo'      => null,
    'experience' => '10+ Years',
    'specialties'=> ['Implants','Hollywood Smile','Veneers','Root Canal'],
    'is_featured'=> true,
  ]];

  $list = $doctors && count($doctors)
    ? array_slice(is_array($doctors) ? $doctors : $doctors->toArray(), 0, $limit)
    : array_slice($defaults, 0, $limit);
@endphp

<section class="doctors" id="doctors" aria-labelledby="doctors-heading">
  <div class="container">

    <div class="section-header" data-animate="fade-up">
      <div class="section-tag">Our Team</div>
      <h2 class="section-title" id="doctors-heading">
        Meet Your<br>Dental Experts
      </h2>
      <div class="section-divider" aria-hidden="true"></div>
      <p class="section-subtitle" style="margin-top:var(--sp-5)">
        Precision-trained, patient-first professionals who believe in honest treatment
        and lasting results.
      </p>
    </div>

    <div class="doctors__grid">
      @foreach($list as $i => $doc)
      @php
        $id    = is_array($doc) ? ($doc['id'] ?? $i+1)            : $doc->id;
        $name  = is_array($doc) ? $doc['name']                    : $doc->name;
        $title = is_array($doc) ? ($doc['title'] ?? '')           : ($doc->title ?? '');
        $spec  = is_array($doc) ? ($doc['specialty'] ?? '')       : ($doc->specialty ?? '');
        $bio   = is_array($doc) ? ($doc['bio'] ?? '')             : ($doc->bio ?? '');
        $photo = is_array($doc) ? ($doc['photo'] ?? null)         : $doc->photo;
        $exp   = is_array($doc) ? ($doc['experience'] ?? '')      : ($doc->experience ?? '');
        $tags  = is_array($doc) ? ($doc['specialties'] ?? [])     : (json_decode($doc->specialties ?? '[]', true) ?: []);
        $feat  = is_array($doc) ? ($doc['is_featured'] ?? false)  : false;
        $photoUrl = $photo ? asset('storage/'.$photo) : null;
      @endphp

      <article class="doctor-card {{ $feat ? 'doctor-card--featured' : '' }}"
               data-animate="fade-up"
               data-delay="{{ $i * 100 }}"
               itemscope itemtype="https://schema.org/Physician">

        {{-- Photo --}}
        <div class="doctor-card__photo-wrap">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="Photo of {{ $name }}"
                 class="doctor-card__photo" loading="lazy" itemprop="image"
                 width="400" height="300">
          @else
            <img src="{{ asset('images/doctor-placeholder.svg') }}"
                 alt="Doctor photo coming soon"
                 class="doctor-card__photo doctor-card__photo--placeholder"
                 loading="lazy" aria-hidden="true"
                 width="400" height="300">
          @endif

          @if($feat)
            <div class="doctor-card__featured-tag">Lead Doctor</div>
          @endif

          @if($exp)
            <div class="doctor-card__exp-badge">{{ $exp }}</div>
          @endif
        </div>

        {{-- Body --}}
        <div class="doctor-card__body">
          <h3 class="doctor-card__name" itemprop="name">{{ $name }}</h3>
          @if($title)
            <p class="doctor-card__title" itemprop="honorificSuffix">{{ $title }}</p>
          @endif
          @if($spec)
            <p class="doctor-card__specialty" itemprop="medicalSpecialty">{{ $spec }}</p>
          @endif
          @if($bio)
            <p class="doctor-card__bio">{{ Str::limit($bio, 155) }}</p>
          @endif

          @if(count($tags))
            <div class="doctor-card__tags" aria-label="Specialties">
              @foreach($tags as $tag)
                <span class="doctor-card__tag">{{ $tag }}</span>
              @endforeach
            </div>
          @endif

          <div class="doctor-card__actions">
            <a href="{{ route('booking') }}?doctor={{ $id }}"
               class="btn btn--primary btn--sm">
              Book with {{ Str::before($name, ' ') === 'Dr.' ? explode(' ', $name)[1] : explode(' ', $name)[0] }}
            </a>
            <a href="{{ route('doctors.show', $id) }}"
               class="btn btn--ghost btn--sm">
              Profile
            </a>
          </div>
        </div>

      </article>
      @endforeach
    </div>

    <div class="doctors__footer" data-animate="fade-up">
      <a href="{{ route('doctors') }}" class="btn btn--outline btn--lg">
        Meet All Doctors
      </a>
    </div>

  </div>
</section>
