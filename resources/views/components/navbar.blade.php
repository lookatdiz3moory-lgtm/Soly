{{--
    Navbar Component
    Usage: <x-navbar />
    Sticky, transparent-on-hero, solid-on-scroll
--}}

<header class="navbar" id="navbar" role="banner">

    {{-- Top Strip --}}
    <div class="navbar__strip">
        <div class="container navbar__strip-inner">
            <div class="navbar__strip-left">
                <a href="tel:{{ config('clinic.phone', '+20 100 582 6642') }}" class="navbar__strip-link">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.95 12 19.79 19.79 0 01.88 3.37 2 2 0 012.86 1.1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L7.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                    {{ config('clinic.phone', '+20 100 582 6642') }}
                </a>
                <span class="navbar__strip-sep" aria-hidden="true">·</span>
                <span class="navbar__strip-text">Zahraa Maadi, Cairo — Open Sun–Thu 9AM–9PM</span>
            </div>
            <div class="navbar__strip-right">
                <a href="{{ config('clinic.instagram', '#') }}" target="_blank" rel="noopener" class="navbar__strip-link" aria-label="Instagram">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="2" width="20" height="20" rx="5"/>
                        <circle cx="12" cy="12" r="4"/>
                        <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
                    </svg>
                    Instagram
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp', '201000000000')) }}"
                   target="_blank" rel="noopener" class="navbar__strip-link navbar__strip-link--wa" aria-label="WhatsApp">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </a>
            </div>
        </div>
    </div>

    {{-- Main Nav --}}
    <div class="navbar__main">
        <div class="container navbar__main-inner">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="navbar__logo" aria-label="Soly Clinic — Home">
                <img src="{{ asset('images/logo.png') }}"
                     alt=""
                     class="navbar__logo-img"
                     width="84" height="56"
                     loading="eager"
                     aria-hidden="true">
                <div class="navbar__logo-text">
                    <span class="navbar__logo-name">Soly Clinic</span>
                    <span class="navbar__logo-tagline">Premium Dental Care</span>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="navbar__nav" role="navigation" aria-label="Main navigation">
                <a href="{{ route('home') }}"
                   class="navbar__link {{ request()->routeIs('home') ? 'navbar__link--active' : '' }}">
                    Home
                </a>

                {{-- Services Dropdown --}}
                <div class="navbar__dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('services') }}"
                       class="navbar__link navbar__dropdown-trigger {{ request()->routeIs('services*') ? 'navbar__link--active' : '' }}"
                       :aria-expanded="open.toString()">
                        Services
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </a>
                    <div class="navbar__dropdown-menu" x-show="open"
                         x-transition:enter="dropdown-enter"
                         x-transition:enter-start="dropdown-enter-start"
                         x-transition:enter-end="dropdown-enter-end">
                        @php
                            $navServices = [
                                ['route' => 'services.show', 'param' => 'teeth-cleaning',  'icon' => '🦷', 'label' => 'Teeth Cleaning'],
                                ['route' => 'services.show', 'param' => 'veneers',          'icon' => '✨', 'label' => 'Veneers'],
                                ['route' => 'services.show', 'param' => 'zircon-crowns',    'icon' => '💎', 'label' => 'Zircon Crowns'],
                                ['route' => 'services.show', 'param' => 'hollywood-smile',  'icon' => '😁', 'label' => 'Hollywood Smile'],
                                ['route' => 'services.show', 'param' => 'dental-implants',  'icon' => '🏆', 'label' => 'Dental Implants'],
                                ['route' => 'services.show', 'param' => 'orthodontics',     'icon' => '📐', 'label' => 'Orthodontics'],
                                ['route' => 'services.show', 'param' => 'teeth-whitening',  'icon' => '⭐', 'label' => 'Teeth Whitening'],
                                ['route' => 'services.show', 'param' => 'root-canal',       'icon' => '🔬', 'label' => 'Root Canal'],
                            ];
                        @endphp
                        @foreach($navServices as $srv)
                            <a href="{{ route($srv['route'], $srv['param']) }}" class="navbar__dropdown-item">
                                <span class="navbar__dropdown-icon" aria-hidden="true">{{ $srv['icon'] }}</span>
                                {{ $srv['label'] }}
                            </a>
                        @endforeach
                        <a href="{{ route('services') }}" class="navbar__dropdown-item navbar__dropdown-item--all">
                            View All Services →
                        </a>
                    </div>
                </div>

                <a href="{{ route('doctors') }}"
                   class="navbar__link {{ request()->routeIs('doctors*') ? 'navbar__link--active' : '' }}">
                    Doctors
                </a>
                <a href="{{ route('gallery') }}"
                   class="navbar__link {{ request()->routeIs('gallery') ? 'navbar__link--active' : '' }}">
                    Gallery
                </a>
                <a href="{{ route('contact') }}"
                   class="navbar__link {{ request()->routeIs('contact') ? 'navbar__link--active' : '' }}">
                    Contact
                </a>
            </nav>

            {{-- CTA + Hamburger --}}
            <div class="navbar__actions">
                <a href="{{ route('booking') }}" class="btn btn--book">
                    Book Appointment
                </a>
                <button class="navbar__hamburger" id="hamburger"
                        aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div class="navbar__mobile" id="mobileMenu" aria-hidden="true" role="dialog" aria-label="Mobile navigation">
        <nav class="navbar__mobile-nav">
            <a href="{{ route('home') }}" class="navbar__mobile-link">Home</a>
            <a href="{{ route('services') }}" class="navbar__mobile-link">Services</a>
            <a href="{{ route('doctors') }}" class="navbar__mobile-link">Doctors</a>
            <a href="{{ route('gallery') }}" class="navbar__mobile-link">Gallery</a>
            <a href="{{ route('contact') }}" class="navbar__mobile-link">Contact</a>
        </nav>
        <div class="navbar__mobile-cta">
            <a href="{{ route('booking') }}" class="btn btn--primary btn--full">
                Book Appointment
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp', '201000000000')) }}"
               target="_blank" rel="noopener" class="btn btn--wa btn--full">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat on WhatsApp
            </a>
        </div>
    </div>

</header>
