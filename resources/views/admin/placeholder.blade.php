@extends('layouts.admin')

@section('title', $pageTitle ?? 'Admin')
@section('breadcrumb', $pageTitle ?? '')

@section('content')
<div style="padding: 2rem;">
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 2rem; max-width: 720px;">
        <h1 style="font-size: 1.5rem; margin-bottom: .75rem;">{{ $pageTitle ?? 'Module' }}</h1>
        <p style="color: #6b7280; line-height: 1.6;">
            {{ $pageMessage ?? 'This admin module is functional but its UI is being prepared. Underlying data is loaded and listed below for reference.' }}
        </p>

        @isset($records)
            <p style="margin-top: 1.25rem; font-size: .875rem; color: #374151;">
                <strong>Records:</strong> {{ $records }}
            </p>
        @endisset

        <p style="margin-top: 1.5rem;">
            <a href="{{ route('admin.dashboard') }}" class="adm-btn adm-btn--outline">← Back to Dashboard</a>
        </p>
    </div>
</div>
@endsection
