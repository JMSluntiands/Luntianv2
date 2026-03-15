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
    @endpush
