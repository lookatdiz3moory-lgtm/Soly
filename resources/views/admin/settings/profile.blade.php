@extends('layouts.admin')

@section('title', 'My Profile')
@section('breadcrumb', 'My Profile')
@section('page-title', 'My Profile')
@section('page-sub', 'Update your admin account details and password')

@section('page-actions')
    <a href="{{ route('admin.settings.index') }}" class="adm-btn adm-btn--outline">← Settings</a>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    {{-- Profile Details --}}
    <div class="adm-card">
        <div class="adm-card__header">
            <span class="adm-card__title">Account Details</span>
        </div>
        <div class="adm-card__body">

            {{-- Avatar placeholder --}}
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--adm-card-border)">
                <div style="width:60px;height:60px;border-radius:50%;background:rgba(201,168,76,.12);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--adm-gold)">
                    {{ mb_strtoupper(mb_substr($user->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:16px;font-weight:700;color:var(--adm-navy)">{{ $user->name }}</div>
                    <div style="font-size:13px;color:var(--adm-text-muted);text-transform:capitalize">
                        {{ $user->role_label ?? $user->role ?? 'Administrator' }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.updateProfile') }}" novalidate>
            @csrf @method('PUT')

                <div class="adm-form-group">
                    <label class="adm-form-label adm-form-label--req" for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           class="adm-form-control @error('name') adm-form-control--error @enderror"
                           value="{{ old('name', $user->name) }}" required maxlength="120">
                    @error('name')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label adm-form-label--req" for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           class="adm-form-control @error('email') adm-form-control--error @enderror"
                           value="{{ old('email', $user->email) }}" required maxlength="150">
                    @error('email')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone"
                           class="adm-form-control @error('phone') adm-form-control--error @enderror"
                           value="{{ old('phone', $user->phone ?? '') }}" maxlength="25">
                    @error('phone')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="adm-btn adm-btn--gold">Save Profile</button>

            </form>

        </div>
    </div>

    {{-- Change Password --}}
    <div class="adm-card">
        <div class="adm-card__header">
            <span class="adm-card__title">Change Password</span>
        </div>
        <div class="adm-card__body">

            <form method="POST" action="{{ route('admin.settings.changePassword') }}" novalidate>
            @csrf @method('PUT')

                <div class="adm-form-group">
                    <label class="adm-form-label adm-form-label--req" for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                           class="adm-form-control @error('current_password') adm-form-control--error @enderror"
                           required autocomplete="current-password">
                    @error('current_password')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label adm-form-label--req" for="password">New Password</label>
                    <input type="password" id="password" name="password"
                           class="adm-form-control @error('password') adm-form-control--error @enderror"
                           required minlength="8" autocomplete="new-password">
                    <span class="adm-form-hint">Minimum 8 characters.</span>
                    @error('password')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label adm-form-label--req" for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="adm-form-control"
                           required autocomplete="new-password">
                </div>

                <button type="submit" class="adm-btn adm-btn--primary">Update Password</button>

            </form>

        </div>
    </div>

</div>

@endsection
