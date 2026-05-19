<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Sign In — Soly Clinic</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Outfit:wght@400;500;600&family=DM+Mono&display=swap" rel="stylesheet">

    @vite('resources/css/admin.css')

    <style>
        /* Login-only overrides — keeps admin.css clean */
        html, body {
            height: 100%;
            margin: 0;
            background: #0B1520;
            font-family: 'Outfit', sans-serif;
        }
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(ellipse 60% 60% at 80% 20%, rgba(201,168,76,.055) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 10% 80%, rgba(44,74,99,.35) 0%, transparent 60%),
                #0B1520;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,.025);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 20px;
            padding: 44px 42px 40px;
            box-shadow: 0 24px 64px rgba(0,0,0,.5);
            backdrop-filter: blur(12px);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 36px;
        }
        .login-logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px; height: 56px;
            background: rgba(201,168,76,.12);
            border-radius: 14px;
            font-size: 26px;
            margin-bottom: 14px;
        }
        .login-logo-name {
            display: block;
            font-family: 'Playfair Display', serif;
            font-size: 1.375rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 3px;
        }
        .login-logo-tag {
            display: block;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #C9A84C;
        }
        .login-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #fff;
            text-align: center;
            margin-bottom: 6px;
        }
        .login-sub {
            text-align: center;
            font-size: 13.5px;
            color: rgba(255,255,255,.40);
            margin-bottom: 32px;
        }
        .login-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: rgba(255,255,255,.40);
            margin-bottom: 7px;
        }
        .login-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,.055);
            border: 1.5px solid rgba(255,255,255,.10);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-family: 'Outfit', sans-serif;
            transition: border-color .2s, box-shadow .2s;
            box-sizing: border-box;
        }
        .login-input:focus {
            outline: none;
            border-color: #C9A84C;
            box-shadow: 0 0 0 3px rgba(201,168,76,.15);
        }
        .login-input::placeholder { color: rgba(255,255,255,.22); }
        .login-input.is-error     { border-color: #EF4444; }
        .login-error {
            font-size: 12.5px;
            color: #FCA5A5;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .login-group { margin-bottom: 20px; }
        .login-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .login-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(255,255,255,.42);
            cursor: pointer;
        }
        .login-remember input { accent-color: #C9A84C; cursor: pointer; }
        .login-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #C9A84C, #A8892C);
            color: #0D1B2A;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: filter .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 6px 20px rgba(201,168,76,.30);
        }
        .login-btn:hover   { filter: brightness(1.06); transform: translateY(-1px); box-shadow: 0 10px 28px rgba(201,168,76,.40); }
        .login-btn:active  { transform: translateY(0); }
        .login-btn:disabled{ opacity: .7; cursor: not-allowed; }
        .login-footer {
            text-align: center;
            margin-top: 26px;
            padding-top: 22px;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .login-footer a {
            font-size: 12.5px;
            color: rgba(255,255,255,.30);
            text-decoration: none;
            transition: color .15s;
        }
        .login-footer a:hover { color: #C9A84C; }
        .login-secure {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: rgba(255,255,255,.18);
            margin-top: 16px;
        }
    </style>
</head>
<body>

<div class="login-page" role="main">
    <div class="login-card">

        {{-- Logo --}}
        <div class="login-logo">
            <div class="login-logo-icon" aria-hidden="true">🦷</div>
            <span class="login-logo-name">Soly Clinic</span>
            <span class="login-logo-tag">Staff Portal</span>
        </div>

        <h1 class="login-title">Welcome Back</h1>
        <p class="login-sub">Sign in to your admin account</p>

        {{-- Session flash --}}
        @if(session('success'))
            <div style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);border-radius:8px;padding:11px 14px;font-size:13.5px;color:#86efac;margin-bottom:22px">
                {{ session('success') }}
            </div>
        @endif

        {{-- Login form --}}
        <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm" novalidate>
            @csrf

            <div class="login-group">
                <label class="login-label" for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="login-input {{ $errors->has('email') ? 'is-error' : '' }}"
                    value="{{ old('email') }}"
                    placeholder="admin@solyclinic.com"
                    autocomplete="email"
                    autofocus
                    required
                >
                @error('email')
                    <div class="login-error">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="login-group">
                <label class="login-label" for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="login-input {{ $errors->has('password') ? 'is-error' : '' }}"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >
                @error('password')
                    <div class="login-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-row">
                <label class="login-remember">
                    <input type="checkbox" name="remember" value="1"
                           {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Sign In
            </button>
        </form>

        <div class="login-footer">
            <a href="{{ route('home') }}">← Back to website</a>
        </div>

        <div class="login-secure" aria-hidden="true">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Secured connection · Staff only
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<span>Signing in…</span>';
});
</script>

</body>
</html>
