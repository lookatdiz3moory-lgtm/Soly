@extends('layouts.admin')

@section('title', 'Testimonials')
@section('breadcrumb', 'Testimonials')
@section('page-title', 'Testimonials')
@section('page-sub', $items->count() . ' total · ' . $items->where('is_approved', false)->count() . ' pending approval')

@section('content')

{{-- Add New --}}
<div class="adm-card" style="margin-bottom:20px">
    <div class="adm-card__header">
        <span class="adm-card__title">Add Testimonial</span>
        <span style="font-size:12px;color:var(--adm-text-muted)">Manually enter a review from WhatsApp, Google, or elsewhere</span>
    </div>
    <div class="adm-card__body">
        <form method="POST" action="{{ route('admin.testimonials.store') }}" novalidate>
        @csrf

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:14px">

            <div class="adm-form-group" style="margin-bottom:0">
                <label class="adm-form-label adm-form-label--req" for="name">Patient Name</label>
                <input type="text" id="name" name="name" class="adm-form-control @error('name') adm-form-control--error @enderror"
                       value="{{ old('name') }}" required maxlength="120" placeholder="Ahmed Hassan">
                @error('name')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-form-group" style="margin-bottom:0">
                <label class="adm-form-label adm-form-label--req" for="rating">Rating</label>
                <select id="rating" name="rating" class="adm-form-control @error('rating') adm-form-control--error @enderror">
                    @foreach([5,4,3,2,1] as $r)
                    <option value="{{ $r }}" {{ old('rating', 5) == $r ? 'selected' : '' }}>
                        {{ str_repeat('★', $r) . str_repeat('☆', 5-$r) }} ({{ $r }})
                    </option>
                    @endforeach
                </select>
                @error('rating')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-form-group" style="margin-bottom:0">
                <label class="adm-form-label" for="service">Service Received</label>
                <input type="text" id="service" name="service" class="adm-form-control @error('service') adm-form-control--error @enderror"
                       value="{{ old('service') }}" maxlength="120" placeholder="Dental Implant">
                @error('service')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-form-group" style="margin-bottom:0">
                <label class="adm-form-label" for="source">Source</label>
                <select id="source" name="source" class="adm-form-control @error('source') adm-form-control--error @enderror">
                    <option value="internal" {{ old('source') === 'internal'  ? 'selected' : '' }}>Direct / Internal</option>
                    <option value="google"   {{ old('source') === 'google'    ? 'selected' : '' }}>Google</option>
                    <option value="facebook" {{ old('source') === 'facebook'  ? 'selected' : '' }}>Facebook</option>
                    <option value="whatsapp" {{ old('source') === 'whatsapp'  ? 'selected' : '' }}>WhatsApp</option>
                </select>
                @error('source')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>

        </div>

        <div class="adm-form-group" style="margin-bottom:14px">
            <label class="adm-form-label adm-form-label--req" for="review">Review Text</label>
            <textarea id="review" name="review" rows="3" class="adm-form-control @error('review') adm-form-control--error @enderror"
                      required maxlength="2000" placeholder="Patient's review…">{{ old('review') }}</textarea>
            @error('review')<span class="adm-form-error">{{ $message }}</span>@enderror
        </div>

        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="hidden" name="is_approved" value="0">
                <input type="checkbox" name="is_approved" value="1" checked>
                <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Approve immediately</span>
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1">
                <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Featured</span>
            </label>
            <button type="submit" class="adm-btn adm-btn--gold">Add Testimonial</button>
        </div>

        </form>
    </div>
</div>

{{-- List --}}
<div class="adm-card">

    @if($items->isEmpty())
    <div style="text-align:center;padding:64px 24px;color:var(--adm-text-muted)">
        <div style="font-size:48px;margin-bottom:16px" aria-hidden="true">⭐</div>
        <p style="font-size:15px;margin:0;font-weight:600;color:var(--adm-navy)">No testimonials yet</p>
        <p style="font-size:13px;margin:8px 0 0">Add reviews from happy patients using the form above.</p>
    </div>
    @else
    <div class="adm-table-wrap">
        <table class="adm-table" aria-label="Testimonials">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Service</th>
                    <th>Source</th>
                    <th>Flags</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>
                        <div class="td-primary">{{ $item->name }}</div>
                        <div class="td-muted">{{ $item->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <span style="color:var(--adm-gold);font-size:14px;letter-spacing:1px">{{ $item->stars_html }}</span>
                    </td>
                    <td style="max-width:280px">
                        <div style="font-size:13px;color:var(--adm-text-mid);white-space:normal;line-height:1.5">
                            {{ Str::limit($item->review, 120) }}
                        </div>
                    </td>
                    <td class="td-muted">{{ $item->service ?? '—' }}</td>
                    <td class="td-muted" style="text-transform:capitalize">{{ $item->source }}</td>
                    <td>
                        @if($item->is_featured)
                            <span class="adm-badge adm-badge--pending">⭐ Featured</span>
                        @endif
                    </td>
                    <td>
                        @if($item->is_approved)
                            <span class="adm-badge adm-badge--confirmed">Approved</span>
                        @else
                            <span class="adm-badge adm-badge--pending">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:nowrap">
                            @if(!$item->is_approved)
                            <form method="POST" action="{{ route('admin.testimonials.approve', $item->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="adm-btn adm-btn--outline adm-btn--sm">Approve</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.testimonials.destroy', $item->id) }}"
                                  onsubmit="return confirm('Delete this testimonial?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

@endsection
