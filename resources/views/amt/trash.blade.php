@extends('layouts.dashboard')

@section('title', 'A&M Archive')

@section('body_class', 'page-amt-trash')

@section('content')
    <div class="amt-list-page">
        <div class="amt-list-header">
            <div class="amt-list-header-text">
                <h1 class="amt-list-title">A&amp;M Archive</h1>
                <p class="amt-list-subtitle">View archived A&amp;M jobs.</p>
            </div>
        </div>
        <div class="amt-table-card">
            <div class="amt-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No archived A&amp;M jobs.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
