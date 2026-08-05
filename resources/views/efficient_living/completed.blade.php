@extends('layouts.dashboard')

@section('title', 'Efficient Living Completed')

@section('body_class', 'page-efficient_living-completed')

@section('content')
    <div class="efficient_living-list-page">
        <div class="efficient_living-list-header">
            <div class="efficient_living-list-header-text">
                <h1 class="efficient_living-list-title">Efficient Living Completed</h1>
                <p class="efficient_living-list-subtitle">View completed Efficient Living jobs.</p>
            </div>
        </div>
        <div class="efficient_living-table-card">
            <div class="efficient_living-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No completed Efficient Living jobs yet.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
