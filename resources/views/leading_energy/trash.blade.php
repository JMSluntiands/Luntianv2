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
    @endpush
