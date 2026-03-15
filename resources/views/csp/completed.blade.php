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
    @endpush
