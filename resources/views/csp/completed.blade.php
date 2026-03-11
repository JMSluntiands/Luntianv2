@extends('layouts.dashboard')

@section('title', 'CSP Completed')

@section('body_class', 'page-csp-completed')

@section('content')
    <div class="csp-list-page">
        <div class="csp-list-header">
            <div class="csp-list-header-text">
                <h1 class="csp-list-title">CSP Completed</h1>
                <p class="csp-list-subtitle">View completed CSP jobs.</p>
            </div>
        </div>
        <div class="csp-table-card">
            <div class="csp-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No completed CSP jobs yet.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .csp-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-csp-completed .content { padding-bottom: 0; }
        .csp-list-header { margin-bottom: 1.75rem; }
        .csp-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; margin: 0 0 0.375rem 0; }
        .csp-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .csp-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; }
        .csp-table-wrap { padding: 1rem; }
        html[data-theme="light"] .csp-list-title { color: #1e293b; }
        html[data-theme="light"] .csp-list-subtitle { color: #64748b; }
        html[data-theme="light"] .csp-table-card { background: #fff; border-color: #e2e8f0; }
    </style>
@endpush
