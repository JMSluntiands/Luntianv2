@extends('layouts.dashboard')

@section('title', 'LEADING ENERGY Archive')

@section('body_class', 'page-leading_energy-trash')

@section('content')
    <div class="leading_energy-list-page">
        <div class="leading_energy-list-header">
            <div class="leading_energy-list-header-text">
                <h1 class="leading_energy-list-title">LEADING ENERGY Archive</h1>
                <p class="leading_energy-list-subtitle">View archived LEADING ENERGY jobs.</p>
            </div>
        </div>
        <div class="leading_energy-table-card">
            <div class="leading_energy-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No archived LEADING ENERGY jobs.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .leading_energy-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-leading_energy-trash .content { padding-bottom: 0; }
        .leading_energy-list-header { margin-bottom: 1.75rem; }
        .leading_energy-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; margin: 0 0 0.375rem 0; }
        .leading_energy-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .leading_energy-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; }
        .leading_energy-table-wrap { padding: 1rem; }
        html[data-theme="light"] .leading_energy-list-title { color: #1e293b; }
        html[data-theme="light"] .leading_energy-list-subtitle { color: #64748b; }
        html[data-theme="light"] .leading_energy-table-card { background: #fff; border-color: #e2e8f0; }
    </style>
@endpush
