@extends('layouts.admin')

@section('title', 'Edit — ' . $offer->title)
@section('breadcrumb', 'Edit Offer')
@section('page-title', 'Edit: ' . $offer->title)
@section('page-sub', $offer->discount_percent . '% discount offer')

@section('page-actions')
    <a href="{{ route('admin.offers.index') }}" class="adm-btn adm-btn--outline">← Back</a>
@endsection

@section('content')

<form method="POST" action="{{ route('admin.offers.update', $offer->id) }}" novalidate>
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <div>
        <div class="adm-card">
            <div class="adm-card__header"><span class="adm-card__title">Offer Details</span></div>
            <div class="adm-card__body">

                <div class="adm-form-group">
                    <label class="adm-form-label adm-form-label--req" for="title">Offer Title (English)</label>
                    <input type="text" id="title" name="title" class="adm-form-control @error('title') adm-form-control--error @enderror"
                           value="{{ old('title', $offer->title) }}" required maxlength="140">
                    @error('title')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="title_ar">Offer Title (Arabic)</label>
                    <input type="text" id="title_ar" name="title_ar" class="adm-form-control @error('title_ar') adm-form-control--error @enderror"
                           value="{{ old('title_ar', $offer->title_ar) }}" maxlength="140" dir="rtl">
                    @error('title_ar')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="service_id">Linked Service</label>
                    <select id="service_id" name="service_id" class="adm-form-control @error('service_id') adm-form-control--error @enderror">
                        <option value="">— Not linked to a specific service —</option>
                        @foreach($services as $svc)
                        <option value="{{ $svc->id }}" {{ old('service_id', $offer->service_id) == $svc->id ? 'selected' : '' }}>
                            {{ $svc->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('service_id')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="adm-form-control @error('description') adm-form-control--error @enderror"
                              maxlength="5000">{{ old('description', $offer->description) }}</textarea>
                    @error('description')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="terms">Terms & Conditions</label>
                    <textarea id="terms" name="terms" rows="3"
                              class="adm-form-control @error('terms') adm-form-control--error @enderror"
                              maxlength="2000">{{ old('terms', $offer->terms) }}</textarea>
                    @error('terms')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__header"><span class="adm-card__title">Pricing</span></div>
            <div class="adm-card__body">
                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="original_price">Original Price (EGP)</label>
                        <input type="number" id="original_price" name="original_price"
                               class="adm-form-control @error('original_price') adm-form-control--error @enderror"
                               value="{{ old('original_price', $offer->original_price) }}" required min="0" step="50">
                        @error('original_price')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="offer_price">Offer Price (EGP)</label>
                        <input type="number" id="offer_price" name="offer_price"
                               class="adm-form-control @error('offer_price') adm-form-control--error @enderror"
                               value="{{ old('offer_price', $offer->offer_price) }}" required min="0" step="50">
                        @error('offer_price')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="adm-form-group">
                    <label class="adm-form-label" for="badge_text">Badge Label</label>
                    <input type="text" id="badge_text" name="badge_text"
                           class="adm-form-control @error('badge_text') adm-form-control--error @enderror"
                           value="{{ old('badge_text', $offer->badge_text) }}" maxlength="50">
                    @error('badge_text')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="adm-card">
            <div class="adm-card__header"><span class="adm-card__title">Availability</span></div>
            <div class="adm-card__body">

                <div class="adm-form-group">
                    <label class="adm-form-label" for="starts_at">Starts At</label>
                    <input type="datetime-local" id="starts_at" name="starts_at"
                           class="adm-form-control @error('starts_at') adm-form-control--error @enderror"
                           value="{{ old('starts_at', $offer->starts_at?->format('Y-m-d\TH:i')) }}">
                    @error('starts_at')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="expires_at">Expires At</label>
                    <input type="datetime-local" id="expires_at" name="expires_at"
                           class="adm-form-control @error('expires_at') adm-form-control--error @enderror"
                           value="{{ old('expires_at', $offer->expires_at?->format('Y-m-d\TH:i')) }}">
                    <span class="adm-form-hint">Leave blank for no expiry.</span>
                    @error('expires_at')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="adm-form-control"
                           value="{{ old('sort_order', $offer->sort_order) }}" min="0">
                </div>

                <div class="adm-form-group" style="display:flex;flex-direction:column;gap:10px">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $offer->is_active) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Active</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $offer->is_featured) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Featured</span>
                    </label>
                </div>

            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px">
            <button type="submit" class="adm-btn adm-btn--gold" style="justify-content:center">Save Changes</button>
            <a href="{{ route('admin.offers.index') }}" class="adm-btn adm-btn--outline" style="justify-content:center">Cancel</a>
            <form method="POST" action="{{ route('admin.offers.destroy', $offer->id) }}"
                  onsubmit="return confirm('Delete this offer permanently?')">
                @csrf @method('DELETE')
                <button type="submit" class="adm-btn adm-btn--danger" style="width:100%;justify-content:center">Delete Offer</button>
            </form>
        </div>
    </div>

</div>
</form>

@endsection
