@extends('layouts.dashboard')

@section('title', 'LBS List')

@section('body_class', 'page-lbs-list')

@section('content')
    @php
        $statusColors = $statusColors ?? [];
        $statusFontColors = $statusFontColors ?? [];
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">LBS List</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View and manage all LBS jobs.</p>
        </div>

        @include('lbs.partials.list-filters')

        <div id="lbs-list-tables-refresh-root" data-refresh-url="{{ route('lbs.list.tablesFragment') }}">
            <div class="mb-3 flex flex-col gap-3 border-b border-slate-200 pb-3 dark:border-slate-700 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <p class="m-0 max-w-xl text-sm leading-snug text-slate-600 dark:text-slate-400">Active LBS rows. Use refresh to reload data without leaving this page.</p>
                </div>
                <button type="button" id="lbsListRefreshBtn" class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 sm:self-auto" title="Refresh tables" aria-label="Refresh job tables">
                    <svg class="lbs-list-refresh-icon h-4 w-4 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 16h5v5"/></svg>
                    <span>Refresh</span>
                </button>
            </div>
            <div id="lbs-list-tables-inner">
                @include('lbs.partials.list-tables-body')
            </div>
        </div>

    </div>
@endsection

@include('lbs.partials.list-assets')
