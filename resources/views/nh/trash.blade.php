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
    @endpush
