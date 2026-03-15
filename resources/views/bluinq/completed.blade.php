@extends('layouts.dashboard')

@section('title', 'BLUINQ Completed')

@section('body_class', 'page-bluinq-completed')

@section('content')
    <div class="bluinq-list-page">
        <div class="bluinq-list-header">
            <div class="bluinq-list-header-text">
                <h1 class="bluinq-list-title">BLUINQ Completed</h1>
                <p class="bluinq-list-subtitle">View completed BLUINQ jobs.</p>
            </div>
        </div>
        <div class="bluinq-table-card">
            <div class="bluinq-table-wrap">
                <p style="padding: 2rem; color: #94a3b8; text-align: center;">No completed BLUINQ jobs yet.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
