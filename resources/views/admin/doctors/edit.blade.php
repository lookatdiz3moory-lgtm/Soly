@extends('layouts.admin')

@section('title', 'Edit — ' . $doctor->name)
@section('breadcrumb', 'Edit Doctor')
@section('page-title', 'Edit: ' . $doctor->name)
@section('page-sub', $doctor->specialty ?? 'Doctor profile')

@section('page-actions')
    <a href="{{ route('admin.doctors.schedule', $doctor->id) }}" class="adm-btn adm-btn--outline">🗓 Schedule</a>
    <a href="{{ route('admin.doctors.index') }}" class="adm-btn adm-btn--outline">← Back</a>
@endsection

@section('content')

<form method="POST" action="{{ route('admin.doctors.update', $doctor->id) }}" novalidate>
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <div>
        <div class="adm-card">
            <div class="adm-card__header">
                <span class="adm-card__title">Doctor Information</span>
            </div>
            <div class="adm-card__body">

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label adm-form-label--req" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="adm-form-control @error('name') adm-form-control--error @enderror"
                               value="{{ old('name', $doctor->name) }}" required maxlength="120">
                        @error('name')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="title">Qualifications / Title</label>
                        <input type="text" id="title" name="title" class="adm-form-control @error('title') adm-form-control--error @enderror"
                               value="{{ old('title', $doctor->title) }}" maxlength="80">
                        @error('title')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="specialty">Specialty</label>
                        <input type="text" id="specialty" name="specialty" class="adm-form-control @error('specialty') adm-form-control--error @enderror"
                               value="{{ old('specialty', $doctor->specialty) }}" maxlength="120">
                        @error('specialty')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="experience">Experience</label>
                        <input type="text" id="experience" name="experience" class="adm-form-control @error('experience') adm-form-control--error @enderror"
                               value="{{ old('experience', $doctor->experience) }}" maxlength="80">
                        @error('experience')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="bio">Bio / About</label>
                    <textarea id="bio" name="bio" class="adm-form-control @error('bio') adm-form-control--error @enderror"
                              rows="5" maxlength="5000">{{ old('bio', $doctor->bio) }}</textarea>
                    @error('bio')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="specialties">Areas of Expertise</label>
                        <input type="text" id="specialties" name="specialties" class="adm-form-control @error('specialties') adm-form-control--error @enderror"
                               value="{{ old('specialties', is_array($doctor->specialties) ? implode(', ', $doctor->specialties) : '') }}" maxlength="500" placeholder="Implants, Veneers, Orthodontics">
                        <span class="adm-form-hint">Comma-separated list.</span>
                        @error('specialties')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="languages">Languages</label>
                        <input type="text" id="languages" name="languages" class="adm-form-control @error('languages') adm-form-control--error @enderror"
                               value="{{ old('languages', is_array($doctor->languages) ? implode(', ', $doctor->languages) : '') }}" maxlength="200" placeholder="Arabic, English">
                        <span class="adm-form-hint">Comma-separated list.</span>
                        @error('languages')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="adm-form-grid">
                    <div class="adm-form-group">
                        <label class="adm-form-label" for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="adm-form-control @error('phone') adm-form-control--error @enderror"
                               value="{{ old('phone', $doctor->phone) }}" maxlength="25">
                        @error('phone')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="adm-form-control @error('email') adm-form-control--error @enderror"
                               value="{{ old('email', $doctor->email) }}" maxlength="150">
                        @error('email')<span class="adm-form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

            </div>
        </div>

        @if($services->isNotEmpty())
        <div class="adm-card">
            <div class="adm-card__header">
                <span class="adm-card__title">Services Offered</span>
            </div>
            <div class="adm-card__body">
                @php $assignedIds = old('services', $doctor->services->pluck('id')->toArray()); @endphp
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">
                    @foreach($services as $svc)
                    <label style="display:flex;align-items:center;gap:9px;padding:9px 12px;border:1.5px solid var(--adm-card-border);border-radius:8px;cursor:pointer;transition:border-color .15s"
                           onmouseover="this.style.borderColor='var(--adm-gold)'" onmouseout="this.style.borderColor=''">
                        <input type="checkbox" name="services[]" value="{{ $svc->id }}"
                               {{ in_array($svc->id, $assignedIds) ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:500;color:var(--adm-navy)">{{ $svc->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div>
        <div class="adm-card">
            <div class="adm-card__header">
                <span class="adm-card__title">Settings</span>
            </div>
            <div class="adm-card__body">

                <div class="adm-form-group">
                    <label class="adm-form-label" for="slot_duration">Slot Duration (minutes)</label>
                    <input type="number" id="slot_duration" name="slot_duration" class="adm-form-control @error('slot_duration') adm-form-control--error @enderror"
                           value="{{ old('slot_duration', $doctor->slot_duration ?? 30) }}" min="5" max="240">
                    @error('slot_duration')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label" for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="adm-form-control @error('sort_order') adm-form-control--error @enderror"
                           value="{{ old('sort_order', $doctor->sort_order) }}" min="0">
                    @error('sort_order')<span class="adm-form-error">{{ $message }}</span>@enderror
                </div>

                <div class="adm-form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $doctor->is_active) ? 'checked' : '' }}>
                        <span class="adm-form-label" style="margin:0">Active</span>
                    </label>
                </div>

            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px">
            <button type="submit" class="adm-btn adm-btn--gold" style="justify-content:center">
                Save Changes
            </button>
            <a href="{{ route('admin.doctors.index') }}" class="adm-btn adm-btn--outline" style="justify-content:center">
                Cancel
            </a>
            <form method="POST" action="{{ route('admin.doctors.destroy', $doctor->id) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($doctor->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="adm-btn adm-btn--danger" style="width:100%;justify-content:center">
                    Delete Doctor
                </button>
            </form>
        </div>
    </div>

</div>
</form>

@endsection
