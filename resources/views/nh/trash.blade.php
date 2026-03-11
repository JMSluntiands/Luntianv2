@extends('layouts.dashboard')

@section('title', 'NH Archive')

@section('body_class', 'page-nh-trash')

@section('content')
    <div class="nh-list-page">
        <div class="nh-list-header">
            <div class="nh-list-header-text">
                <h1 class="nh-list-title">NH Archive</h1>
                <p class="nh-list-subtitle">View archived NH jobs.</p>
            </div>
        </div>
        <div class="nh-table-card">
            <div class="nh-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No archived NH jobs.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .nh-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-nh-trash .content { padding-bottom: 0; }
        .nh-list-header { margin-bottom: 1.75rem; }
        .nh-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; margin: 0 0 0.375rem 0; }
        .nh-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .nh-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; }
        .nh-table-wrap { padding: 1rem; }
        html[data-theme="light"] .nh-list-title { color: #1e293b; }
        html[data-theme="light"] .nh-list-subtitle { color: #64748b; }
        html[data-theme="light"] .nh-table-card { background: #fff; border-color: #e2e8f0; }
    </style>
@endpush
