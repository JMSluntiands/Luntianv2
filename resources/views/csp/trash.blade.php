@extends('layouts.dashboard')

@section('title', 'CSP Archive')

@section('body_class', 'page-csp-trash')

@section('content')
    <div class="csp-list-page">
        <div class="csp-list-header">
            <div class="csp-list-header-text">
                <h1 class="csp-list-title">CSP Archive</h1>
                <p class="csp-list-subtitle">View archived CSP jobs.</p>
            </div>
        </div>
        <div class="csp-table-card">
            <div class="csp-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No archived CSP jobs.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
