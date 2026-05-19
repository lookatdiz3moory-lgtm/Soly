<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * AdminAuthController
 *
 * Handles staff authentication (superadmin / admin / secretary / doctor).
 * Uses Laravel's built-in Auth guard — no custom session handling needed.
 *
 * Routes (defined in routes/web.php):
 *   GET  /admin/login   → showLogin()
 *   POST /admin/login   → login()
 *   POST /admin/logout  → logout()
 */
class AdminAuthController extends Controller
{
    /* ── Show Login Page ────────────────────────────────────── */

    public function showLogin(Request $request): View|RedirectResponse
    {
        // Already authenticated staff → go to dashboard
        if (Auth::check() && Auth::user()->isStaff()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-login');
    }

    /* ── Process Login ──────────────────────────────────────── */

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate limiting: 5 attempts per minute per IP+email combo
        $this->checkRateLimit($request);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey($request), 60);

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // Clear rate limiter on success
        RateLimiter::clear($this->throttleKey($request));

        /** @var User $user */
        $user = Auth::user();

        // Block non-staff from admin panel
        if (!$user->isStaff()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'You do not have permission to access the admin panel.',
            ]);
        }

        // Block deactivated accounts
        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact the clinic administrator.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /* ── Logout ─────────────────────────────────────────────── */

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been signed out successfully.');
    }

    /* ── Rate Limiting ──────────────────────────────────────── */

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('email', '')) . '|' . $request->ip()
        );
    }

    private function checkRateLimit(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => __('Too many login attempts. Please try again in :seconds seconds.', [
                    'seconds' => $seconds,
                ]),
            ]);
        }
    }
}
