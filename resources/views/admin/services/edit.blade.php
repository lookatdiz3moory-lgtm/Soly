@extends('layouts.admin')

@section('title', 'Edit — ' . $service->name)
@section('breadcrumb', 'Edit Service')
@section('page-title', 'Edit: ' . $service->name)
@section('page-sub', $service->category ?? 'Service')

@section('page-actions')
    <a href="{{ route('admin.services.index') }}" class="adm-btn adm-btn--outline">← Back</a>
@endsection

@section('content')

<form method="POST" action="{{ route('admin.services.update', $service->id) }}" enctype="multipart/form-data" novalidate>
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <div>
        <div class="adm-card">
            <div class="adm-card__header"><span class="adm-card__title">Service Details</span></div>
            <div class="adm-card__body">

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="name">Service Name (English)</label>
                        <input type="text" id="name" name="name" class="adm-form-control @error('name') adm-form-control--error @enderror"
                               value="{{ old('name', $service->name) }}" required maxlength="120">
                        @error('name')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="name_ar">Service Name (Arabic)</label>
                        <input type="text" id="name_ar" name="name_ar" class="adm-form-control @error('name_ar') adm-form-control--error @enderror"
                               value="{{ old('name_ar', $service->name_ar) }}" maxlength="120" dir="rtl">
                        @error('name_ar')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="category">Category</label>
                        <input type="text" id="category" name="category" class="adm-form-control @error('category') adm-form-control--error @enderror"
                               value="{{ old('category', $service->category) }}" maxlength="80">
                        @error('category')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" class="adm-form-control @error('slug') adm-form-control--error @enderror"
                               value="{{ old('slug', $service->slug) }}" maxlength="140">
                        @error('slug')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="short_description">Short Description</label>
                    <input type="text" id="short_description" name="short_description"
                           class="adm-form-control @error('short_description') adm-form-control--error @enderror"
                           value="{{ old('short_description', $service->short_description) }}" maxlength="300">
                    @error('short_description')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="description">Full Description</label>
                    <textarea id="description" name="description" rows="6"
                              class="adm-form-control @error('description') adm-form-control--error @enderror"
                              maxlength="8000">{{ old('description', $service->description) }}</textarea>
                    @error('description')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="image">Cover Image</label>
                    <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:8px">
                        <img id="imagePreview" src="{{ $service->image_url }}" alt="Current image"
                             style="width:160px;height:110px;object-fit:cover;border-radius:8px;border:1px solid var(--adm-card-border)">
                        <div style="flex:1">
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                                   class="adm-form-control @error('image') adm-form-control--error @enderror">
                            <span class="adm-form-hint">Upload a new image to replace the current cover. Old image is deleted automatically. JPG/PNG/WEBP, ≤ 2 MB.</span>
                            @error('image')<span class="adm-form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
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
                               value="{{ old('price_from', $service->price_from) }}" min="0" step="50">
                        @error('price_from')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="price_to">Price To (EGP)</label>
                        <input type="number" id="price_to" name="price_to" class="adm-form-control @error('price_to') adm-form-control--error @enderror"
                               value="{{ old('price_to', $service->price_to) }}" min="0" step="50">
                        @error('price_to')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="duration_minutes">Duration (minutes)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes"
                           class="adm-form-control @error('duration_minutes') adm-form-control--error @enderror"
                           value="{{ old('duration_minutes', $service->duration_minutes) }}" min="0" max="1440">
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
                           value="{{ old('sort_order', $service->sort_order) }}" min="0">
                </div>

                <div class="adm-form-group" style="display:flex;flex-direction:column;gap:10px">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Active</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_bookable" value="0">
                        <input type="checkbox" name="is_bookable" value="1" {{ old('is_bookable', $service->is_bookable) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Bookable online</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Featured on homepage</span>
                    </label>
                </div>

            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px">
            <button type="submit" class="adm-btn adm-btn--gold" style="justify-content:center">Save Changes</button>
            <a href="{{ route('admin.services.index') }}" class="adm-btn adm-btn--outline" style="justify-content:center">Cancel</a>
            <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}"
                  onsubmit="return confirm('Delete \'{{ addslashes($service->name) }}\'? This will affect bookings linked to this service.')">
                @csrf @method('DELETE')
                <button type="submit" class="adm-btn adm-btn--danger" style="width:100%;justify-content:center">Delete Service</button>
            </form>
        </div>
    </div>

</div>
</form>

@push('scripts')
<script>
    document.getElementById('image')?.addEventListener('change', function (e) {
        const file = e.target.files?.[0];
        const preview = document.getElementById('imagePreview');
        if (!file || !preview) return;
        preview.src = URL.createObjectURL(file);
    });
</script>
@endpush

@endsection
