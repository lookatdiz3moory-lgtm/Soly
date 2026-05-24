@extends('layouts.app')

@section('title', $doctor->localized_name . ' — Soly Clinic')
@section('meta_description', ($doctor->localized_specialty ? $doctor->localized_specialty . ' specialist. ' : '') . ($doctor->bio_excerpt ?: 'Meet ' . $doctor->localized_name . ' at Soly Clinic in Zahraa Maadi, Cairo.'))

@section('content')

<div class="page-hero" aria-label="{{ $doctor->localized_name }}">
    <div class="container" style="position:relative;z-index:1">
        @if($doctor->localized_specialty)
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">{{ $doctor->localized_specialty }}</div>
        @endif
        <h1 class="page-hero__title">{{ $doctor->localized_name }}</h1>
        @if($doctor->title)
        <p class="page-hero__sub">{{ $doctor->title }}</p>
        @endif
    </div>
</div>

<section style="padding:var(--sp-16) 0">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 320px;gap:var(--sp-12);align-items:start">

            {{-- Main --}}
            <div>

                @if($doctor->localized_bio)
                <h2 style="font-size:1.3rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-5)">{{ __('messages.doctor_detail.about') }}</h2>
                <div style="font-size:1rem;line-height:1.8;color:var(--text-mid);margin-bottom:var(--sp-10)">
                    {!! nl2br(e($doctor->localized_bio)) !!}
                </div>
                @endif

                @if($doctor->specialties && count($doctor->specialties))
                <div style="margin-bottom:var(--sp-10)">
                    <h2 style="font-size:1.3rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-5)">{{ __('messages.doctor_detail.specialties') }}</h2>
                    <div style="display:flex;flex-wrap:wrap;gap:var(--sp-2)">
                        @foreach($doctor->specialties as $spec)
                        <span style="padding:var(--sp-2) var(--sp-4);background:rgba(201,168,76,.1);border-radius:20px;font-size:.85rem;font-weight:600;color:var(--gold-dark)">
                            {{ $spec }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($doctor->qualifications && count($doctor->qualifications))
                <div style="margin-bottom:var(--sp-10)">
                    <h2 style="font-size:1.3rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-5)">{{ __('messages.doctor_detail.qualifications') }}</h2>
                    <ul style="list-style:none;padding:0;margin:0;display:grid;gap:var(--sp-3)">
                        @foreach($doctor->qualifications as $q)
                        <li style="display:flex;align-items:flex-start;gap:var(--sp-3);font-size:.95rem;color:var(--text-mid)">
                            <span style="color:var(--gold-dark);font-weight:700;flex-shrink:0;margin-top:2px" aria-hidden="true">🎓</span>
                            <span>{{ $q }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($doctor->services->isNotEmpty())
                <div>
                    <h2 style="font-size:1.3rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-5)">{{ __('messages.doctor_detail.services_offered') }}</h2>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:var(--sp-3)">
                        @foreach($doctor->services as $service)
                        <a href="{{ route('services.show', $service->slug) }}"
                           style="padding:var(--sp-3) var(--sp-4);border:1px solid var(--border);border-radius:8px;text-decoration:none;font-size:.9rem;font-weight:600;color:var(--navy);transition:border-color .2s,color .2s">
                            {{ $service->localized_name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div style="position:sticky;top:calc(var(--nav-h, 72px) + var(--sp-6))">

                <div style="border-radius:12px;overflow:hidden;margin-bottom:var(--sp-6);background:rgba(201,168,76,.08);aspect-ratio:3/4">
                    <img src="{{ $doctor->photo_url }}"
                         alt="{{ $doctor->localized_name }}"
                         style="width:100%;height:100%;object-fit:cover;display:block">
                </div>

                <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;padding:var(--sp-5);margin-bottom:var(--sp-5)">
                    @if($doctor->experience)
                    <div style="display:flex;justify-content:space-between;padding:var(--sp-3) 0;border-bottom:1px solid var(--border);font-size:.875rem">
                        <span style="color:var(--text-light)">{{ __('messages.doctor_detail.experience_label') }}</span>
                        <span style="font-weight:600;color:var(--navy)">{{ $doctor->experience }}</span>
                    </div>
                    @endif
                    @if($doctor->languages && count($doctor->languages))
                    <div style="display:flex;justify-content:space-between;padding:var(--sp-3) 0;font-size:.875rem">
                        <span style="color:var(--text-light)">{{ __('messages.doctor_detail.languages_label') }}</span>
                        <span style="font-weight:600;color:var(--navy)">{{ implode(', ', $doctor->languages) }}</span>
                    </div>
                    @endif
                </div>

                <a href="{{ route('booking.index') }}?doctor={{ $doctor->id }}"
                   class="btn btn--primary" style="width:100%;text-align:center;display:block;margin-bottom:var(--sp-3)">
                    {{ __('messages.doctor_detail.book_with') }} {{ Str::before($doctor->localized_name, ' ') }}
                </a>
                <a href="{{ route('doctors.index') }}"
                   class="btn btn--outline" style="width:100%;text-align:center;display:block">
                    {{ __('messages.doctor_detail.back_doctors') }}
                </a>

            </div>
        </div>
    </div>
</section>

@endsection
