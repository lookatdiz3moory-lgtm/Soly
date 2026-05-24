{{--
    Admin Sidebar Partial
    Included by layouts/admin.blade.php
    Active link detection is handled by admin.js
--}}

@php
    use App\Models\Appointment;

    // Badge counts — wrapped in try/catch so seeded-empty DB never breaks the layout
    try {
        $pendingCount  = Appointment::pending()->count();
        $todayCount    = Appointment::today()->whereIn('status', Appointment::ACTIVE_STATUSES)->count();
    } catch (\Throwable) {
        $pendingCount  = 0;
        $todayCount    = 0;
    }
@endphp

<aside class="adm-sidebar" id="admSidebar" role="navigation" aria-label="Admin sidebar">

    {{-- Logo --}}
    <div class="adm-sidebar__logo">
        <img src="{{ asset('images/logo.png') }}"
             alt="Soly Clinic"
             class="adm-sidebar__logo-img"
             width="60" height="40"
             loading="eager">
        <div>
            <span class="adm-sidebar__logo-name">Soly Clinic</span>
            <span class="adm-sidebar__logo-tag">Admin Panel</span>
        </div>
    </div>

    {{-- Signed-in user --}}
    <div class="adm-sidebar__user">
        <div class="adm-sidebar__avatar" aria-hidden="true">
            {{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'A', 0, 1)) }}
        </div>
        <div>
            <div class="adm-sidebar__user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
            <div class="adm-sidebar__user-role">{{ Auth::user()->role_label ?? 'Staff' }}</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="adm-sidebar__nav">

        {{-- Overview --}}
        <div class="adm-sidebar__section">Overview</div>

        <a href="{{ route('admin.dashboard') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        {{-- Appointments --}}
        <div class="adm-sidebar__section">Appointments</div>

        <a href="{{ route('admin.appointments.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8"  y1="2" x2="8"  y2="6"/>
                <line x1="3"  y1="10" x2="21" y2="10"/>
            </svg>
            All Appointments
            @if($pendingCount > 0)
                <span class="adm-nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.appointments.index', ['status' => 'pending']) }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            Pending
            @if($pendingCount > 0)
                <span class="adm-nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.appointments.index', ['date' => today()->toDateString()]) }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            Today
            @if($todayCount > 0)
                <span class="adm-nav-badge" style="background:var(--adm-blue);color:#fff">
                    {{ $todayCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('admin.appointments.calendar') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8"  y1="2" x2="8"  y2="6"/>
                <line x1="3"  y1="10" x2="21" y2="10"/>
                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>
            </svg>
            Calendar
        </a>

        <a href="{{ route('admin.appointments.create') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8"  y1="12" x2="16" y2="12"/>
            </svg>
            New Appointment
        </a>

        {{-- Clinic Management --}}
        <div class="adm-sidebar__section">Clinic</div>

        <a href="{{ route('admin.doctors.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            Doctors
        </a>

        <a href="{{ route('admin.services.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            Services
        </a>

        <a href="{{ route('admin.offers.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            Offers
        </a>

        {{-- Content --}}
        <div class="adm-sidebar__section">Content</div>

        <a href="{{ route('admin.gallery.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            Gallery
        </a>

        <a href="{{ route('admin.testimonials.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
            Testimonials
        </a>

        <a href="{{ route('admin.faqs.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            FAQs
        </a>

        {{-- System --}}
        <div class="adm-sidebar__divider"></div>

        <a href="{{ route('admin.settings.index') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            Settings
        </a>

        <a href="{{ route('admin.settings.profile') }}" class="adm-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            My Profile
        </a>

    </nav>{{-- /adm-sidebar__nav --}}

    {{-- Footer --}}
    <div class="adm-sidebar__footer">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="adm-nav-item" style="width:100%;text-align:left">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>

</aside>
