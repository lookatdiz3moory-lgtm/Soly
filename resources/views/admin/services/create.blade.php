@extends('layouts.admin')

@section('title', 'Add Service')
@section('breadcrumb', 'Add Service')
@section('page-title', 'Add Service')
@section('page-sub', 'Create a new dental or cosmetic service')

@section('page-actions')
    <a href="{{ route('admin.services.index') }}" class="adm-btn adm-btn--outline">← Back to Services</a>
@endsection

@section('content')

<form method="POST" action="{{ route('admin.services.store') }}" novalidate>
@csrf

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <div>
        <div class="adm-card">
            <div class="adm-card__header"><span class="adm-card__title">Service Details</span></div>
            <div class="adm-card__body">

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="name">Service Name (English)</label>
                        <input type="text" id="name" name="name" class="adm-form-control @error('name') adm-form-control--error @enderror"
                               value="{{ old('name') }}" required maxlength="120" placeholder="Dental Implants">
                        @error('name')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="name_ar">Service Name (Arabic)</label>
                        <input type="text" id="name_ar" name="name_ar" class="adm-form-control @error('name_ar') adm-form-control--error @enderror"
                               value="{{ old('name_ar') }}" maxlength="120" dir="rtl">
                        @error('name_ar')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="category">Category</label>
                        <input type="text" id="category" name="category" class="adm-form-control @error('category') adm-form-control--error @enderror"
                               value="{{ old('category') }}" maxlength="80" placeholder="Restorative, Cosmetic, Orthodontics…">
                        @error('category')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" class="adm-form-control @error('slug') adm-form-control--error @enderror"
                               value="{{ old('slug') }}" maxlength="140" placeholder="auto-generated if empty">
                        <span class="adm-form-hint">Leave blank to auto-generate from name.</span>
                        @error('slug')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="short_description">Short Description</label>
                    <input type="text" id="short_description" name="short_description"
                           class="adm-form-control @error('short_description') adm-form-control--error @enderror"
                           value="{{ old('short_description') }}" maxlength="300"
                           placeholder="One-line summary shown on cards and listing pages">
                    @error('short_description')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="description">Full Description</label>
                    <textarea id="description" name="description" rows="6"
                              class="adm-form-control @error('description') adm-form-control--error @enderror"
                              maxlength="8000"
                              placeholder="Detailed description shown on the service detail page…">{{ old('description') }}</textarea>
                    @error('description')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__header"><span class="adm-card__title">Pricing & Duration</span></div>
            <div class="adm-card__body">
                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="price_from">Price From (EGP)</label>
                        <input type="number" id="price_from" name="price_from" class="adm-form-control @error('price_from') adm-form-control--error @enderror"
                               value="{{ old('price_from') }}" min="0" step="50" placeholder="1500">
                        @error('price_from')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="price_to">Price To (EGP)</label>
                        <input type="number" id="price_to" name="price_to" class="adm-form-control @error('price_to') adm-form-control--error @enderror"
                               value="{{ old('price_to') }}" min="0" step="50" placeholder="3000">
                        @error('price_to')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="duration_minutes">Duration (minutes)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes"
                           class="adm-form-control @error('duration_minutes') adm-form-control--error @enderror"
                           value="{{ old('duration_minutes') }}" min="0" max="1440" placeholder="60">
                    @error('duration_minutes')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="adm-card">
            <div class="adm-card__header"><span class="adm-card__title">Settings</span></div>
            <div class="adm-card__body">

                <div class="adm-form-group">
                    <label class="adm-form-label" for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="adm-form-control"
                           value="{{ old('sort_order', 0) }}" min="0">
                    <span class="adm-form-hint">Lower = appears first.</span>
                </div>

                <div class="adm-form-group" style="display:flex;flex-direction:column;gap:10px">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Active</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_bookable" value="0">
                        <input type="checkbox" name="is_bookable" value="1" {{ old('is_bookable', true) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Bookable online</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Featured on homepage</span>
                    </label>
                </div>

            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px">
            <button type="submit" class="adm-btn adm-btn--gold" style="justify-content:center">Save Service</button>
            <a href="{{ route('admin.services.index') }}" class="adm-btn adm-btn--outline" style="justify-content:center">Cancel</a>
        </div>
    </div>

</div>
</form>

@endsection
