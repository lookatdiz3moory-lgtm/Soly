@extends('layouts.admin')

@section('title', 'Gallery')
@section('breadcrumb', 'Gallery')
@section('page-title', 'Gallery')
@section('page-sub', $items->count() . ' ' . Str::plural('item', $items->count()) . ' in gallery')

@section('content')

{{-- Upload Form --}}
<div class="adm-card" style="margin-bottom:20px">
    <div class="adm-card__header">
        <span class="adm-card__title">Upload New Item</span>
    </div>
    <div class="adm-card__body">
        <form method="POST" action="{{ route('admin.gallery.upload') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:16px">

            <div class="adm-form-group">
                <label class="adm-form-label adm-form-label--req" for="type">Type</label>
                <select id="type" name="type" class="adm-form-control @error('type') adm-form-control--error @enderror"
                        onchange="toggleImageFields(this.value)">
                    <option value="before_after" {{ old('type') === 'before_after' ? 'selected' : '' }}>Before / After</option>
                    <option value="general"      {{ old('type') === 'general'      ? 'selected' : '' }}>General</option>
                    <option value="clinic"       {{ old('type') === 'clinic'       ? 'selected' : '' }}>Clinic</option>
                    <option value="team"         {{ old('type') === 'team'         ? 'selected' : '' }}>Team</option>
                </select>
                @error('type')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="title">Title</label>
                <input type="text" id="title" name="title" class="adm-form-control @error('title') adm-form-control--error @enderror"
                       value="{{ old('title') }}" maxlength="200" placeholder="Hollywood Smile transformation">
                @error('title')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="treatment">Treatment</label>
                <input type="text" id="treatment" name="treatment" class="adm-form-control @error('treatment') adm-form-control--error @enderror"
                       value="{{ old('treatment') }}" maxlength="120" placeholder="Veneers, Whitening…">
                @error('treatment')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>

        </div>

        {{-- Before/After images --}}
        <div id="ba-fields" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div class="adm-form-group">
                <label class="adm-form-label" for="before_image">Before Image</label>
                <input type="file" id="before_image" name="before_image" class="adm-form-control @error('before_image') adm-form-control--error @enderror"
                       accept="image/*">
                @error('before_image')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>
            <div class="adm-form-group">
                <label class="adm-form-label" for="after_image">After Image</label>
                <input type="file" id="after_image" name="after_image" class="adm-form-control @error('after_image') adm-form-control--error @enderror"
                       accept="image/*">
                @error('after_image')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Single image --}}
        <div id="single-field" style="display:none;margin-bottom:16px">
            <div class="adm-form-group">
                <label class="adm-form-label" for="image">Image</label>
                <input type="file" id="image" name="image" class="adm-form-control @error('image') adm-form-control--error @enderror"
                       accept="image/*">
                @error('image')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="hidden" name="is_active" value="1">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Featured</span>
            </label>
            <button type="submit" class="adm-btn adm-btn--gold">Upload</button>
        </div>

        </form>
    </div>
</div>

{{-- Gallery Grid --}}
@if($items->isEmpty())
<div class="adm-card">
    <div style="text-align:center;padding:64px 24px;color:var(--adm-text-muted)">
        <div style="font-size:48px;margin-bottom:16px" aria-hidden="true">🖼️</div>
        <p style="font-size:15px;margin:0;font-weight:600;color:var(--adm-navy)">No gallery items yet</p>
        <p style="font-size:13px;margin:8px 0 0">Upload your first before/after or clinic photo above.</p>
    </div>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px">
    @foreach($items as $item)
    <div class="adm-card" style="margin-bottom:0;overflow:hidden">

        {{-- Preview --}}
        <div style="aspect-ratio:4/3;background:var(--adm-content-bg);position:relative;overflow:hidden">
            @if($item->type === 'before_after')
                @if($item->before_image || $item->after_image)
                <div style="display:grid;grid-template-columns:1fr 1fr;height:100%">
                    <img src="{{ $item->before_image_url }}" alt="Before"
                         style="width:100%;height:100%;object-fit:cover">
                    <img src="{{ $item->after_image_url }}" alt="After"
                         style="width:100%;height:100%;object-fit:cover;border-left:2px solid #fff">
                </div>
                @endif
            @elseif($item->image)
                <img src="{{ $item->image_url }}" alt="{{ $item->title ?? 'Gallery item' }}"
                     style="width:100%;height:100%;object-fit:cover">
            @else
                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--adm-text-muted);font-size:32px" aria-hidden="true">🖼️</div>
            @endif

            {{-- Type badge --}}
            <span style="position:absolute;top:8px;left:8px;background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px;text-transform:uppercase;letter-spacing:.5px">
                {{ str_replace('_', '/', $item->type) }}
            </span>

            @if($item->is_featured)
            <span style="position:absolute;top:8px;right:8px;background:var(--adm-gold);color:var(--adm-navy);font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px">
                ⭐ Featured
            </span>
            @endif
        </div>

        <div style="padding:12px 14px">
            <div style="font-size:13px;font-weight:600;color:var(--adm-navy);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                {{ $item->title ?: ($item->treatment ?: 'Untitled') }}
            </div>
            <div style="font-size:11.5px;color:var(--adm-text-muted)">
                {{ $item->service?->name ?? '' }}
                @if($item->service && $item->treatment) · @endif
                {{ $item->treatment ?? '' }}
            </div>

            <div style="display:flex;align-items:center;gap:6px;margin-top:10px">
                <form method="POST" action="{{ route('admin.gallery.toggle', $item->id) }}" style="flex:1">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="adm-btn adm-btn--sm {{ $item->is_active ? 'adm-btn--outline' : 'adm-btn--primary' }}"
                            style="width:100%;justify-content:center">
                        {{ $item->is_active ? 'Hide' : 'Show' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.gallery.destroy', $item->id) }}"
                      onsubmit="return confirm('Delete this gallery item?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm adm-btn--icon" aria-label="Delete">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </div>
    @endforeach
</div>
@endif

@push('scripts')
<script>
function toggleImageFields(type) {
    const ba     = document.getElementById('ba-fields');
    const single = document.getElementById('single-field');
    if (type === 'before_after') {
        ba.style.display     = 'grid';
        single.style.display = 'none';
    } else {
        ba.style.display     = 'none';
        single.style.display = 'block';
    }
}
toggleImageFields('before_after');
</script>
@endpush

@endsection
