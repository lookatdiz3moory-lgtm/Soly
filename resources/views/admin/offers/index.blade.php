@extends('layouts.admin')

@section('title', 'Offers')
@section('breadcrumb', 'Offers')
@section('page-title', 'Special Offers')
@section('page-sub', $offers->count() . ' ' . Str::plural('offer', $offers->count()) . ' configured')

@section('page-actions')
    <a href="{{ route('admin.offers.create') }}" class="adm-btn adm-btn--gold">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Add Offer
    </a>
@endsection

@section('content')

<div class="adm-card">

    @if($offers->isEmpty())
    <div style="text-align:center;padding:64px 24px;color:var(--adm-text-muted)">
        <div style="font-size:48px;margin-bottom:16px" aria-hidden="true">🎁</div>
        <p style="font-size:15px;margin:0 0 8px;font-weight:600;color:var(--adm-navy)">No offers yet</p>
        <p style="font-size:13px;margin:0 0 20px">Create your first promotional offer to attract new patients.</p>
        <a href="{{ route('admin.offers.create') }}" class="adm-btn adm-btn--gold">Create First Offer</a>
    </div>
    @else
    <div class="adm-table-wrap">
        <table class="adm-table" role="table" aria-label="Offers list">
            <thead>
                <tr>
                    <th scope="col">Offer</th>
                    <th scope="col">Service</th>
                    <th scope="col">Original</th>
                    <th scope="col">Offer Price</th>
                    <th scope="col">Discount</th>
                    <th scope="col">Expires</th>
                    <th scope="col">Flags</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $offer)
                <tr>
                    <td>
                        <div class="td-primary">{{ $offer->title }}</div>
                        @if($offer->badge_text)
                        <div class="td-muted">{{ $offer->badge_text }}</div>
                        @endif
                    </td>
                    <td class="td-muted">{{ $offer->service?->name ?? '—' }}</td>
                    <td class="td-muted" style="text-decoration:line-through">{{ number_format((float)$offer->original_price) }}</td>
                    <td>
                        <span style="font-weight:700;color:var(--adm-navy)">{{ number_format((float)$offer->offer_price) }}</span>
                        <span style="font-size:11px;color:var(--adm-text-muted)"> EGP</span>
                    </td>
                    <td>
                        <span class="adm-badge adm-badge--pending">{{ $offer->discount_percent }}% off</span>
                    </td>
                    <td class="td-muted">
                        {{ $offer->expires_at ? $offer->expires_at->format('d M Y') : '∞ No expiry' }}
                    </td>
                    <td>
                        @if($offer->is_featured)
                            <span class="adm-badge adm-badge--confirmed">⭐ Featured</span>
                        @endif
                    </td>
                    <td>
                        @if($offer->is_live)
                            <span class="adm-badge adm-badge--confirmed">Live</span>
                        @elseif(!$offer->is_active)
                            <span class="adm-badge adm-badge--cancelled">Inactive</span>
                        @else
                            <span class="adm-badge adm-badge--no_show">Scheduled</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.offers.edit', $offer->id) }}"
                               class="adm-btn adm-btn--outline adm-btn--sm">Edit</a>
                            <form method="POST" action="{{ route('admin.offers.destroy', $offer->id) }}"
                                  onsubmit="return confirm('Delete offer \'{{ addslashes($offer->title) }}\'?')">
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
