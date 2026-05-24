@extends('layouts.app')

@section('title', 'Gallery — Real Patient Results — Soly Clinic')
@section('meta_description', 'See real before and after results from patients at Soly Clinic in Zahraa Maadi. Dental implants, veneers, Hollywood smile, and more transformations.')

@section('content')

<div class="page-hero" aria-label="{{ __('messages.gallery_page.page_heading') }}">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">{{ __('messages.gallery_page.page_tag') }}</div>
        <h1 class="page-hero__title">{{ __('messages.gallery_page.page_heading') }}</h1>
        <p class="page-hero__sub">{{ __('messages.gallery_page.page_sub') }}</p>
    </div>
</div>

<section style="padding:var(--sp-16) 0" aria-label="{{ __('messages.gallery_page.page_heading') }}">
    <div class="container">

        @if($items->isEmpty())
        <div style="text-align:center;padding:var(--sp-16) 0;color:var(--text-light)">
            <div style="font-size:56px;margin-bottom:var(--sp-6)" aria-hidden="true">📷</div>
            <h2 style="color:var(--navy);margin-bottom:var(--sp-4)">{{ __('messages.gallery_page.empty_heading') }}</h2>
            <p style="margin-bottom:var(--sp-8)">{{ __('messages.gallery_page.empty_sub') }}</p>
            <a href="{{ route('contact') }}" class="btn btn--primary">{{ __('messages.buttons.contact_us') }}</a>
        </div>
        @else

        @php
            $beforeAfter = $items->where('type', 'before_after');
            $general     = $items->whereIn('type', ['general', 'clinic', 'team']);
        @endphp

        @if($beforeAfter->isNotEmpty())
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.8px;margin-bottom:var(--sp-8)">
            {{ __('messages.gallery_page.before_after_heading') }}
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:var(--sp-8);margin-bottom:var(--sp-12)">
            @foreach($beforeAfter as $item)
            <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 2px 12px rgba(11,21,32,.06)">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:2px;background:var(--border)">
                    <div style="position:relative;overflow:hidden;aspect-ratio:1/1;background:#f5f0e8">
                        <img src="{{ $item->before_image_url }}"
                             alt="{{ __('messages.gallery_page.before') }} {{ $item->treatment ?? '' }}"
                             style="width:100%;height:100%;object-fit:cover" loading="lazy">
                        <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(11,21,32,.6);color:#fff;font-size:.7rem;font-weight:700;text-align:center;padding:4px;text-transform:uppercase;letter-spacing:.5px">{{ __('messages.gallery_page.before') }}</div>
                    </div>
                    <div style="position:relative;overflow:hidden;aspect-ratio:1/1;background:#f5f0e8">
                        <img src="{{ $item->after_image_url }}"
                             alt="{{ __('messages.gallery_page.after') }} {{ $item->treatment ?? '' }}"
                             style="width:100%;height:100%;object-fit:cover" loading="lazy">
                        <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(201,168,76,.85);color:#fff;font-size:.7rem;font-weight:700;text-align:center;padding:4px;text-transform:uppercase;letter-spacing:.5px">{{ __('messages.gallery_page.after') }}</div>
                    </div>
                </div>

                @if($item->treatment || $item->title || $item->description)
                <div style="padding:var(--sp-4) var(--sp-5)">
                    @if($item->title)
                    <div style="font-size:.95rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-1)">{{ $item->title }}</div>
                    @endif
                    @if($item->treatment && !$item->title)
                    <div style="font-size:.85rem;font-weight:600;color:var(--gold-dark)">{{ $item->treatment }}</div>
                    @endif
                    @if($item->description)
                    <div style="font-size:.8rem;color:var(--text-light);margin-top:var(--sp-1)">{{ $item->description }}</div>
                    @endif
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif

        @if($general->isNotEmpty())
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.8px;margin-bottom:var(--sp-8)">
            {{ __('messages.gallery_page.clinic_team_heading') }}
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:var(--sp-4)">
            @foreach($general as $item)
            <div style="border-radius:10px;overflow:hidden;aspect-ratio:4/3;background:#f5f0e8;box-shadow:0 2px 8px rgba(11,21,32,.06)">
                <img src="{{ $item->image_url }}"
                     alt="{{ $item->title ?? $item->treatment ?? 'Soly Clinic' }}"
                     style="width:100%;height:100%;object-fit:cover" loading="lazy">
            </div>
            @endforeach
        </div>
        @endif

        @endif

        <div style="text-align:center;margin-top:var(--sp-12)">
            <p style="color:var(--text-mid);margin-bottom:var(--sp-6)">
                {{ __('messages.gallery_page.bottom_text') }}
            </p>
            <a href="{{ route('booking.index') }}" class="btn btn--primary">{{ __('messages.gallery_page.book_btn') }}</a>
        </div>

    </div>
</section>

@endsection
