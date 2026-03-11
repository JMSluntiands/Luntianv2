@extends('layouts.dashboard')

@section('title', 'EFFICIENT LIVING For Review')

@section('body_class', 'page-efficient_living-review')

@section('content')
    <div class="efficient_living-list-page">
        <div class="efficient_living-list-header">
            <div class="efficient_living-list-header-text">
                <h1 class="efficient_living-list-title">EFFICIENT LIVING For Review</h1>
                <p class="efficient_living-list-subtitle">View EFFICIENT LIVING jobs pending review.</p>
            </div>
        </div>
        <div class="efficient_living-table-card">
            <div class="efficient_living-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No EFFICIENT LIVING jobs for review.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .efficient_living-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-efficient_living-review .content { padding-bottom: 0; }
        .efficient_living-list-header { margin-bottom: 1.75rem; }
        .efficient_living-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; margin: 0 0 0.375rem 0; }
        .efficient_living-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .efficient_living-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; }
        .efficient_living-table-wrap { padding: 1rem; }
        html[data-theme="light"] .efficient_living-list-title { color: #1e293b; }
        html[data-theme="light"] .efficient_living-list-subtitle { color: #64748b; }
        html[data-theme="light"] .efficient_living-table-card { background: #fff; border-color: #e2e8f0; }
    </style>
@endpush
