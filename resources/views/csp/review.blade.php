@extends('layouts.dashboard')

@section('title', 'CSP For Review')

@section('body_class', 'page-csp-review')

@section('content')
    <div class="csp-list-page">
        <div class="csp-list-header">
            <div class="csp-list-header-text">
                <h1 class="csp-list-title">CSP For Review</h1>
                <p class="csp-list-subtitle">View CSP jobs pending review.</p>
            </div>
        </div>
        <div class="csp-table-card">
            <div class="csp-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No CSP jobs for review.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
