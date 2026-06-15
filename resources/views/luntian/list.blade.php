@extends('layouts.dashboard')

@section('title', 'Luntian List')

@section('body_class', 'page-luntian-list')

@section('content')
    @php
        $statusColors = $statusColors ?? [];
        $statusFontColors = $statusFontColors ?? [];
    @endphp
    <div class="block max-w-full pb-0 luntian-list-page">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4 luntian-list-header">
            <div class="min-w-0 luntian-list-header-text">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-100 luntian-list-title">Luntian List</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-400 luntian-list-subtitle">View and manage all Luntian jobs.</p>
            </div>
            <div class="shrink-0 luntian-list-search-wrap">
                <label for="luntianSearch" class="mb-1.5 block text-xs font-semibold text-slate-400 luntian-search-label">Search</label>
                <div class="relative flex min-w-[260px] items-center luntian-search-input-wrap">
                    <svg class="luntian-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="luntianSearch" class="w-full rounded-lg border border-slate-700 bg-slate-800 py-2 pl-9 pr-3.5 text-sm text-slate-200 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 luntian-search-input" placeholder="Search by client, job number, email..." autocomplete="off" aria-label="Search Luntian jobs">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-700 bg-slate-900 shadow luntian-table-card">
            <div class="luntian-table-wrap">
                <table class="luntian-table" id="luntianTable">
                    <colgroup>
                        <col class="luntian-col-action">
                        <col class="luntian-col-log-date">
                        <col class="luntian-col-client">
                        <col class="luntian-col-client-name">
                        <col class="luntian-col-reference">
                        <col class="luntian-col-job-type">
                        <col class="luntian-col-priority">
                        <col class="luntian-col-staff">
                        <col class="luntian-col-checker">
                        <col class="luntian-col-status">
                        <col class="luntian-col-due-date">
                        <col class="luntian-col-completion-date">
                        <col class="luntian-col-complexity">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="luntian-th luntian-th-action" data-sort="">
                                <span>Action</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Log Date</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Client</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Client Name</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Reference</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Job Type</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Priority</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Staff</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Checker</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Status</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Due Date</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Completion Date</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="luntian-th" data-sort="">
                                <span>Complexity</span>
                                <span class="luntian-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs ?? [] as $job)
                            @php
                                $log = $job->log_date ? \Carbon\Carbon::parse($job->log_date, 'Asia/Manila') : null;
                                $priorityText = (string) ($job->priority ?? '');
                                $status = (string) ($job->job_status ?? 'Allocated');
                                $statusClass = 'luntian-badge-' . strtolower(str_replace(' ', '-', $status));
                                $priorityBg = $priorityColors[$priorityText] ?? null;
                                $statusBg = $statusColors[$status] ?? null;
                                $statusFg = $statusFontColors[$status] ?? \App\Models\Status::DEFAULT_FONT_COLOR;
                                $priorityLower = strtolower($priorityText);

                                $statusLower = strtolower($status);
                                $statusOptions = \App\Support\LbsJobStatusFlow::nextAllowedLabels($status, $statuses ?? []);
                                $canEditStatus = count($statusOptions) > 0;

                                $completion = $job->completion_date ? \Carbon\Carbon::parse($job->completion_date, 'Asia/Manila') : null;
                                $due = null;
                                $isOverdue = false;
                                if ($log) {
                                    if (str_contains($priorityLower, 'top')) {
                                        $due = $log->copy()->addHours(6);
                                    }
                                    $isOverdue = $due && !$completion && $due->lt(now('Asia/Manila'));
                                }

                                $dueDate1 = $due ? $due->format('F j, Y') : '—';
                                $dueDate2 = $due ? $due->format('g:i A') : '';
                                $completionDate1 = $completion ? $completion->format('F j, Y') : '—';
                                $completionDate2 = $completion ? $completion->format('g:i A') : '';

                                $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                                $complexity = max(0, min(5, $complexity));
                            @endphp
                            <tr class="luntian-data-row lbs-data-row" data-job-id="{{ $job->job_id }}" data-job-units="{{ (int) ($job->units ?? 0) }}" data-update-url="{{ route('luntian.job.update', ['id' => $job->job_id]) }}">
                                {{-- Action column: same pattern as LBS list — no .lbs-td hover cell styling; icons use lbs-action-icon --}}
                                <td class="luntian-td luntian-td-action overflow-visible text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                    <div class="relative z-10 flex flex-nowrap items-center justify-center gap-1.5">
                                        <a href="{{ route('luntian.add', ['duplicate' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate job to Add New form">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </a>
                                        <a href="{{ route('luntian.job.view', ['id' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View job {{ $job->job_reference_no }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="luntian-td luntian-td-log-date" data-label="Log Date" data-sort="{{ $job->log_date }}">
                                    <span class="luntian-date-line1">{{ $log ? $log->format('F j, Y') : '—' }}</span>
                                    <span class="luntian-date-line2">{{ $log ? $log->format('g:i A') : '' }}</span>
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Client">
                                    <span class="luntian-date-line1">{{ $job->client_account_name ?? $job->client_code ?? '—' }}</span>
                                    <span class="luntian-date-line2">{{ $job->ncc_compliance ?? '' }}</span>
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Client Name">{{ $job->client_code ?? '—' }}</td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Reference">{{ $job->job_reference_no ?? '—' }}</td>
                                <td class="luntian-td luntian-td-job-type" data-label="Job Type">
                                    <span class="luntian-job-line1">{{ $job->job_type ?? '—' }}</span>
                                    @if(!empty($job->job_request_id))
                                        <span class="luntian-job-line2">{{ $job->job_request_id }}</span>
                                    @endif
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Priority" style="white-space: nowrap;">
                                    @if($priorityBg)
                                        <span
                                            class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold"
                                            style="background-color: {{ $priorityBg }};"
                                        >{{ $priorityText !== '' ? $priorityText : '—' }}</span>
                                    @else
                                        <span class="luntian-priority">{{ $priorityText !== '' ? $priorityText : '—' }}</span>
                                    @endif
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Staff" style="white-space: nowrap;">
                                    <div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="staff">
                                        <button type="button" class="lbs-initials lbs-initials-trigger inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</button>
                                        <div class="lbs-initials-menu fixed z-[9999] flex min-w-[70px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JS">JS</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Checker" style="white-space: nowrap;">
                                    <div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="checker">
                                        <button type="button" class="lbs-initials lbs-initials-trigger inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</button>
                                        <div class="lbs-initials-menu fixed z-[9999] flex min-w-[70px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JS">JS</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Status" style="white-space: nowrap;">
                                    @if($canEditStatus && count($statusOptions) > 0)
                                        <div class="lbs-status-wrap relative inline-block" data-status-wrap>
                                            <button
                                                type="button"
                                                class="lbs-badge lbs-status-trigger inline-block rounded-md border-0 px-2 py-1 text-xs font-semibold leading-tight cursor-pointer hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:ring-offset-0 dark:focus:ring-blue-500/40"
                                                @if($statusBg)
                                                    style="background-color: {{ $statusBg }}; color: {{ $statusFg }};"
                                                @endif
                                                data-status-trigger
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                                data-reference="{{ $job->job_reference_no }}"
                                            >{{ $status }}</button>
                                            <div class="lbs-status-menu fixed z-[9999] flex min-w-[90px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                                @foreach($statusOptions as $opt)
                                                    <button type="button" role="menuitem" class="lbs-status-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-status-value="{{ $opt }}">{{ $opt }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        @if($statusBg)
                                            <span
                                                class="lbs-badge lbs-status-badge-readonly inline-block cursor-default rounded-md px-2 py-1 text-xs font-semibold opacity-95"
                                                style="background-color: {{ $statusBg }}; color: {{ $statusFg }};"
                                                aria-disabled="true"
                                            >{{ $status }}</span>
                                        @else
                                            <span class="luntian-badge {{ $statusClass }}" aria-disabled="true">{{ $status }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="luntian-td luntian-td-nowrap lbs-td-due" data-label="Due Date" data-sort="{{ $due ? $due->format('Y-m-d H:i:s') : '' }}" data-overdue="{{ $isOverdue ? '1' : '0' }}">
                                    <span class="luntian-date-line1 {{ $isOverdue ? 'text-red-400' : '' }}">{{ $dueDate1 }}</span>
                                    @if($dueDate2)
                                        <span class="luntian-date-line2">{{ $dueDate2 }}</span>
                                    @endif
                                    @if($isOverdue)
                                        <span class="luntian-date-line2" style="color: rgb(248 113 113); margin-top: 0.15rem;">(Overdue)</span>
                                    @endif
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Completion Date" data-sort="{{ $completion ? $completion->format('Y-m-d H:i:s') : '' }}">
                                    <span class="luntian-date-line1">{{ $completionDate1 }}</span>
                                    @if($completionDate2)
                                        <span class="luntian-date-line2">{{ $completionDate2 }}</span>
                                    @endif
                                </td>
                                <td class="luntian-td luntian-td-nowrap" data-label="Complexity" data-sort="{{ $complexity }}">
                                    <span class="luntian-stars inline-flex items-center" data-rating="{{ $complexity }}" aria-label="{{ $complexity }} out of 5">
                                        @include('lbs.partials.stars', ['rating' => $complexity])
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="luntian-td text-center text-slate-400" colspan="13">No Luntian jobs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.luntian-list-page { width: 100%; max-width: 100%; }
.luntian-list-header { margin-bottom: 1.75rem; }
.luntian-list-title { color: rgb(248 250 252); }
.luntian-list-subtitle { color: rgb(148 163 184); }
.luntian-list-search-wrap { min-width: 260px; }
.luntian-search-icon { pointer-events: none; position: absolute; left: 0.75rem; color: rgb(148 163 184); }
.luntian-table-card { overflow: hidden; }
.luntian-table-wrap { overflow-x: auto; }
.luntian-table { width: 100%; min-width: 1750px; border-collapse: collapse; font-size: 0.875rem; table-layout: fixed; }
.luntian-table col.luntian-col-action { width: 110px; }
.luntian-table col.luntian-col-log-date { width: 150px; }
.luntian-table col.luntian-col-client { width: 220px; }
.luntian-table col.luntian-col-client-name { width: 130px; }
.luntian-table col.luntian-col-reference { width: 150px; }
.luntian-table col.luntian-col-job-type { width: 360px; }
.luntian-table col.luntian-col-priority { width: 150px; }
.luntian-table col.luntian-col-staff { width: 120px; }
.luntian-table col.luntian-col-checker { width: 120px; }
.luntian-table col.luntian-col-status { width: 170px; }
.luntian-table col.luntian-col-due-date { width: 160px; }
.luntian-table col.luntian-col-completion-date { width: 170px; }
.luntian-table col.luntian-col-complexity { width: 140px; }
.luntian-th { cursor: pointer; user-select: none; white-space: nowrap; border-bottom: 1px solid rgb(51 65 85); background: rgb(30 41 59); padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: rgb(148 163 184); text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.04em; }
.luntian-th-action { cursor: default; }
.luntian-sort-icon { margin-left: 0.25rem; font-size: 0.75rem; opacity: .65; }
.luntian-th[data-sort="asc"] .luntian-sort-icon { font-size: 0; opacity: 1; }
.luntian-th[data-sort="asc"] .luntian-sort-icon::before { content: "↑"; font-size: 0.75rem; }
.luntian-th[data-sort="desc"] .luntian-sort-icon { font-size: 0; opacity: 1; }
.luntian-th[data-sort="desc"] .luntian-sort-icon::before { content: "↓"; font-size: 0.75rem; }
.luntian-td { border-bottom: 1px solid rgb(51 65 85); padding: 0.75rem 1rem; vertical-align: middle; color: rgb(226 232 240); background: rgb(15 23 42); }
/* Row hover: same as LBS — green on data cells only; action column stays base slate (LBS first td has no .lbs-td) */
.page-luntian-list .luntian-data-row:hover .luntian-td:not(.luntian-td-action) { background: #ecfdf5; color: rgb(15 23 42); }
.page-luntian-list .luntian-data-row:hover .luntian-td-action { background: rgb(15 23 42); color: rgb(226 232 240); }
.page-luntian-list .luntian-data-row:hover .luntian-date-line2,
.page-luntian-list .luntian-data-row:hover .luntian-job-line2 { color: rgb(100 116 139); }
[data-theme="dark"] .page-luntian-list .luntian-data-row:hover .luntian-td:not(.luntian-td-action) { background: rgba(6, 78, 59, 0.9); color: rgb(241 245 249); }
[data-theme="dark"] .page-luntian-list .luntian-data-row:hover .luntian-td-action { background: rgba(15, 23, 42, 0.98); color: rgb(226 232 240); }
[data-theme="dark"] .page-luntian-list .luntian-data-row:hover .luntian-date-line2,
[data-theme="dark"] .page-luntian-list .luntian-data-row:hover .luntian-job-line2 { color: rgb(167 243 208); }
.luntian-td-nowrap { white-space: nowrap; }
.luntian-td-job-type { min-width: 340px; }
.luntian-date-line1, .luntian-job-line1 { display: block; font-weight: 500; color: rgb(226 232 240); }
.luntian-date-line2, .luntian-job-line2 { display: block; font-size: 0.8125rem; color: rgb(148 163 184); }
.luntian-job-line1 { line-height: 1.35; word-break: normal; overflow-wrap: break-word; }
.luntian-job-line2 { margin-top: 0.25rem; }
/* Match LBS list badge shaping (app.css .page-lbs-list .lbs-priority / .lbs-badge) */
.page-luntian-list .lbs-priority {
    border-radius: 9999px;
    padding-inline: 0.7rem;
    padding-block: 0.2rem;
    font-size: 0.75rem;
}
.page-luntian-list .lbs-badge {
    border-radius: 9999px;
    padding-inline: 0.65rem;
    padding-block: 0.2rem;
    font-size: 0.75rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.page-luntian-list .lbs-status-trigger {
    box-shadow: 0 8px 18px -12px rgba(15, 23, 42, 0.6);
}
[data-theme="dark"] .page-luntian-list .lbs-status-trigger {
    box-shadow: 0 12px 26px -18px rgba(0, 0, 0, 0.9);
}
.page-luntian-list .lbs-initials-trigger {
    border-radius: 9999px;
    font-size: 0.75rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.luntian-badge, .luntian-initials { display: inline-block; border: 0; border-radius: 0.5rem; padding: 0.3rem 0.5rem; font-size: 0.75rem; font-weight: 700; line-height: 1.2; cursor: pointer; }
.luntian-initials { border: 1px solid rgb(71 85 105); background: rgb(30 41 59); color: rgb(226 232 240); min-width: 2.8rem; }
.luntian-priority { display: inline-block; border: 0; border-radius: 0.5rem; padding: 0.3rem 0.5rem; font-size: 0.75rem; font-weight: 700; line-height: 1.2; cursor: pointer; color: rgb(226 232 240); background: rgb(148 163 184 / 0.15); }
.luntian-badge-pending { background: rgb(250 204 21 / 0.2); color: rgb(161 98 7); }
.luntian-badge-accepted { background: rgb(34 197 94 / 0.2); color: rgb(21 128 61); }
.luntian-badge-allocated { background: rgb(59 130 246 / 0.2); color: rgb(37 99 235); }
.luntian-badge-awaiting-further-information { background: rgb(245 158 11 / 0.2); color: rgb(180 83 9); }
.luntian-badge-completed { background: rgb(16 185 129 / 0.2); color: rgb(5 150 105); }
.luntian-status-menu,
.luntian-initials-menu { position: fixed; z-index: 9999; display: flex; min-width: 90px; flex-direction: column; gap: 2px; border-radius: 0.5rem; border: 1px solid rgb(51 65 85); background: rgb(30 41 59); padding: 0.25rem; box-shadow: 0 10px 20px rgb(15 23 42 / 0.25); }
.luntian-status-menu[hidden],
.luntian-initials-menu[hidden] { display: none !important; }
.luntian-status-option,
.luntian-initials-option { border: 0; border-radius: 0.375rem; background: transparent; padding: 0.35rem 0.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(226 232 240); cursor: pointer; }
.luntian-status-option:hover,
.luntian-initials-option:hover { background: rgb(255 255 255 / 0.12); }
/* Shared with LBS list (lbs-list.js): menus use hidden */
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
    <script>
        (function() {
            var searchEl = document.getElementById('luntianSearch');
            var table = document.getElementById('luntianTable');
            if (searchEl && table) {
                var tbody = table.querySelector('tbody');
                searchEl.addEventListener('input', function() {
                    var q = (this.value || '').trim().toLowerCase();
                    if (!tbody) return;
                    var rows = tbody.querySelectorAll('tr');
                    rows.forEach(function(tr) {
                        var text = (tr.textContent || '').toLowerCase();
                        tr.style.display = !q || text.indexOf(q) !== -1 ? '' : 'none';
                    });
                });
            }
            if (!table) return;
            var thead = table.querySelector('thead');
            thead.addEventListener('click', function(e) {
                var th = e.target.closest('th');
                if (!th || th.classList.contains('luntian-th-action')) return;
                var current = th.getAttribute('data-sort') || '';
                var next = current === 'asc' ? 'desc' : 'asc';
                thead.querySelectorAll('th').forEach(function(h) { h.setAttribute('data-sort', ''); });
                th.setAttribute('data-sort', next);
                var colIndex = Array.prototype.indexOf.call(thead.querySelectorAll('th'), th);
                var tbody = table.querySelector('tbody');
                var rows = Array.from(tbody.querySelectorAll('tr'));
                rows.sort(function(a, b) {
                    var aCell = a.children[colIndex];
                    var bCell = b.children[colIndex];
                    var aVal = (aCell && (aCell.getAttribute('data-sort') || aCell.textContent)) || '';
                    var bVal = (bCell && (bCell.getAttribute('data-sort') || bCell.textContent)) || '';
                    var aNum = parseFloat(aVal);
                    var bNum = parseFloat(bVal);
                    if (!isNaN(aNum) && !isNaN(bNum)) {
                        return next === 'asc' ? aNum - bNum : bNum - aNum;
                    }
                    if (next === 'asc') return String(aVal).localeCompare(String(bVal), undefined, { numeric: true });
                    return String(bVal).localeCompare(String(aVal), undefined, { numeric: true });
                });
                rows.forEach(function(r) { tbody.appendChild(r); });
            });
        })();
    </script>
@endpush
