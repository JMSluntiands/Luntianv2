@extends('layouts.dashboard')

@section('title', 'BLUINQ For Review')

@section('body_class', 'page-bluinq-review')

@section('content')
    <div class="bluinq-list-page">
        <div class="bluinq-list-header">
            <div class="bluinq-list-header-text">
                <h1 class="bluinq-list-title">BLUINQ For Review</h1>
                <p class="bluinq-list-subtitle">View BLUINQ jobs pending review.</p>
            </div>
        </div>
        <div class="bluinq-table-card">
            <div class="bluinq-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No BLUINQ jobs for review.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bluinq-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-bluinq-review .content { padding-bottom: 0; }
        .bluinq-list-header { margin-bottom: 1.75rem; }
        .bluinq-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; margin: 0 0 0.375rem 0; }
        .bluinq-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .bluinq-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; }
        .bluinq-table-wrap { padding: 1rem; }
        html[data-theme="light"] .bluinq-list-title { color: #1e293b; }
        html[data-theme="light"] .bluinq-list-subtitle { color: #64748b; }
        html[data-theme="light"] .bluinq-table-card { background: #fff; border-color: #e2e8f0; }
    </style>
@endpush
