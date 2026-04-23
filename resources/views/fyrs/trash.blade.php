@extends('layouts.dashboard')

@section('title', 'Fyrs Energy Wise Archive')

@section('body_class', 'page-fyrs-trash')

@section('content')
    <div class="fyrs-list-page">
        <div class="fyrs-list-header">
            <div class="fyrs-list-header-text">
                <h1 class="fyrs-list-title">Fyrs Energy Wise Archive</h1>
                <p class="fyrs-list-subtitle">View archived Fyrs Energy Wise jobs.</p>
            </div>
        </div>
        <div class="fyrs-table-card">
            <div class="fyrs-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No archived Fyrs Energy Wise jobs.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
