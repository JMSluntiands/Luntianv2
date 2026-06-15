@extends('layouts.dashboard')

@section('title', 'Luntian Completed')

@section('body_class', 'page-luntian-completed')

@section('content')
    <div class="luntian-list-page">
        <div class="luntian-list-header">
            <div class="luntian-list-header-text">
                <h1 class="luntian-list-title m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-slate-100">Luntian Completed</h1>
                <p class="luntian-list-subtitle m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View completed Luntian jobs.</p>
            </div>
        </div>
        <div class="luntian-table-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="luntian-table-wrap">
                <p class="p-8 text-center text-slate-500 dark:text-slate-400">No completed Luntian jobs yet.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush
