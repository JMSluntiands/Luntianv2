@extends('layouts.dashboard')

@section('title', 'BLUINQ For Review')

@section('body_class', 'page-bluinq-review')

@section('content')
    <div class="bluinq-list-page">
        <div class="bluinq-list-header">
            <div class="bluinq-list-header-text">
                <h1 class="bluinq-list-title">BLUINQ For Review</h1>
                <p class="bluinq-list-subtitle">View BLUINQ jobs pending review.</p>
            </div>
        </div>
        <div class="bluinq-table-card">
            <div class="bluinq-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No BLUINQ jobs for review.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
