<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    /* ── Clinic-wide settings ───────────────────────────────── */

    public function index(): View
    {
        Gate::authorize('manageSettings');

        $settings = Schema::hasTable('settings')
            ? DB::table('settings')->orderBy('group')->orderBy('id')->get()
            : collect();

        return view()->exists('admin.settings.index')
            ? view('admin.settings.index', compact('settings'))
            : view('admin.placeholder', [
                'pageTitle' => 'Settings',
                'records'   => $settings->count() . ' configured keys',
                'settings'  => $settings,
            ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('manageSettings');

        $payload = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable'],
        ])['settings'];

        if (Schema::hasTable('settings')) {
            foreach ($payload as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => (string) $key],
                    [
                        'value'      => is_array($value) ? json_encode($value) : (string) $value,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated.');
    }

    /* ── Profile (current admin user) ───────────────────────── */

    public function profile(): View
    {
        $user = Auth::user();
        return view()->exists('admin.settings.profile')
            ? view('admin.settings.profile', compact('user'))
            : view('admin.placeholder', [
                'pageTitle' => 'My Profile',
                'records'   => $user?->email,
                'user'      => $user,
            ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
        ]);

        $user->update($data);

        return redirect()->route('admin.settings.profile')
            ->with('success', 'Profile updated.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('admin.settings.profile')
            ->with('success', 'Password changed successfully.');
    }
}
