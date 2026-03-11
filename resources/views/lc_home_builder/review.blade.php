@extends('layouts.dashboard')

@section('title', 'LC HOME BUILDER For Review')

@section('body_class', 'page-lc_home_builder-review')

@section('content')
    <div class="lc_home_builder-list-page">
        <div class="lc_home_builder-list-header">
            <div class="lc_home_builder-list-header-text">
                <h1 class="lc_home_builder-list-title">LC HOME BUILDER For Review</h1>
                <p class="lc_home_builder-list-subtitle">View LC HOME BUILDER jobs pending review.</p>
            </div>
        </div>
        <div class="lc_home_builder-table-card">
            <div class="lc_home_builder-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No LC HOME BUILDER jobs for review.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .lc_home_builder-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-lc_home_builder-review .content { padding-bottom: 0; }
        .lc_home_builder-list-header { margin-bottom: 1.75rem; }
        .lc_home_builder-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; margin: 0 0 0.375rem 0; }
        .lc_home_builder-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .lc_home_builder-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; }
        .lc_home_builder-table-wrap { padding: 1rem; }
        html[data-theme="light"] .lc_home_builder-list-title { color: #1e293b; }
        html[data-theme="light"] .lc_home_builder-list-subtitle { color: #64748b; }
        html[data-theme="light"] .lc_home_builder-table-card { background: #fff; border-color: #e2e8f0; }
    </style>
@endpush
