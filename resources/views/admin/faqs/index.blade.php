@extends('layouts.admin')

@section('title', 'FAQs')
@section('breadcrumb', 'FAQs')
@section('page-title', 'Frequently Asked Questions')
@section('page-sub', $faqs->count() . ' ' . Str::plural('question', $faqs->count()) . ' configured')

@section('content')

{{-- Add New FAQ --}}
<div class="adm-card" style="margin-bottom:20px">
    <div class="adm-card__header">
        <span class="adm-card__title">Add New FAQ</span>
    </div>
    <div class="adm-card__body">
        <form method="POST" action="{{ route('admin.faqs.store') }}" novalidate>
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div class="adm-form-group" style="margin-bottom:0">
                <label class="adm-form-label" for="category">Category</label>
                <input type="text" id="category" name="category" class="adm-form-control @error('category') adm-form-control--error @enderror"
                       value="{{ old('category') }}" maxlength="80" placeholder="General, Booking, Treatments, Pricing…">
                @error('category')<span class="adm-form-error">{{ $message }}</span>@enderror
            </div>
            <div class="adm-form-group" style="margin-bottom:0">
                <label class="adm-form-label" for="sort_order">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="adm-form-control"
                       value="{{ old('sort_order', 0) }}" min="0">
            </div>
        </div>

        <div class="adm-form-group">
            <label class="adm-form-label adm-form-label--req" for="question">Question (English)</label>
            <input type="text" id="question" name="question" class="adm-form-control @error('question') adm-form-control--error @enderror"
                   value="{{ old('question') }}" required maxlength="500" placeholder="How long does the procedure take?">
            @error('question')<span class="adm-form-error">{{ $message }}</span>@enderror
        </div>

        <div class="adm-form-group">
            <label class="adm-form-label adm-form-label--req" for="answer">Answer (English)</label>
            <textarea id="answer" name="answer" rows="3" class="adm-form-control @error('answer') adm-form-control--error @enderror"
                      required maxlength="5000" placeholder="Answer text…">{{ old('answer') }}</textarea>
            @error('answer')<span class="adm-form-error">{{ $message }}</span>@enderror
        </div>

        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked>
                <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Active</span>
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1">
                <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Featured</span>
            </label>
            <button type="submit" class="adm-btn adm-btn--gold">Add FAQ</button>
        </div>

        </form>
    </div>
</div>

{{-- FAQ List --}}
@if($faqs->isEmpty())
<div class="adm-card">
    <div style="text-align:center;padding:64px 24px;color:var(--adm-text-muted)">
        <div style="font-size:48px;margin-bottom:16px" aria-hidden="true">❓</div>
        <p style="font-size:15px;margin:0;font-weight:600;color:var(--adm-navy)">No FAQs yet</p>
        <p style="font-size:13px;margin:8px 0 0">Add your first frequently asked question above.</p>
    </div>
</div>
@else

{{-- Group by category --}}
@php $grouped = $faqs->groupBy('category'); @endphp

@foreach($grouped as $category => $group)
<div class="adm-card" style="margin-bottom:16px">
    <div class="adm-card__header">
        <span class="adm-card__title">{{ $category ?: 'General' }}</span>
        <span style="font-size:12px;color:var(--adm-text-muted)">{{ $group->count() }} {{ Str::plural('question', $group->count()) }}</span>
    </div>

    @foreach($group as $faq)
    <div style="border-bottom:1px solid var(--adm-card-border);padding:16px 22px" id="faq-{{ $faq->id }}">

        {{-- View mode --}}
        <div class="faq-view-{{ $faq->id }}">
            <div style="display:flex;align-items:flex-start;gap:12px">
                <div style="flex:1">
                    <div style="font-size:14px;font-weight:600;color:var(--adm-navy);margin-bottom:6px">
                        {{ $faq->question }}
                    </div>
                    <div style="font-size:13px;color:var(--adm-text-mid);line-height:1.6">
                        {{ Str::limit($faq->answer, 200) }}
                    </div>
                    <div style="display:flex;gap:8px;margin-top:8px">
                        @if($faq->is_featured)
                        <span class="adm-badge adm-badge--pending">⭐ Featured</span>
                        @endif
                        @if($faq->is_active)
                        <span class="adm-badge adm-badge--confirmed">Active</span>
                        @else
                        <span class="adm-badge adm-badge--cancelled">Inactive</span>
                        @endif
                        <span class="adm-badge adm-badge--no_show">Order: {{ $faq->sort_order }}</span>
                    </div>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0">
                    <button type="button" onclick="toggleFaqEdit({{ $faq->id }})"
                            class="adm-btn adm-btn--outline adm-btn--sm">Edit</button>
                    <form method="POST" action="{{ route('admin.faqs.destroy', $faq->id) }}"
                          onsubmit="return confirm('Delete this FAQ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit mode (hidden by default) --}}
        <div class="faq-edit-{{ $faq->id }}" style="display:none">
            <form method="POST" action="{{ route('admin.faqs.update', $faq->id) }}" novalidate>
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div class="adm-form-group" style="margin-bottom:0">
                    <label class="adm-form-label" for="edit_category_{{ $faq->id }}">Category</label>
                    <input type="text" id="edit_category_{{ $faq->id }}" name="category"
                           class="adm-form-control" value="{{ $faq->category }}" maxlength="80">
                </div>
                <div class="adm-form-group" style="margin-bottom:0">
                    <label class="adm-form-label" for="edit_order_{{ $faq->id }}">Sort Order</label>
                    <input type="number" id="edit_order_{{ $faq->id }}" name="sort_order"
                           class="adm-form-control" value="{{ $faq->sort_order }}" min="0">
                </div>
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="edit_q_{{ $faq->id }}">Question</label>
                <input type="text" id="edit_q_{{ $faq->id }}" name="question"
                       class="adm-form-control" value="{{ $faq->question }}" required maxlength="500">
            </div>

            <div class="adm-form-group">
                <label class="adm-form-label" for="edit_a_{{ $faq->id }}">Answer</label>
                <textarea id="edit_a_{{ $faq->id }}" name="answer" rows="4"
                          class="adm-form-control" required maxlength="5000">{{ $faq->answer }}</textarea>
            </div>

            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }}>
                    <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Active</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" {{ $faq->is_featured ? 'checked' : '' }}>
                    <span style="font-size:13px;font-weight:600;color:var(--adm-navy)">Featured</span>
                </label>
                <button type="submit" class="adm-btn adm-btn--gold adm-btn--sm">Save</button>
                <button type="button" onclick="toggleFaqEdit({{ $faq->id }})"
                        class="adm-btn adm-btn--outline adm-btn--sm">Cancel</button>
            </div>

            </form>
        </div>

    </div>
    @endforeach

</div>
@endforeach

@endif

@push('scripts')
<script>
function toggleFaqEdit(id) {
    const view = document.querySelector('.faq-view-' + id);
    const edit = document.querySelector('.faq-edit-' + id);
    if (!view || !edit) return;
    const isEditing = edit.style.display !== 'none';
    view.style.display = isEditing ? 'block' : 'none';
    edit.style.display = isEditing ? 'none'  : 'block';
}
</script>
@endpush

@endsection
