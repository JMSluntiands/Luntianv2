@extends('layouts.dashboard')

@section('title', 'NH For Review')

@section('body_class', 'page-nh-review')

@section('content')
    <div class="nh-list-page">
        <div class="nh-list-header">
            <div class="nh-list-header-text">
                <h1 class="nh-list-title">NH For Review</h1>
                <p class="nh-list-subtitle">View NH jobs pending review.</p>
            </div>
        </div>
        <div class="nh-table-card">
            <div class="nh-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No NH jobs for review.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
