@extends('layouts.dashboard')

@section('title', 'EFFICIENT LIVING List')

@section('body_class', 'page-efficient_living-list')

@section('content')
    <div class="block max-w-full pb-0 efficient_living-list-page">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4 efficient_living-list-header">
            <div class="min-w-0 efficient_living-list-header-text">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-100 efficient_living-list-title">EFFICIENT LIVING List</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-400 efficient_living-list-subtitle">View and manage all EFFICIENT LIVING jobs.</p>
            </div>
            <div class="shrink-0 efficient_living-list-search-wrap">
                <label for="efficient_livingSearch" class="mb-1.5 block text-xs font-semibold text-slate-400 efficient_living-search-label">Search</label>
                <div class="relative flex min-w-[260px] items-center efficient_living-search-input-wrap">
                    <svg class="efficient_living-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="efficient_livingSearch" class="w-full rounded-lg border border-slate-700 bg-slate-800 py-2 pl-9 pr-3.5 text-sm text-slate-200 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 efficient_living-search-input" placeholder="Search by client, job number, email..." autocomplete="off" aria-label="Search EFFICIENT LIVING jobs">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-700 bg-slate-900 shadow efficient_living-table-card">
            <div class="efficient_living-table-wrap">
                <table class="efficient_living-table" id="efficient_livingTable">
                    <colgroup>
                        <col class="efficient_living-col-action">
                        <col class="efficient_living-col-log-date">
                        <col class="efficient_living-col-client">
                        <col class="efficient_living-col-client-name">
                        <col class="efficient_living-col-reference">
                        <col class="efficient_living-col-job-type">
                        <col class="efficient_living-col-priority">
                        <col class="efficient_living-col-staff">
                        <col class="efficient_living-col-checker">
                        <col class="efficient_living-col-status">
                        <col class="efficient_living-col-due-date">
                        <col class="efficient_living-col-completion-date">
                        <col class="efficient_living-col-complexity">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="efficient_living-th efficient_living-th-action" data-sort="">
                                <span>Action</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Log Date</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Client</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Client Name</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Reference</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Job Type</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Priority</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Staff</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Checker</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Status</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Due Date</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Completion Date</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Complexity</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs ?? [] as $job)
                            @php
                                $log = $job->log_date ? \Carbon\Carbon::parse($job->log_date, 'Asia/Manila') : null;
                                $priorityText = (string) ($job->priority ?? '');
                                $status = (string) ($job->job_status ?? 'Allocated');
                                $statusClass = 'efficient_living-badge-' . strtolower(str_replace(' ', '-', $status));
                                $priorityBg = $priorityColors[$priorityText] ?? null;
                                $statusBg = $statusColors[$status] ?? null;
                                $priorityLower = strtolower($priorityText);

                                $statusLower = strtolower($status);
                                $canEditStatus = in_array($statusLower, ['allocated', 'accepted', 'processing', 'revised', 'for checking', 'for review'], true);
                                $statusOptions = [];
                                if ($statusLower === 'allocated') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string) ($s->name ?? ''));
                                        if (in_array($n, ['accepted', 'processing'], true)) {
                                            $statusOptions[] = $s->name;
                                        }
                                    }
                                } elseif (in_array($statusLower, ['accepted', 'processing', 'revised'], true)) {
                                    foreach ($statuses ?? [] as $s) {
                                        if (strtolower((string) ($s->name ?? '')) === 'for checking') {
                                            $statusOptions[] = $s->name;
                                        }
                                    }
                                } elseif ($statusLower === 'for checking') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string) ($s->name ?? ''));
                                        if (in_array($n, ['for review', 'revised'], true)) {
                                            $statusOptions[] = $s->name;
                                        }
                                    }
                                } elseif ($statusLower === 'for review') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string) ($s->name ?? ''));
                                        if (in_array($n, ['for email confirmation', 'cancelled', 'revised', 'for checking'], true)) {
                                            $statusOptions[] = $s->name;
                                        }
                                    }
                                } else {
                                    foreach ($statuses ?? [] as $s) {
                                        $statusOptions[] = $s->name;
                                    }
                                }

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
                            <tr class="efficient_living-data-row lbs-data-row" data-job-id="{{ $job->job_id }}" data-update-url="{{ route('efficient_living.job.update', ['id' => $job->job_id]) }}">
                                {{-- Action column: same pattern as LBS list — no .lbs-td hover cell styling; icons use lbs-action-icon --}}
                                <td class="efficient_living-td efficient_living-td-action overflow-visible text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                    <div class="relative z-10 flex flex-nowrap items-center justify-center gap-1.5">
                                        <a href="{{ route('efficient_living.add', ['duplicate' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate job to Add New form">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </a>
                                        <a href="{{ route('efficient_living.job.view', ['id' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View job {{ $job->job_reference_no }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="efficient_living-td efficient_living-td-log-date" data-label="Log Date" data-sort="{{ $job->log_date }}">
                                    <span class="efficient_living-date-line1">{{ $log ? $log->format('F j, Y') : '—' }}</span>
                                    <span class="efficient_living-date-line2">{{ $log ? $log->format('g:i A') : '' }}</span>
                                </td>
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Client">
                                    <span class="efficient_living-date-line1">{{ $job->client_account_name ?? $job->client_code ?? '—' }}</span>
                                    <span class="efficient_living-date-line2">{{ $job->ncc_compliance ?? '' }}</span>
                                </td>
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Client Name">{{ $job->client_code ?? '—' }}</td>
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Reference">{{ $job->job_reference_no ?? '—' }}</td>
                                <td class="efficient_living-td efficient_living-td-job-type" data-label="Job Type">
                                    <span class="efficient_living-job-line1">{{ $job->job_type ?? '—' }}</span>
                                    @if(!empty($job->job_request_id))
                                        <span class="efficient_living-job-line2">{{ $job->job_request_id }}</span>
                                    @endif
                                </td>
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Priority" style="white-space: nowrap;">
                                    @if($priorityBg)
                                        <span
                                            class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold"
                                            style="background-color: {{ $priorityBg }};"
                                        >{{ $priorityText !== '' ? $priorityText : '—' }}</span>
                                    @else
                                        <span class="efficient_living-priority">{{ $priorityText !== '' ? $priorityText : '—' }}</span>
                                    @endif
                                </td>
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Staff" style="white-space: nowrap;">
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
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Checker" style="white-space: nowrap;">
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
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Status" style="white-space: nowrap;">
                                    @if($canEditStatus && count($statusOptions) > 0)
                                        <div class="lbs-status-wrap relative inline-block" data-status-wrap>
                                            <button
                                                type="button"
                                                class="lbs-badge lbs-status-trigger inline-block rounded-md border-0 px-2 py-1 text-xs font-semibold leading-tight cursor-pointer hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:ring-offset-0 dark:focus:ring-blue-500/40"
                                                @if($statusBg)
                                                    style="background-color: {{ $statusBg }};"
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
                                                style="background-color: {{ $statusBg }};"
                                                aria-disabled="true"
                                            >{{ $status }}</span>
                                        @else
                                            <span class="efficient_living-badge {{ $statusClass }}" aria-disabled="true">{{ $status }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="efficient_living-td efficient_living-td-nowrap lbs-td-due" data-label="Due Date" data-sort="{{ $due ? $due->format('Y-m-d H:i:s') : '' }}" data-overdue="{{ $isOverdue ? '1' : '0' }}">
                                    <span class="efficient_living-date-line1 {{ $isOverdue ? 'text-red-400' : '' }}">{{ $dueDate1 }}</span>
                                    @if($dueDate2)
                                        <span class="efficient_living-date-line2">{{ $dueDate2 }}</span>
                                    @endif
                                    @if($isOverdue)
                                        <span class="efficient_living-date-line2" style="color: rgb(248 113 113); margin-top: 0.15rem;">(Overdue)</span>
                                    @endif
                                </td>
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Completion Date" data-sort="{{ $completion ? $completion->format('Y-m-d H:i:s') : '' }}">
                                    <span class="efficient_living-date-line1">{{ $completionDate1 }}</span>
                                    @if($completionDate2)
                                        <span class="efficient_living-date-line2">{{ $completionDate2 }}</span>
                                    @endif
                                </td>
                                <td class="efficient_living-td efficient_living-td-nowrap" data-label="Complexity" data-sort="{{ $complexity }}">
                                    <span class="efficient_living-stars inline-flex items-center" data-rating="{{ $complexity }}" aria-label="{{ $complexity }} out of 5">
                                        @include('lbs.partials.stars', ['rating' => $complexity])
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="efficient_living-td text-center text-slate-400" colspan="13">No Efficient Living jobs found.</td>
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
.efficient_living-list-page { width: 100%; max-width: 100%; }
.efficient_living-list-header { margin-bottom: 1.75rem; }
.efficient_living-list-title { color: rgb(248 250 252); }
.efficient_living-list-subtitle { color: rgb(148 163 184); }
.efficient_living-list-search-wrap { min-width: 260px; }
.efficient_living-search-icon { pointer-events: none; position: absolute; left: 0.75rem; color: rgb(148 163 184); }
.efficient_living-table-card { overflow: hidden; }
.efficient_living-table-wrap { overflow-x: auto; }
.efficient_living-table { width: 100%; min-width: 1750px; border-collapse: collapse; font-size: 0.875rem; table-layout: fixed; }
.efficient_living-table col.efficient_living-col-action { width: 110px; }
.efficient_living-table col.efficient_living-col-log-date { width: 150px; }
.efficient_living-table col.efficient_living-col-client { width: 220px; }
.efficient_living-table col.efficient_living-col-client-name { width: 130px; }
.efficient_living-table col.efficient_living-col-reference { width: 150px; }
.efficient_living-table col.efficient_living-col-job-type { width: 360px; }
.efficient_living-table col.efficient_living-col-priority { width: 150px; }
.efficient_living-table col.efficient_living-col-staff { width: 120px; }
.efficient_living-table col.efficient_living-col-checker { width: 120px; }
.efficient_living-table col.efficient_living-col-status { width: 170px; }
.efficient_living-table col.efficient_living-col-due-date { width: 160px; }
.efficient_living-table col.efficient_living-col-completion-date { width: 170px; }
.efficient_living-table col.efficient_living-col-complexity { width: 140px; }
.efficient_living-th { cursor: pointer; user-select: none; white-space: nowrap; border-bottom: 1px solid rgb(51 65 85); background: rgb(30 41 59); padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: rgb(148 163 184); text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.04em; }
.efficient_living-th-action { cursor: default; }
.efficient_living-sort-icon { margin-left: 0.25rem; font-size: 0.75rem; opacity: .65; }
.efficient_living-th[data-sort="asc"] .efficient_living-sort-icon { font-size: 0; opacity: 1; }
.efficient_living-th[data-sort="asc"] .efficient_living-sort-icon::before { content: "↑"; font-size: 0.75rem; }
.efficient_living-th[data-sort="desc"] .efficient_living-sort-icon { font-size: 0; opacity: 1; }
.efficient_living-th[data-sort="desc"] .efficient_living-sort-icon::before { content: "↓"; font-size: 0.75rem; }
.efficient_living-td { border-bottom: 1px solid rgb(51 65 85); padding: 0.75rem 1rem; vertical-align: middle; color: rgb(226 232 240); background: rgb(15 23 42); }
/* Row hover: same as LBS — green on data cells only; action column stays base slate (LBS first td has no .lbs-td) */
.page-efficient_living-list .efficient_living-data-row:hover .efficient_living-td:not(.efficient_living-td-action) { background: #ecfdf5; color: rgb(15 23 42); }
.page-efficient_living-list .efficient_living-data-row:hover .efficient_living-td-action { background: rgb(15 23 42); color: rgb(226 232 240); }
.page-efficient_living-list .efficient_living-data-row:hover .efficient_living-date-line2,
.page-efficient_living-list .efficient_living-data-row:hover .efficient_living-job-line2 { color: rgb(100 116 139); }
[data-theme="dark"] .page-efficient_living-list .efficient_living-data-row:hover .efficient_living-td:not(.efficient_living-td-action) { background: rgba(6, 78, 59, 0.9); color: rgb(241 245 249); }
[data-theme="dark"] .page-efficient_living-list .efficient_living-data-row:hover .efficient_living-td-action { background: rgba(15, 23, 42, 0.98); color: rgb(226 232 240); }
[data-theme="dark"] .page-efficient_living-list .efficient_living-data-row:hover .efficient_living-date-line2,
[data-theme="dark"] .page-efficient_living-list .efficient_living-data-row:hover .efficient_living-job-line2 { color: rgb(167 243 208); }
.efficient_living-td-nowrap { white-space: nowrap; }
.efficient_living-td-job-type { min-width: 340px; }
.efficient_living-date-line1, .efficient_living-job-line1 { display: block; font-weight: 500; color: rgb(226 232 240); }
.efficient_living-date-line2, .efficient_living-job-line2 { display: block; font-size: 0.8125rem; color: rgb(148 163 184); }
.efficient_living-job-line1 { line-height: 1.35; word-break: normal; overflow-wrap: break-word; }
.efficient_living-job-line2 { margin-top: 0.25rem; }
/* Match LBS list badge shaping (app.css .page-lbs-list .lbs-priority / .lbs-badge) */
.page-efficient_living-list .lbs-priority {
    border-radius: 9999px;
    padding-inline: 0.7rem;
    padding-block: 0.2rem;
    font-size: 0.75rem;
}
.page-efficient_living-list .lbs-badge {
    border-radius: 9999px;
    padding-inline: 0.65rem;
    padding-block: 0.2rem;
    font-size: 0.75rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.page-efficient_living-list .lbs-status-trigger {
    box-shadow: 0 8px 18px -12px rgba(15, 23, 42, 0.6);
}
[data-theme="dark"] .page-efficient_living-list .lbs-status-trigger {
    box-shadow: 0 12px 26px -18px rgba(0, 0, 0, 0.9);
}
.page-efficient_living-list .lbs-initials-trigger {
    border-radius: 9999px;
    font-size: 0.75rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.efficient_living-badge, .efficient_living-initials { display: inline-block; border: 0; border-radius: 0.5rem; padding: 0.3rem 0.5rem; font-size: 0.75rem; font-weight: 700; line-height: 1.2; cursor: pointer; }
.efficient_living-initials { border: 1px solid rgb(71 85 105); background: rgb(30 41 59); color: rgb(226 232 240); min-width: 2.8rem; }
.efficient_living-priority { display: inline-block; border: 0; border-radius: 0.5rem; padding: 0.3rem 0.5rem; font-size: 0.75rem; font-weight: 700; line-height: 1.2; cursor: pointer; color: rgb(226 232 240); background: rgb(148 163 184 / 0.15); }
.efficient_living-badge-pending { background: rgb(250 204 21 / 0.2); color: rgb(161 98 7); }
.efficient_living-badge-accepted { background: rgb(34 197 94 / 0.2); color: rgb(21 128 61); }
.efficient_living-badge-allocated { background: rgb(59 130 246 / 0.2); color: rgb(37 99 235); }
.efficient_living-badge-awaiting-further-information { background: rgb(245 158 11 / 0.2); color: rgb(180 83 9); }
.efficient_living-badge-completed { background: rgb(16 185 129 / 0.2); color: rgb(5 150 105); }
.efficient_living-status-menu,
.efficient_living-initials-menu { position: fixed; z-index: 9999; display: flex; min-width: 90px; flex-direction: column; gap: 2px; border-radius: 0.5rem; border: 1px solid rgb(51 65 85); background: rgb(30 41 59); padding: 0.25rem; box-shadow: 0 10px 20px rgb(15 23 42 / 0.25); }
.efficient_living-status-menu[hidden],
.efficient_living-initials-menu[hidden] { display: none !important; }
.efficient_living-status-option,
.efficient_living-initials-option { border: 0; border-radius: 0.375rem; background: transparent; padding: 0.35rem 0.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(226 232 240); cursor: pointer; }
.efficient_living-status-option:hover,
.efficient_living-initials-option:hover { background: rgb(255 255 255 / 0.12); }
/* Shared with LBS list (lbs-list.js): menus use hidden */
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
    <script>
        (function() {
            var searchEl = document.getElementById('efficient_livingSearch');
            var table = document.getElementById('efficient_livingTable');
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
                if (!th || th.classList.contains('efficient_living-th-action')) return;
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
