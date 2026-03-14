@extends('layouts.dashboard')

@section('title', 'LBS Mailbox')

@section('body_class', 'page-lbs-mailbox')

@section('content')
    <div class="lbs-list-page">
        <div class="lbs-list-header">
            <div class="lbs-list-header-text">
                <h1 class="lbs-list-title">LBS Mailbox</h1>
                <p class="lbs-list-subtitle">View LBS jobs waiting for email confirmation.</p>
            </div>
            <div class="lbs-list-search-wrap">
                <label for="lbsMailboxSearch" class="lbs-search-label">Search</label>
                <div class="lbs-search-input-wrap">
                    <svg class="lbs-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsMailboxSearch" class="lbs-search-input" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search LBS mailbox jobs">
                </div>
            </div>
        </div>

        <div class="lbs-table-card">
            <div class="lbs-table-wrap">
                <table class="lbs-table" id="lbsMailboxTable">
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

                                // Reuse the same due-date logic as the main LBS list
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
                                $completionText = $completion ? $completion->format('F j, Y g:i A') : '—';

                                $priorityBg = $priorityColors[$priorityText] ?? null;

                                $status = $job->job_status ?? 'For Email Confirmation';
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
                                            class="lbs-badge lbs-badge-mailbox lbs-status-trigger"
                                            data-status-trigger
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                            data-reference="{{ $job->job_reference_no }}"
                                        >
                                            {{ $status }}
                                        </button>
                                        <div class="lbs-status-menu" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="For Email Confirmation">For Email Confirmation</button>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="Pending">Pending</button>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="Accepted">Accepted</button>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="Allocated">Allocated</button>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="Awaiting Further Information">Awaiting Further Information</button>
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="Completed">Completed</button>
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
                                <td class="lbs-td lbs-td-nowrap" data-label="Completion Date">{{ $completionText }}</td>
                                <td class="lbs-td lbs-td-nowrap" data-sort="{{ $complexity }}" data-label="Complexity">
                                    <span class="lbs-stars" data-rating="{{ $complexity }}" aria-label="{{ $complexity }} out of 5">
                                        @include('lbs.partials.stars', ['rating' => $complexity])
                                    </span>
                                </td>
                            </tr>
                            <tr class="lbs-row-detail" id="lbs-mailbox-detail-{{ $index }}" hidden>
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
                                                        class="lbs-detail-status-badge lbs-badge lbs-badge-mailbox"
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
                                    No LBS jobs currently For Email Confirmation.
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
    <link rel="stylesheet" href="{{ asset('css/lbs-list.css') }}">
    <style>
        body.page-lbs-mailbox .content { padding-bottom: 0; }
        .lbs-badge-mailbox {
            background: #f97316;
            color: #0f172a;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
@endpush
