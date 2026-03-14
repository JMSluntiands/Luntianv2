@extends('layouts.dashboard')

@section('title', 'LBS For Review')

@section('body_class', 'page-lbs-review')

@section('content')
    <div class="lbs-list-page">
        <div class="lbs-list-header">
            <div class="lbs-list-header-text">
                <h1 class="lbs-list-title">LBS For Review</h1>
                <p class="lbs-list-subtitle">View LBS jobs pending review.</p>
            </div>
            <div class="lbs-list-search-wrap">
                <label for="lbsSearch" class="lbs-search-label">Search</label>
                <div class="lbs-search-input-wrap">
                    <svg class="lbs-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="lbs-search-input" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search LBS jobs for review">
                </div>
            </div>
        </div>

        <div class="lbs-table-card">
            <div class="lbs-table-wrap">
                <table class="lbs-table" id="lbsTable">
                    <colgroup>
                        <col class="lbs-col-action">
                        <col class="lbs-col-log-date">
                        <col class="lbs-col-client">
                        <col class="lbs-col-client-name">
                        <col class="lbs-col-reference">
                        <col class="lbs-col-job-type">
                        <col class="lbs-col-priority">
                        <col class="lbs-col-staff">
                        <col class="lbs-col-checker">
                        <col class="lbs-col-status">
                        <col class="lbs-col-due-date">
                        <col class="lbs-col-completion">
                        <col class="lbs-col-complexity">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th lbs-th-action" data-sort=""><span>Action</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Log Date</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Client</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Client Name</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Reference</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Job Type</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Priority</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Staff</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Checker</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Status</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Due Date</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Completion Date</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Complexity</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $index => $job)
                            @php
                                $log = $job->log_date ? \Carbon\Carbon::parse($job->log_date, 'Asia/Manila') : null;
                                $logDate1 = $log ? $log->format('F j, Y') : '—';
                                $logDate2 = $log ? $log->format('g:i A') : '';

                                // Same due-date logic as main LBS list
                                $priorityText = $job->priority ?? '';
                                $priorityLower = strtolower($priorityText);

                                $due = null;
                                if ($log) {
                                    $start = $log->copy();

                                    $startOfDay = $start->copy()->setTime(8, 0, 0);
                                    $cutoff = $start->copy()->setTime(15, 0, 0);

                                    if ($start->lt($startOfDay)) {
                                        $start = $startOfDay;
                                    }

                                    $isTop = str_contains($priorityLower, 'top');
                                    if (!$isTop && $start->gt($cutoff)) {
                                        $start = $start->copy()->addDay()->setTime(8, 0, 0);
                                    }

                                    if ($isTop) {
                                        $due = $start->copy()->addHours(6);
                                    } else {
                                        $days = 0;
                                        if (preg_match('/(\d+)\s*day/', $priorityLower, $m)) {
                                            $days = (int) ($m[1] ?? 0);
                                        }
                                        if ($days > 0) {
                                            $due = $start->copy()->addDays($days);
                                        }
                                    }
                                }

                                $completion = $job->completion_date ? \Carbon\Carbon::parse($job->completion_date, 'Asia/Manila') : null;
                                $isOverdue = $due && !$completion && $due->lt(now('Asia/Manila'));

                                $dueDate1 = $due ? $due->format('F j, Y') : '—';
                                $dueDate2 = $due ? $due->format('g:i A') : '';
                                $completionDate1 = $completion ? $completion->format('F j, Y') : '—';
                                $completionDate2 = $completion ? $completion->format('g:i A') : '';
                                $completionText = $completion ? $completionDate1 . ' ' . $completionDate2 : '—';

                                $priorityBg = $priorityColors[$priorityText] ?? null;

                                $status = $job->job_status ?? 'For Review';
                                $statusBg = $statusColors[$status] ?? null;

                                $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                                $complexity = max(0, min(5, $complexity));
                            @endphp
                            <tr class="lbs-data-row" data-job-id="{{ $job->job_id }}" data-update-url="{{ route('lbs.job.update', ['id' => $job->job_id]) }}">
                                <td class="lbs-td lbs-td-action">
                                    <div class="lbs-action-btns">
                                        <button type="button" class="lbs-action-icon lbs-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </button>
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}" class="lbs-action-icon lbs-action-view" title="View" aria-label="View job {{ $job->job_reference_no }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <button type="button" class="lbs-action-icon lbs-action-expand" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row>
                                            <svg class="lbs-expand-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-log-date" data-label="Log Date" data-sort="{{ $job->log_date }}">
                                    <span class="lbs-date-line1">{{ $logDate1 }}</span>
                                    @if($logDate2)<span class="lbs-date-line2">{{ $logDate2 }}</span>@endif
                                </td>
                                <td class="lbs-td lbs-td-client" data-label="Client">
                                    <span class="lbs-client-name">{{ $job->client_account_name ?? $job->client_code ?? '—' }}</span>
                                    <span class="lbs-client-project">{{ $job->ncc_compliance ?? '' }}</span>
                                </td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Client Name">
                                    {{ $job->client_code ?? 'LBS' }}
                                </td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Reference">
                                    {{ $job->job_reference_no ?? $job->reference }}
                                </td>
                                <td class="lbs-td lbs-td-job-type" data-label="Job Type">
                                    <span class="lbs-job-line1">{{ $job->job_type }}</span>
                                    <span class="lbs-job-line2">{{ $job->job_request_id }}</span>
                                </td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Priority">
                                    <span
                                        class="lbs-priority"
                                        @if($priorityBg)
                                            style="background-color: {{ $priorityBg }};"
                                        @endif
                                    >
                                        {{ $priorityText }}
                                    </span>
                                </td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Staff">
                                    <div class="lbs-initials-wrap" data-initials-wrap data-role="staff">
                                        <button type="button" class="lbs-initials lbs-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">
                                            {{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}
                                        </button>
                                        <div class="lbs-initials-menu" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JS">JS</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Checker">
                                    <div class="lbs-initials-wrap" data-initials-wrap data-role="checker">
                                        <button type="button" class="lbs-initials lbs-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">
                                            {{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}
                                        </button>
                                        <div class="lbs-initials-menu" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JS">JS</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Status">
                                    <div class="lbs-status-wrap" data-status-wrap>
                                        <button
                                            type="button"
                                            class="lbs-badge lbs-badge-for-review lbs-status-trigger"
                                            data-status-trigger
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                            data-reference="{{ $job->job_reference_no }}"
                                        >
                                            {{ $status }}
                                        </button>
                                        <div class="lbs-status-menu" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="For Email Confirmation">For Email Confirmation</button>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="Cancelled">Cancelled</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-due" data-label="Due Date" data-sort="{{ $due ? $due->format('Y-m-d H:i:s') : '' }}" data-overdue="{{ $isOverdue ? '1' : '0' }}">
                                    <span class="lbs-date-line1 {{ $isOverdue ? 'lbs-overdue' : '' }}">{{ $dueDate1 }}</span>
                                    @if($dueDate2)
                                        <span class="lbs-date-line2">{{ $dueDate2 }}</span>
                                        @if($isOverdue)
                                            <span class="lbs-overdue">(Overdue)</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="lbs-td lbs-td-due" data-label="Completion Date">
                                    <span class="lbs-date-line1">{{ $completionDate1 }}</span>
                                    @if($completionDate2)
                                        <span class="lbs-date-line2">{{ $completionDate2 }}</span>
                                    @endif
                                </td>
                                <td class="lbs-td lbs-td-nowrap" data-sort="{{ $complexity }}" data-label="Complexity">
                                    <span class="lbs-stars" data-rating="{{ $complexity }}" aria-label="{{ $complexity }} out of 5">
                                        @include('lbs.partials.stars', ['rating' => $complexity])
                                    </span>
                                </td>
                            </tr>
                            <tr class="lbs-row-detail" id="lbs-detail-{{ $index }}" hidden>
                                <td colspan="13" class="lbs-td-detail">
                                    <div class="lbs-detail-panel">
                                        <div class="lbs-detail-grid">
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Log Date</span><span class="lbs-detail-value">{{ $logDate1 }} {{ $logDate2 }}</span></div>
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Client</span><span class="lbs-detail-value">{{ $job->client_account_name ?? $job->client_code ?? '—' }} @if($job->ncc_compliance) · {{ $job->ncc_compliance }} @endif</span></div>
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Client Name</span><span class="lbs-detail-value">{{ $job->client_code ?? 'LBS' }}</span></div>
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Reference</span><span class="lbs-detail-value">{{ $job->job_reference_no ?? $job->reference }}</span></div>
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Job Type</span><span class="lbs-detail-value">{{ $job->job_type }} @if($job->job_request_id) · {{ $job->job_request_id }} @endif</span></div>
                                            <div class="lbs-detail-item">
                                                <span class="lbs-detail-label">Priority</span>
                                                <span class="lbs-detail-value">
                                                    <span
                                                        class="lbs-priority"
                                                        @if($priorityBg)
                                                            style="background-color: {{ $priorityBg }};"
                                                        @endif
                                                    >
                                                        {{ $priorityText }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="lbs-detail-item lbs-detail-item-staff"><span class="lbs-detail-label">Staff</span><span class="lbs-detail-value"><span class="lbs-initials lbs-detail-staff-badge">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</span></span></div>
                                            <div class="lbs-detail-item lbs-detail-item-checker"><span class="lbs-detail-label">Checker</span><span class="lbs-detail-value"><span class="lbs-initials lbs-detail-checker-badge">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</span></span></div>
                                            <div class="lbs-detail-item">
                                                <span class="lbs-detail-label">Status</span>
                                                <span class="lbs-detail-value">
                                                    <span
                                                        class="lbs-detail-status-badge lbs-badge lbs-badge-for-review"
                                                    >
                                                        {{ $status }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="lbs-detail-item">
                                                <span class="lbs-detail-label">Due Date</span>
                                                <span class="lbs-detail-value">
                                                    {{ $dueDate1 }} {{ $dueDate2 }}
                                                    @if($isOverdue)
                                                        <br>
                                                        <span class="lbs-overdue">(Overdue)</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Completion Date</span><span class="lbs-detail-value">{{ $completionText }}</span></div>
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Complexity</span><span class="lbs-detail-value">@include('lbs.partials.stars', ['rating' => $complexity])</span></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="lbs-td" colspan="13" style="text-align:center; padding:1.5rem; color:#94a3b8;">
                                    No LBS jobs currently For Review.
                                </td>
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
        .lbs-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-lbs-review .content { padding-bottom: 0; }
        .lbs-list-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; }
        .lbs-list-header-text { min-width: 0; }
        .lbs-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .lbs-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .lbs-list-search-wrap { flex-shrink: 0; }
        .lbs-search-label { font-size: 0.75rem; font-weight: 600; color: #94a3b8; display: block; margin-bottom: 0.35rem; }
        .lbs-search-input-wrap { position: relative; display: flex; align-items: center; min-width: 260px; }
        .lbs-search-icon { position: absolute; left: 0.75rem; color: #64748b; pointer-events: none; }
        .lbs-search-input { width: 100%; padding: 0.5rem 0.875rem 0.5rem 2.25rem; font-size: 0.9rem; line-height: 1.4; border: 1px solid #334155; border-radius: 8px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; }
        .lbs-search-input::placeholder { color: #64748b; }
        .lbs-search-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        html[data-theme="light"] .lbs-search-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .lbs-search-input::placeholder { color: #94a3b8; }
        html[data-theme="light"] .lbs-search-icon { color: #94a3b8; }
        .lbs-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.15); max-width: 100%; }
        .lbs-table-wrap { overflow-x: auto; max-width: 100%; -webkit-overflow-scrolling: touch; }
        .lbs-table { width: 100%; min-width: 1320px; border-collapse: collapse; font-size: 0.875rem; table-layout: fixed; }
        .lbs-col-action { width: 110px; }
        .lbs-col-log-date { width: 140px; }
        .lbs-col-client { width: 200px; }
        .lbs-col-client-name { width: 90px; }
        .lbs-col-reference { width: 105px; }
        .lbs-col-job-type { width: 200px; }
        .lbs-col-priority { width: 150px; }
        .lbs-col-staff { width: 70px; }
        .lbs-col-checker { width: 70px; }
        .lbs-col-status { width: 200px; }
        .lbs-col-due-date { width: 155px; }
        .lbs-col-completion { width: 115px; }
        .lbs-col-complexity { width: 95px; }
        .lbs-th { text-align: left; padding: 0.75rem 1.25rem; white-space: nowrap; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; cursor: pointer; user-select: none; vertical-align: middle; }
        .lbs-th:hover { color: #e2e8f0; }
        .lbs-th .lbs-sort-icon { margin-left: 0.25rem; opacity: 0.6; font-size: 0.75rem; }
        .lbs-th[data-sort="asc"] .lbs-sort-icon, .lbs-th[data-sort="desc"] .lbs-sort-icon { font-size: 0; }
        .lbs-th[data-sort="asc"] .lbs-sort-icon::before { content: '↑'; font-size: 0.75rem; }
        .lbs-th[data-sort="desc"] .lbs-sort-icon::before { content: '↓'; font-size: 0.75rem; }
        .lbs-th:not([data-sort=""]) .lbs-sort-icon { opacity: 1; }
        .lbs-td { padding: 0.75rem 1rem; border-bottom: 1px solid #334155; color: #e2e8f0; vertical-align: middle; overflow: hidden; }
        .lbs-td-action { overflow: visible; text-align: center; white-space: nowrap; }
        .lbs-td-nowrap { white-space: nowrap; }
        .lbs-td-log-date, .lbs-td-client, .lbs-td-job-type, .lbs-td-due { white-space: normal; line-height: 1.35; }
        .lbs-td-log-date .lbs-date-line1, .lbs-td-due .lbs-date-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .lbs-td-log-date .lbs-date-line2, .lbs-td-due .lbs-date-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .lbs-td-client .lbs-client-name, .lbs-td-job-type .lbs-job-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .lbs-td-client .lbs-client-project, .lbs-td-job-type .lbs-job-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .lbs-td-due .lbs-overdue { display: block; font-size: 0.8125rem; color: #f87171; font-weight: 500; margin-top: 0.1rem; }
        .lbs-action-btns { display: flex; align-items: center; gap: 0.35rem; flex-wrap: nowrap; }
        .lbs-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer; transition: background 0.15s, color 0.15s; }
        .lbs-action-icon:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
        .lbs-action-icon svg { display: block; }
        .lbs-action-duplicate:hover { color: #93c5fd; background: rgba(44,82,139,0.25); }
        .lbs-action-view:hover { color: #86efac; background: rgba(34,197,94,0.15); }
        .lbs-action-expand:hover { color: #fbbf24; background: rgba(251,191,36,0.15); }
        .lbs-action-expand[aria-expanded="true"] .lbs-expand-icon { transform: rotate(180deg); }
        .lbs-expand-icon { display: block; transition: transform 0.2s ease; }
        .lbs-row-detail td { padding: 0; border-bottom: 1px solid #334155; vertical-align: top; }
        .lbs-td-detail { background: #0f172a !important; }
        .lbs-detail-panel { padding: 0; margin-left: 0; }
        .lbs-action-collapse:hover { color: #f87171; background: rgba(248,113,113,0.15); }
        .lbs-detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem 1.5rem; padding: 1.25rem 1.5rem; }
        .lbs-detail-item { display: flex; flex-direction: column; gap: 0.35rem; }
        .lbs-detail-label { font-size: 0.6875rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .lbs-detail-value { font-size: 0.9375rem; font-weight: 500; color: #e2e8f0; line-height: 1.4; }
        .lbs-detail-value .lbs-priority, .lbs-detail-value .lbs-initials, .lbs-detail-value .lbs-badge { margin-top: 0.1rem; }
        .lbs-badge { display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; }
        .lbs-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .lbs-badge-completed { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .lbs-badge-pending { background: rgba(234, 179, 8, 0.2); color: #fde047; }
        .lbs-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .lbs-badge-for-review { background: rgba(234, 179, 8, 0.2); color: #fde047; }
        .lbs-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .lbs-status-wrap { position: relative; display: inline-block; }
        .lbs-status-trigger { border: none; cursor: pointer; font-family: inherit; margin: 0; line-height: 1.3; }
        .lbs-status-trigger:hover { opacity: 0.9; }
        .lbs-status-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .lbs-status-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 90px; }
        .lbs-status-menu[hidden] { display: none !important; }
        .lbs-status-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .lbs-status-option:hover { background: rgba(255,255,255,0.08); }
        .lbs-status-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        html[data-theme="light"] .lbs-status-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .lbs-status-option { color: #1e293b; }
        html[data-theme="light"] .lbs-status-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .lbs-status-option:focus { background: rgba(44,82,139,0.12); }
        .lbs-priority { display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; white-space: nowrap; }
        .lbs-priority-high { background: #ea580c; color: #fff; }
        .lbs-priority-standard { background: #7c3aed; color: #fff; }
        .lbs-initials { display: inline-block; padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 600; border: 1px solid #475569; border-radius: 6px; background: rgba(30,41,59,0.5); color: #e2e8f0; }
        .lbs-initials-wrap { position: relative; display: inline-block; }
        .lbs-initials-trigger { cursor: pointer; margin: 0; font-family: inherit; }
        .lbs-initials-trigger:hover { opacity: 0.9; }
        .lbs-initials-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .lbs-initials-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 70px; }
        .lbs-initials-menu[hidden] { display: none !important; }
        .lbs-initials-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .lbs-initials-option:hover { background: rgba(255,255,255,0.08); }
        .lbs-initials-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        html[data-theme="light"] .lbs-initials-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .lbs-initials-option { color: #1e293b; }
        html[data-theme="light"] .lbs-initials-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .lbs-initials-option:focus { background: rgba(44,82,139,0.12); }
        .lbs-stars { display: inline-flex; align-items: center; }
        .lbs-star-filled { color: #eab308; }
        .lbs-star-empty { color: #64748b; opacity: 0.8; }
        .lbs-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        html[data-theme="light"] .lbs-table-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .lbs-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .lbs-th:hover { color: #334155; }
        html[data-theme="light"] .lbs-td { border-bottom-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .lbs-table tbody tr:hover { background: #f8fafc; }
        html[data-theme="light"] .lbs-action-icon { color: #64748b; }
        html[data-theme="light"] .lbs-action-icon:hover { color: #334155; background: #e2e8f0; }
        html[data-theme="light"] .lbs-client-name { color: #1e293b; }
        html[data-theme="light"] .lbs-td-log-date .lbs-date-line1, html[data-theme="light"] .lbs-td-due .lbs-date-line1 { color: #1e293b; }
        html[data-theme="light"] .lbs-td-log-date .lbs-date-line2, html[data-theme="light"] .lbs-td-due .lbs-date-line2 { color: #64748b; }
        html[data-theme="light"] .lbs-td-job-type .lbs-job-line1 { color: #1e293b; }
        html[data-theme="light"] .lbs-td-job-type .lbs-job-line2 { color: #64748b; }
        html[data-theme="light"] .lbs-initials { border-color: #cbd5e1; background: #f8fafc; color: #334155; }
        html[data-theme="light"] .lbs-overdue { color: #dc2626; }
        html[data-theme="light"] .lbs-star-filled { color: #ca8a04; }
        html[data-theme="light"] .lbs-star-empty { color: #94a3b8; }
        html[data-theme="light"] .lbs-row-detail td { border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .lbs-td-detail { background: #f8fafc !important; }
        html[data-theme="light"] .lbs-detail-label { color: #64748b; }
        html[data-theme="light"] .lbs-detail-value { color: #1e293b; }
        @media (max-width: 768px) { .lbs-list-header { margin-bottom: 1.25rem; flex-direction: column; align-items: stretch; } .lbs-list-search-wrap { width: 100%; } .lbs-search-input-wrap { min-width: 0; width: 100%; } .lbs-list-title { font-size: 1.25rem; } .lbs-list-subtitle { font-size: 0.875rem; } .lbs-table { min-width: 1320px; } .lbs-th { padding: 0.6rem 0.75rem; font-size: 0.8125rem; } .lbs-td { padding: 0.6rem 0.75rem; font-size: 0.8125rem; } .lbs-detail-grid { grid-template-columns: 1fr; gap: 0.5rem 0; padding: 1rem; } }
        @media (max-width: 480px) { .lbs-th { padding: 0.5rem 0.6rem; font-size: 0.75rem; } .lbs-td { padding: 0.5rem 0.6rem; font-size: 0.75rem; } .lbs-detail-grid { padding: 0.75rem; } }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
    <script>
        (function() {
            var searchEl = document.getElementById('lbsSearch');
            var table = document.getElementById('lbsTable');
            if (searchEl && table) {
                var tbody = table.querySelector('tbody');
                searchEl.addEventListener('input', function() {
                    var q = (this.value || '').trim().toLowerCase();
                    if (!tbody) return;
                    var rows = tbody.querySelectorAll('tr:not(.lbs-row-detail)');
                    rows.forEach(function(tr) {
                        var text = (tr.textContent || '').toLowerCase();
                        var match = !q || text.indexOf(q) !== -1;
                        tr.style.display = match ? '' : 'none';
                        var next = tr.nextElementSibling;
                        if (next && next.classList.contains('lbs-row-detail')) next.style.display = match ? '' : 'none';
                    });
                });
            }
            document.querySelectorAll('[data-expand-row]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var row = this.closest('tr');
                    var next = row.nextElementSibling;
                    var isDetail = next && next.classList.contains('lbs-row-detail');
                    if (!isDetail) return;
                    var open = next.hidden;
                    next.hidden = !open;
                    this.setAttribute('aria-expanded', open);
                    this.setAttribute('title', open ? 'Hide details' : 'View full row details below');
                });
            });
            document.querySelectorAll('[data-collapse-detail]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var detailRow = this.closest('tr.lbs-row-detail');
                    if (!detailRow) return;
                    var dataRow = detailRow.previousElementSibling;
                    if (dataRow) {
                        var expandBtn = dataRow.querySelector('[data-expand-row]');
                        if (expandBtn) { expandBtn.setAttribute('aria-expanded', 'false'); expandBtn.setAttribute('title', 'View full row details below'); }
                    }
                    detailRow.hidden = true;
                });
            });
            if (!table) return;
            var thead = table.querySelector('thead');
            thead.addEventListener('click', function(e) {
                var th = e.target.closest('th');
                if (!th || th.classList.contains('lbs-th-action')) return;
                var current = th.getAttribute('data-sort') || '';
                var next = current === 'asc' ? 'desc' : 'asc';
                thead.querySelectorAll('th').forEach(function(h) { h.setAttribute('data-sort', ''); });
                th.setAttribute('data-sort', next);
                var colIndex = Array.prototype.indexOf.call(thead.querySelectorAll('th'), th);
                var tbody = table.querySelector('tbody');
                var allRows = Array.from(tbody.querySelectorAll('tr'));
                var dataRows = allRows.filter(function(r) { return !r.classList.contains('lbs-row-detail'); });
                dataRows.sort(function(a, b) {
                    var aCell = a.children[colIndex], bCell = b.children[colIndex];
                    var aVal = (aCell && (aCell.getAttribute('data-sort') || aCell.textContent)) || '';
                    var bVal = (bCell && (bCell.getAttribute('data-sort') || bCell.textContent)) || '';
                    var aNum = parseFloat(aVal), bNum = parseFloat(bVal);
                    if (!isNaN(aNum) && !isNaN(bNum)) return next === 'asc' ? aNum - bNum : bNum - aNum;
                    if (next === 'asc') return String(aVal).localeCompare(String(bVal), undefined, { numeric: true });
                    return String(bVal).localeCompare(String(aVal), undefined, { numeric: true });
                });
                dataRows.forEach(function(r) {
                    tbody.appendChild(r);
                    var detail = r.nextElementSibling;
                    if (detail && detail.classList.contains('lbs-row-detail')) tbody.appendChild(detail);
                });
            });
        })();
    </script>
@endpush
