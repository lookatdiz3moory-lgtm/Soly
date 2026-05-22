@extends('layouts.app')

@section('title', ($service->meta_title ?? $service->name) . ' — Soly Clinic')
@section('meta_description', $service->meta_description ?? $service->short_description ?? 'Learn about ' . $service->name . ' at Soly Clinic in Zahraa Maadi, Cairo.')

@section('content')

<div class="page-hero" aria-label="{{ $service->name }}">
    <div class="container" style="position:relative;z-index:1">
        @if($service->category)
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">{{ $service->category }}</div>
        @endif
        <h1 class="page-hero__title">{{ $service->name }}</h1>
        @if($service->short_description)
        <p class="page-hero__sub">{{ $service->short_description }}</p>
        @endif
    </div>
</div>

<section style="padding:var(--sp-16) 0">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 340px;gap:var(--sp-12);align-items:start">

            {{-- Main content --}}
            <div>
                @if($service->description)
                <div style="font-size:1.05rem;line-height:1.8;color:var(--text-mid);margin-bottom:var(--sp-10)">
                    {!! nl2br(e($service->description)) !!}
                </div>
                @endif

                @if($service->benefits && count($service->benefits))
                <div style="margin-bottom:var(--sp-10)">
                    <h2 style="font-size:1.3rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-6)">
                        Benefits
                    </h2>
                    <ul style="list-style:none;padding:0;margin:0;display:grid;gap:var(--sp-3)">
                        @foreach($service->benefits as $benefit)
                        <li style="display:flex;align-items:flex-start;gap:var(--sp-3);font-size:.95rem;color:var(--text-mid)">
                            <span style="color:var(--gold-dark);font-weight:700;flex-shrink:0;margin-top:2px">✓</span>
                            <span>{{ $benefit }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($service->doctors->isNotEmpty())
                <div>
                    <h2 style="font-size:1.3rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-6)">
                        Treating Doctors
                    </h2>
                    <div style="display:flex;flex-wrap:wrap;gap:var(--sp-4)">
                        @foreach($service->doctors as $doctor)
                        <a href="{{ route('doctors.show', $doctor->id) }}"
                           style="display:flex;align-items:center;gap:var(--sp-3);padding:var(--sp-3) var(--sp-4);border:1px solid var(--border);border-radius:8px;text-decoration:none;color:inherit;transition:border-color .2s">
                            <div style="width:40px;height:40px;border-radius:50%;background:rgba(201,168,76,.12);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--gold-dark);flex-shrink:0">
                                {{ mb_strtoupper(mb_substr($doctor->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:.9rem;font-weight:600;color:var(--navy)">{{ $doctor->name }}</div>
                                @if($doctor->title)
                                <div style="font-size:.8rem;color:var(--text-light)">{{ $doctor->title }}</div>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div style="position:sticky;top:calc(var(--nav-h, 72px) + var(--sp-6))">
                <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 4px 24px rgba(11,21,32,.07)">

                    @if($service->icon)
                    <div style="background:rgba(201,168,76,.08);padding:var(--sp-6);text-align:center;font-size:48px">
                        {{ $service->icon }}
                    </div>
                    @endif

                    <div style="padding:var(--sp-6)">

                        @if($service->price_from)
                        <div style="margin-bottom:var(--sp-5);padding-bottom:var(--sp-5);border-bottom:1px solid var(--border)">
                            <div style="font-size:.75rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--text-light);margin-bottom:var(--sp-2)">Starting From</div>
                            <div style="display:flex;align-items:baseline;gap:var(--sp-2)">
                                <span style="font-size:2rem;font-weight:800;color:var(--gold-dark)">{{ number_format((float)$service->price_from) }}</span>
                                <span style="font-size:.85rem;color:var(--text-light);font-weight:600">EGP</span>
                            </div>
                            @if($service->price_to)
                            <div style="font-size:.8rem;color:var(--text-light);margin-top:var(--sp-1)">Up to {{ number_format((float)$service->price_to) }} EGP</div>
                            @endif
                        </div>
                        @endif

                        @if($service->duration_minutes)
                        <div style="display:flex;align-items:center;gap:var(--sp-3);margin-bottom:var(--sp-4);font-size:.9rem;color:var(--text-mid)">
                            <span aria-hidden="true">⏱</span>
                            <span>Approx. {{ $service->duration_minutes }} minutes</span>
                        </div>
                        @endif

                        @if($service->is_bookable)
                        <a href="{{ route('booking.index') }}?service={{ $service->id }}"
                           class="btn btn--primary" style="width:100%;text-align:center;display:block;margin-bottom:var(--sp-3)">
                            Book This Service
                        </a>
                        @endif

                        <a href="{{ route('contact.index') }}"
                           class="btn btn--outline" style="width:100%;text-align:center;display:block">
                            Ask a Question
                        </a>

                    </div>
                </div>
            </div>

        </div>

        {{-- Back link --}}
        <div style="margin-top:var(--sp-12);padding-top:var(--sp-8);border-top:1px solid var(--border)">
            <a href="{{ route('services.index') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;font-size:.9rem">
                ← All Services
            </a>
        </div>

    </div>
</section>

@endsection
