{{--
    Language Switcher
    Usage: <x-language-switcher />  or  <x-language-switcher variant="mobile" />

    Renders EN / AR pills that POST to /lang/{locale}. The current locale
    is highlighted. Keeps the page on the same URL via Referer-based redirect.
--}}

@props(['variant' => 'desktop'])

@php
    $supported = config('app.supported_locales', ['en', 'ar']);
    $current   = app()->getLocale();
@endphp

<div class="lang-switcher lang-switcher--{{ $variant }}" role="group" aria-label="Language">
    @foreach($supported as $code)
        <a href="{{ route('locale.switch', $code) }}"
           class="lang-switcher__btn {{ $code === $current ? 'lang-switcher__btn--active' : '' }}"
           hreflang="{{ $code }}"
           rel="alternate"
           aria-current="{{ $code === $current ? 'true' : 'false' }}"
           aria-label="Switch to {{ trans('messages.lang_name', [], $code) }}">
            {{ trans('messages.lang_short', [], $code) }}
        </a>
    @endforeach
</div>
