<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <title>@yield('title', 'Dashboard') — Soly Clinic Admin</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Outfit:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Admin assets only — no public CSS --}}
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    @stack('styles')
</head>
<body class="admin-body">

{{-- ── Sidebar overlay (mobile) ──────────────────────────── --}}
<div class="adm-sidebar-overlay" id="admOverlay" aria-hidden="true"></div>

{{-- ── Sidebar ─────────────────────────────────────────────── --}}
@include('admin.partials.sidebar')

{{-- ── Main area ───────────────────────────────────────────── --}}
<div class="adm-main" id="admMain">

    {{-- Top bar --}}
    <header class="adm-topbar" role="banner">
        <div class="adm-topbar__left">

            {{-- Mobile hamburger --}}
            <button class="adm-hamburger" id="admHamburger"
                    aria-label="Toggle sidebar" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

            {{-- Breadcrumb --}}
            <nav class="adm-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                @hasSection('breadcrumb')
                    <span class="adm-breadcrumb__sep" aria-hidden="true">/</span>
                    <span class="adm-breadcrumb__current">@yield('breadcrumb')</span>
                @endif
            </nav>
        </div>

        <div class="adm-topbar__right">

            {{-- Visit site --}}
            <a href="{{ route('home') }}" target="_blank" rel="noopener"
               class="adm-topbar-btn" title="View public website" aria-label="View website">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
            </a>

            {{-- Quick new booking --}}
            <a href="{{ route('admin.appointments.create') }}"
               class="adm-btn adm-btn--gold adm-btn--sm" aria-label="New appointment">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8"  y1="12" x2="16" y2="12"/>
                </svg>
                New Booking
            </a>

            {{-- User menu --}}
            <div class="adm-user-menu" title="{{ Auth::user()->role_label ?? '' }}">
                <div class="adm-user-menu__avatar" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="adm-user-menu__name" style="display:none">
                    {{ Str::before(Auth::user()->name ?? '', ' ') }}
                </span>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('admin.logout') }}" style="display:contents">
                @csrf
                <button type="submit"
                        class="adm-topbar-btn"
                        title="Sign out"
                        aria-label="Sign out"
                        data-confirm="Sign out of the admin panel?">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success') || session('error') || session('info'))
    <div class="adm-flash-wrap" role="alert" aria-live="assertive">
        @foreach(['success','error','info'] as $type)
            @if(session($type))
            <div class="adm-flash adm-flash--{{ $type }}">
                <span>{{ session($type) }}</span>
                <button class="adm-flash__close" onclick="this.parentElement.remove()"
                        aria-label="Dismiss">×</button>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Page content --}}
    <main class="adm-content" id="admContent" role="main">

        {{-- Page header --}}
        <div class="adm-page-header">
            <div>
                <h1 class="adm-page-title">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-sub')
                <p class="adm-page-sub">@yield('page-sub')</p>
                @endif
            </div>
            @hasSection('page-actions')
            <div class="adm-page-actions">@yield('page-actions')</div>
            @endif
        </div>

        @yield('content')
    </main>

</div>{{-- /adm-main --}}

@stack('scripts')
</body>
</html>
