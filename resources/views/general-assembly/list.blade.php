@extends('layouts.dashboard')

@section('title', 'General Assembly List')

@section('body_class', 'page-ga-list')

@section('content')
    @php
        $filterBuilderOptions = collect($filterBuilders ?? []);
        $filterPriorityOptions = collect($filterPriorities ?? []);
        $statusColors = $statusColors ?? [];
        $statusFontColors = $statusFontColors ?? [];
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">General Assembly List</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View and manage all General Assembly jobs.</p>
        </div>

        <div class="mb-6 rounded-xl border border-slate-200 bg-white/80 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                    <div class="relative flex items-center">
                        <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search General Assembly jobs">
                    </div>
                </div>
                <div>
                    <label for="lbsFilterDate" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Date Logged</label>
                    <input type="date" id="lbsFilterDate" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                </div>
                <div>
                    <label for="lbsFilterBuilder" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Builder</label>
                    <select id="lbsFilterBuilder" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                        <option value="">All</option>
                        @foreach($filterBuilderOptions as $builderName)
                            <option value="{{ $builderName }}">{{ $builderName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="lbsFilterPriority" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Priority</label>
                    <select id="lbsFilterPriority" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                        <option value="">All</option>
                        @foreach($filterPriorityOptions as $priorityOption)
                            <option value="{{ $priorityOption }}">{{ $priorityOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-transparent select-none">Reset</label>
                    <button type="button" id="lbsFilterReset" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Reset</button>
                </div>
            </div>
        </div>

        <div id="lbs-list-tables-refresh-root" data-refresh-url="{{ route('general_assembly.list.tablesFragment') }}">
            <div class="mb-3 flex flex-col gap-3 border-b border-slate-200 pb-3 dark:border-slate-700 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <h2 class="m-0 text-lg font-semibold tracking-tight text-slate-900 dark:text-white">Job tables</h2>
                    <p class="m-0 mt-0.5 max-w-xl text-sm leading-snug text-slate-600 dark:text-slate-400">Active General Assembly rows and form submissions. Use refresh to reload data without leaving this page.</p>
                </div>
                <button type="button" id="lbsListRefreshBtn" class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 sm:self-auto" title="Refresh tables" aria-label="Refresh job tables">
                    <svg class="ga-list-refresh-icon h-4 w-4 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 16h5v5"/></svg>
                    <span>Refresh</span>
                </button>
            </div>
            <div id="lbs-list-tables-inner">
                @include('general-assembly.partials.list-tables-body')
            </div>
        </div>

    </div>
@endsection

@push('styles')
<style>
/* Sort icon arrows - JS toggles data-sort */
.lbs-th[data-sort="asc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="asc"] .lbs-sort-icon::before { content: '↑'; font-size: 0.75rem; }
.lbs-th[data-sort="desc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="desc"] .lbs-sort-icon::before { content: '↓'; font-size: 0.75rem; }
.lbs-th:not([data-sort=""]) .lbs-sort-icon { opacity: 1; }
/* Expand icon rotate when open */
.lbs-action-expand[aria-expanded="true"] .lbs-expand-icon { transform: rotate(180deg); }
/* Status badge variants (JS applies these on update/revert) */
.lbs-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
.lbs-badge-completed { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-pending { background: rgba(234, 179, 8, 0.2); color: #eab308; }
.lbs-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-for-email-confirmation { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.lbs-badge-cancelled { background: rgba(100, 116, 139, 0.3); color: #64748b; }
.lbs-badge-for-review { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
.lbs-badge-processing { background: rgba(14, 165, 233, 0.2); color: #0ea5e9; }
.lbs-badge-for-checking { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-revised { background: rgba(100, 116, 139, 0.2); color: #94a3b8; }
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
.lbs-status-trigger, .lbs-initials-trigger { cursor: pointer; }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
@endpush
