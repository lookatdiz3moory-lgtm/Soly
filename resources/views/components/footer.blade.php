{{--
    Footer Component
    Usage: <x-footer />
--}}

<footer class="footer" role="contentinfo">

    <div class="footer__top">
        <div class="container footer__grid">

            {{-- Brand Column --}}
            <div class="footer__brand">
                <a href="{{ route('home') }}" class="footer__logo" aria-label="Soly Clinic">
                    <div class="footer__logo-mark" aria-hidden="true">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none">
                            <rect width="34" height="34" rx="10" fill="#C9A84C" fill-opacity="0.15"/>
                            <path d="M11 10h12M9 14c0 0 1.2 2.4 2.5 3.5S14.5 19 17 19s3.8-.8 5-2 2.5-3.5 2.5-3.5" stroke="#C9A84C" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M13.5 19v5a1.5 1.5 0 003 0v-3a1.5 1.5 0 013 0v3a1.5 1.5 0 003 0v-5" stroke="#C9A84C" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <span class="footer__logo-name">Soly Clinic</span>
                        <span class="footer__logo-tagline">Premium Dental Care</span>
                    </div>
                </a>
                <p class="footer__brand-desc">
                    Expert dental care in Zahraa Maadi, Cairo. Honest treatment, fair pricing, and results that last — because your smile deserves the best.
                </p>
                <div class="footer__social">
                    @if(config('clinic.instagram'))
                    <a href="{{ config('clinic.instagram') }}" target="_blank" rel="noopener"
                       class="footer__social-link" aria-label="Instagram">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="2" width="20" height="20" rx="5"/>
                            <circle cx="12" cy="12" r="4"/>
                            <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
                        </svg>
                    </a>
                    @endif
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp', '201000000000')) }}"
                       target="_blank" rel="noopener" class="footer__social-link" aria-label="WhatsApp">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    @if(config('clinic.facebook'))
                    <a href="{{ config('clinic.facebook') }}" target="_blank" rel="noopener"
                       class="footer__social-link" aria-label="Facebook">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Services Column --}}
            <div class="footer__col">
                <h3 class="footer__heading">Services</h3>
                <ul class="footer__links">
                    @foreach([
                        ['teeth-cleaning', 'Teeth Cleaning'],
                        ['veneers', 'Veneers'],
                        ['zircon-crowns', 'Zircon Crowns'],
                        ['hollywood-smile', 'Hollywood Smile'],
                        ['dental-implants', 'Dental Implants'],
                        ['orthodontics', 'Orthodontics'],
                        ['teeth-whitening', 'Teeth Whitening'],
                        ['root-canal', 'Root Canal'],
                    ] as [$slug, $label])
                    <li>
                        <a href="{{ route('services.show', $slug) }}" class="footer__link">{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Clinic Column --}}
            <div class="footer__col">
                <h3 class="footer__heading">Clinic</h3>
                <ul class="footer__links">
                    <li><a href="{{ route('home') }}" class="footer__link">Home</a></li>
                    <li><a href="{{ route('doctors') }}" class="footer__link">Our Doctors</a></li>
                    <li><a href="{{ route('gallery') }}" class="footer__link">Gallery</a></li>
                    <li><a href="{{ route('testimonials') }}" class="footer__link">Patient Reviews</a></li>
                    <li><a href="{{ route('offers') }}" class="footer__link">Special Offers</a></li>
                    <li><a href="{{ route('faq') }}" class="footer__link">FAQ</a></li>
                    <li><a href="{{ route('contact') }}" class="footer__link">Contact Us</a></li>
                    <li><a href="{{ route('booking') }}" class="footer__link">Book Appointment</a></li>
                </ul>
            </div>

            {{-- Contact Column --}}
            <div class="footer__col">
                <h3 class="footer__heading">Get In Touch</h3>
                <ul class="footer__contact">
                    <li class="footer__contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>{{ config('clinic.address', 'Zahraa Maadi, Cairo, Egypt') }}</span>
                    </li>
                    <li class="footer__contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 0112 19a19.5 19.5 0 01-7-7A2 2 0 011.1 3.85h3A2 2 0 016 5.57c.24.96.58 1.89 1 2.77a2 2 0 01-.45 2.11L5.91 11.1a16 16 0 006 6l.66-.66a2 2 0 012.11-.45c.88.42 1.81.76 2.77 1A2 2 0 0119 19h3"/>
                        </svg>
                        <a href="tel:{{ config('clinic.phone', '+20 100 582 6642') }}" class="footer__link">
                            {{ config('clinic.phone', '+20 100 582 6642') }}
                        </a>
                    </li>
                    <li class="footer__contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <a href="mailto:{{ config('clinic.email', 'info@solyclinic.com') }}" class="footer__link">
                            {{ config('clinic.email', 'info@solyclinic.com') }}
                        </a>
                    </li>
                </ul>

                <div class="footer__hours">
                    <h4 class="footer__hours-title">Working Hours</h4>
                    <div class="footer__hours-grid">
                        <span>Sun – Thu</span><span>9:00 AM – 9:00 PM</span>
                        <span>Saturday</span><span>10:00 AM – 6:00 PM</span>
                        <span>Friday</span><span style="color: #ef4444;">Closed</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Footer Bottom --}}
    <div class="footer__bottom">
        <div class="container footer__bottom-inner">
            <p class="footer__copy">
                &copy; {{ date('Y') }} Soly Clinic. All rights reserved.
            </p>
            <div class="footer__legal">
                <a href="{{ route('privacy') }}" class="footer__legal-link">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="footer__legal-link">Terms of Service</a>
                <a href="{{ route('sitemap') }}" class="footer__legal-link">Sitemap</a>
            </div>
        </div>
    </div>

</footer>
