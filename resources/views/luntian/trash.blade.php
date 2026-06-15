@extends('layouts.dashboard')

@section('title', 'Luntian Archive')

@section('body_class', 'page-luntian-trash')

@section('content')
    <div class="luntian-list-page">
        <div class="luntian-list-header">
            <div class="luntian-list-header-text">
                <h1 class="luntian-list-title">Luntian Archive</h1>
                <p class="luntian-list-subtitle">View archived Luntian jobs.</p>
            </div>
        </div>
        <div class="luntian-table-card">
            <div class="luntian-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No archived Luntian jobs.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
