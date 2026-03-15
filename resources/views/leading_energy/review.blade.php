@extends('layouts.dashboard')

@section('title', 'LEADING ENERGY For Review')

@section('body_class', 'page-leading_energy-review')

@section('content')
    <div class="leading_energy-list-page">
        <div class="leading_energy-list-header">
            <div class="leading_energy-list-header-text">
                <h1 class="leading_energy-list-title">LEADING ENERGY For Review</h1>
                <p class="leading_energy-list-subtitle">View LEADING ENERGY jobs pending review.</p>
            </div>
        </div>
        <div class="leading_energy-table-card">
            <div class="leading_energy-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No LEADING ENERGY jobs for review.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
