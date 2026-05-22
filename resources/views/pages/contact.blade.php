@extends('layouts.app')

@section('title', 'Contact Us — Soly Clinic')
@section('meta_description', 'Get in touch with Soly Clinic in Zahraa Maadi, Cairo. Call, WhatsApp, or send us a message — we always reply promptly.')

@section('content')

<div class="page-hero" aria-label="Contact">
    <div class="container" style="position:relative;z-index:1">
        <div class="page-hero__tag section-tag" style="margin-bottom:var(--sp-4)">Get In Touch</div>
        <h1 class="page-hero__title">Contact Us</h1>
        <p class="page-hero__sub">
            Questions, concerns, or just want to say hello — we are always here for you.
        </p>
    </div>
</div>

<section style="padding:var(--sp-16) 0">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 380px;gap:var(--sp-12);align-items:start">

            {{-- Contact form --}}
            <div>
                <h2 style="font-size:1.4rem;font-weight:700;color:var(--navy);margin-bottom:var(--sp-8)">Send Us a Message</h2>

                @if(session('success'))
                <div role="alert" style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:10px;padding:var(--sp-5) var(--sp-6);margin-bottom:var(--sp-8);color:#15803d;font-size:.95rem;font-weight:600">
                    ✓ {{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" novalidate>
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-5);margin-bottom:var(--sp-5)">
                    <div>
                        <label for="name" style="display:block;font-size:.85rem;font-weight:600;color:var(--navy);margin-bottom:var(--sp-2)">
                            Full Name <span style="color:var(--gold-dark)">*</span>
                        </label>
                        <input type="text" id="name" name="name" required maxlength="120"
                               value="{{ old('name') }}"
                               style="width:100%;padding:var(--sp-3) var(--sp-4);border:1px solid {{ $errors->has('name') ? 'red' : 'var(--border)' }};border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box">
                        @error('name')
                        <span style="font-size:.8rem;color:red;margin-top:4px;display:block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" style="display:block;font-size:.85rem;font-weight:600;color:var(--navy);margin-bottom:var(--sp-2)">
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone" maxlength="25"
                               value="{{ old('phone') }}"
                               placeholder="+20 100 000 0000"
                               style="width:100%;padding:var(--sp-3) var(--sp-4);border:1px solid var(--border);border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box">
                        @error('phone')
                        <span style="font-size:.8rem;color:red;margin-top:4px;display:block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="margin-bottom:var(--sp-5)">
                    <label for="email" style="display:block;font-size:.85rem;font-weight:600;color:var(--navy);margin-bottom:var(--sp-2)">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" maxlength="150"
                           value="{{ old('email') }}"
                           placeholder="you@example.com"
                           style="width:100%;padding:var(--sp-3) var(--sp-4);border:1px solid {{ $errors->has('email') ? 'red' : 'var(--border)' }};border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box">
                    @error('email')
                    <span style="font-size:.8rem;color:red;margin-top:4px;display:block">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:var(--sp-5)">
                    <label for="subject" style="display:block;font-size:.85rem;font-weight:600;color:var(--navy);margin-bottom:var(--sp-2)">
                        Subject
                    </label>
                    <input type="text" id="subject" name="subject" maxlength="200"
                           value="{{ old('subject') }}"
                           placeholder="e.g. Question about dental implants"
                           style="width:100%;padding:var(--sp-3) var(--sp-4);border:1px solid var(--border);border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box">
                </div>

                <div style="margin-bottom:var(--sp-7)">
                    <label for="message" style="display:block;font-size:.85rem;font-weight:600;color:var(--navy);margin-bottom:var(--sp-2)">
                        Message <span style="color:var(--gold-dark)">*</span>
                    </label>
                    <textarea id="message" name="message" required rows="5" maxlength="2000"
                              placeholder="Tell us how we can help…"
                              style="width:100%;padding:var(--sp-3) var(--sp-4);border:1px solid {{ $errors->has('message') ? 'red' : 'var(--border)' }};border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;resize:vertical;transition:border-color .2s;box-sizing:border-box">{{ old('message') }}</textarea>
                    @error('message')
                    <span style="font-size:.8rem;color:red;margin-top:4px;display:block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn--primary">Send Message</button>

                </form>
            </div>

            {{-- Contact info --}}
            <div style="position:sticky;top:calc(var(--nav-h, 72px) + var(--sp-6))">

                <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 4px 24px rgba(11,21,32,.07);margin-bottom:var(--sp-5)">
                    <div style="background:var(--navy);padding:var(--sp-5) var(--sp-6)">
                        <div style="font-size:1rem;font-weight:700;color:#fff">{{ config('clinic.name', 'Soly Clinic') }}</div>
                        <div style="font-size:.8rem;color:rgba(255,255,255,.6);margin-top:4px">Zahraa Maadi, Cairo</div>
                    </div>

                    <div style="padding:var(--sp-5) var(--sp-6);display:grid;gap:var(--sp-4)">

                        <a href="tel:{{ config('clinic.phone', '+201005826642') }}"
                           style="display:flex;align-items:center;gap:var(--sp-3);text-decoration:none;color:inherit">
                            <span style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,.12);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0" aria-hidden="true">📞</span>
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light)">Phone</div>
                                <div style="font-size:.9rem;font-weight:600;color:var(--navy)">{{ config('clinic.phone', '+20 100 582 6642') }}</div>
                            </div>
                        </a>

                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('clinic.whatsapp','201000000000')) }}"
                           target="_blank" rel="noopener"
                           style="display:flex;align-items:center;gap:var(--sp-3);text-decoration:none;color:inherit">
                            <span style="width:36px;height:36px;border-radius:50%;background:rgba(37,211,102,.12);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0" aria-hidden="true">💬</span>
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light)">WhatsApp</div>
                                <div style="font-size:.9rem;font-weight:600;color:var(--navy)">Message Us</div>
                            </div>
                        </a>

                        @if(config('clinic.email'))
                        <a href="mailto:{{ config('clinic.email') }}"
                           style="display:flex;align-items:center;gap:var(--sp-3);text-decoration:none;color:inherit">
                            <span style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,.12);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0" aria-hidden="true">✉️</span>
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light)">Email</div>
                                <div style="font-size:.9rem;font-weight:600;color:var(--navy)">{{ config('clinic.email') }}</div>
                            </div>
                        </a>
                        @endif

                        <div style="display:flex;align-items:flex-start;gap:var(--sp-3)">
                            <span style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,.12);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0" aria-hidden="true">📍</span>
                            <div>
                                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-light)">Address</div>
                                <div style="font-size:.875rem;color:var(--navy);line-height:1.5">{{ config('clinic.address', 'Zahraa Maadi, Cairo, Egypt') }}</div>
                            </div>
                        </div>

                    </div>
                </div>

                <a href="{{ route('booking.index') }}"
                   class="btn btn--primary" style="width:100%;text-align:center;display:block">
                    Book an Appointment
                </a>

            </div>

        </div>
    </div>
</section>

@endsection
