@extends('layouts.app')

@section('title', 'FAQ — Soly Clinic')
@section('meta_description', 'Frequently asked questions about dental treatments, booking, pricing, and what to expect at Soly Clinic in Zahraa Maadi, Cairo.')

@section('content')

<div class="page-hero" aria-label="{{ __('messages.faq_page.page_heading') }}">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">{{ __('messages.faq_page.page_tag') }}</div>
        <h1 class="page-hero__title">{{ __('messages.faq_page.page_heading') }}</h1>
        <p class="page-hero__sub">{{ __('messages.faq_page.page_sub') }}</p>
    </div>
</div>

<section style="padding:var(--sp-16) 0" aria-label="{{ __('messages.faq_page.page_heading') }}">
    <div class="container" style="max-width:760px">

        @if($faqs->isEmpty())
        <div style="text-align:center;padding:var(--sp-16) 0;color:var(--text-light)">
            <div style="font-size:56px;margin-bottom:var(--sp-6)" aria-hidden="true">❓</div>
            <h2 style="color:var(--navy);margin-bottom:var(--sp-4)">{{ __('messages.faq_page.empty_heading') }}</h2>
            <p style="margin-bottom:var(--sp-8)">{{ __('messages.faq_page.empty_sub') }}</p>
            <a href="{{ route('contact.index') }}" class="btn btn--primary">{{ __('messages.buttons.contact_us') }}</a>
        </div>
        @else

        @php $grouped = $faqs->groupBy('category'); @endphp

        @foreach($grouped as $category => $group)

        @if($category)
        <h2 style="font-size:1.05rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.8px;margin:var(--sp-12) 0 var(--sp-6)">
            {{ $category }}
        </h2>
        @endif

        <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:var(--sp-6)">
            @foreach($group as $i => $faq)
            <div class="faq-item" style="{{ !$loop->first ? 'border-top:1px solid var(--border)' : '' }}">
                <button type="button"
                        onclick="toggleFaq(this)"
                        aria-expanded="false"
                        style="width:100%;text-align:start;background:none;border:none;padding:var(--sp-5) var(--sp-6);display:flex;align-items:flex-start;justify-content:space-between;gap:var(--sp-4);cursor:pointer;font-family:inherit">
                    <span style="font-size:.975rem;font-weight:600;color:var(--navy);line-height:1.5">{{ $faq->localized_question }}</span>
                    <span class="faq-icon" style="flex-shrink:0;font-size:1.2rem;color:var(--gold-dark);transition:transform .25s;margin-top:2px" aria-hidden="true">+</span>
                </button>
                <div class="faq-answer" style="display:none;padding:0 var(--sp-6) var(--sp-5)">
                    <div style="font-size:.9rem;line-height:1.75;color:var(--text-mid)">
                        {!! nl2br(e($faq->localized_answer)) !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @endforeach

        @endif

        <div style="text-align:center;margin-top:var(--sp-10);padding:var(--sp-8);border:1px solid var(--border);border-radius:12px;background:rgba(201,168,76,.04)">
            <p style="font-size:.95rem;color:var(--text-mid);margin-bottom:var(--sp-5)">
                {{ __('messages.faq_page.still_have') }}
            </p>
            <a href="{{ route('contact.index') }}" class="btn btn--primary" style="margin-inline-end:var(--sp-3)">{{ __('messages.faq_page.contact_btn') }}</a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
               target="_blank" rel="noopener" class="btn btn--outline">
                {{ __('messages.faq_page.ask_wa_btn') }}
            </a>
        </div>

    </div>
</section>

@push('scripts')
<script>
function toggleFaq(btn) {
    const answer  = btn.nextElementSibling;
    const icon    = btn.querySelector('.faq-icon');
    const isOpen  = btn.getAttribute('aria-expanded') === 'true';
    btn.setAttribute('aria-expanded', String(!isOpen));
    answer.style.display = isOpen ? 'none' : 'block';
    icon.style.transform  = isOpen ? '' : 'rotate(45deg)';
}
</script>
@endpush

@endsection
