@extends('layouts.dashboard')

@section('title', 'LBS List')

@section('body_class', 'page-lbs-list')

@section('content')
    <div class="lbs-list-page">
        <div class="lbs-list-header">
            <div class="lbs-list-header-text">
                <h1 class="lbs-list-title">LBS List</h1>
                <p class="lbs-list-subtitle">View and manage all LBS jobs.</p>
            </div>
            <div class="lbs-list-search-wrap">
                <label for="lbsSearch" class="lbs-search-label">Search</label>
                <div class="lbs-search-input-wrap">
                    <svg class="lbs-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="lbs-search-input" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search LBS jobs">
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
                            <th class="lbs-th lbs-th-action" data-sort="">
                                <span>Action</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Log Date</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Client</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Client Name</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Reference</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Job Type</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Priority</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Staff</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Checker</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Status</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Due Date</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Completion Date</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th" data-sort="">
                                <span>Complexity</span>
                                <span class="lbs-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs ?? [] as $index => $job)
                            @php
                                $log = $job->log_date ? \Carbon\Carbon::parse($job->log_date, 'Asia/Manila') : null;
                                $logDate1 = $log ? $log->format('F j, Y') : '—';
                                $logDate2 = $log ? $log->format('g:i A') : '';

                                // Compute Due Date based on priority rules
                                $priorityText = $job->priority ?? '';
                                $priorityLower = strtolower($priorityText);

                                $due = null;
                                if ($log) {
                                    $start = $log->copy();

                                    // Working hours window: 8:00am to 3:00pm
                                    $startOfDay = $start->copy()->setTime(8, 0, 0);
                                    $cutoff = $start->copy()->setTime(15, 0, 0);

                                    // If logged before 8am, start counting from 8am
                                    if ($start->lt($startOfDay)) {
                                        $start = $startOfDay;
                                    }

                                    // For non-Top priorities, if logged after cutoff, count as next day 8am
                                    $isTop = str_contains($priorityLower, 'top');
                                    if (!$isTop && $start->gt($cutoff)) {
                                        $start = $start->copy()->addDay()->setTime(8, 0, 0);
                                    }

                                    if ($isTop) {
                                        // Top (COB) – 6 hours from start
                                        $due = $start->copy()->addHours(6);
                                    } else {
                                        // Extract number of days from priority label (e.g. "High (1 day)", "Standard (3 days)")
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

                                // Colors from Priority / Status tables (hex)
                                $priorityBg = $priorityColors[$priorityText] ?? null;

                                $status = $job->job_status ?? 'Allocated';
                                $statusBg  = $statusColors[$status] ?? null;
                                $statusLower = strtolower($status);
                                // Same flow as Edit Job Details modal: Allocated→Accepted/Processing; Accepted/Processing/Revised→For Checking; For Checking→For Review/Revised
                                $canEditStatus = in_array($statusLower, ['allocated', 'accepted', 'processing', 'revised', 'for checking', 'for review'], true);
                                $statusOptions = [];
                                if ($statusLower === 'allocated') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string)($s->name ?? ''));
                                        if (in_array($n, ['accepted', 'processing'], true)) $statusOptions[] = $s->name;
                                    }
                                } elseif (in_array($statusLower, ['accepted', 'processing', 'revised'], true)) {
                                    foreach ($statuses ?? [] as $s) {
                                        if (strtolower((string)($s->name ?? '')) === 'for checking') $statusOptions[] = $s->name;
                                    }
                                } elseif ($statusLower === 'for checking') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string)($s->name ?? ''));
                                        if (in_array($n, ['for review', 'revised', 'for email confirmation', 'cancelled'], true)) $statusOptions[] = $s->name;
                                    }
                                } elseif ($statusLower === 'for review') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string)($s->name ?? ''));
                                        if (in_array($n, ['for email confirmation', 'cancelled', 'revised', 'for checking'], true)) $statusOptions[] = $s->name;
                                    }
                                } else {
                                    foreach ($statuses ?? [] as $s) { $statusOptions[] = $s->name; }
                                }

                                $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                                $complexity = max(0, min(5, $complexity));
                            @endphp
                            <tr class="lbs-data-row" data-job-id="{{ $job->job_id }}" data-update-url="{{ route('lbs.job.update', ['id' => $job->job_id]) }}">
                                <td class="lbs-td lbs-td-action">
                                    <div class="lbs-action-btns">
                                        <a href="{{ route('lbs.add', ['duplicate' => $job->job_id]) }}" class="lbs-action-icon lbs-action-duplicate" title="Duplicate" aria-label="Duplicate job to Add New form">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </a>
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}" class="lbs-action-icon lbs-action-view" title="View" aria-label="View job {{ $job->job_reference_no }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
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
                                <td class="lbs-td lbs-td-nowrap" data-label="Client Name">{{ $job->client_code ?? 'LBS' }}</td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Reference" data-sort="{{ $job->job_reference_no }}">{{ $job->job_reference_no }}</td>
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
                                        <button type="button" class="lbs-initials lbs-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</button>
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
                                        <button type="button" class="lbs-initials lbs-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</button>
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
                                    @if($canEditStatus && count($statusOptions) > 0)
                                        <div class="lbs-status-wrap" data-status-wrap>
                                            <button
                                                type="button"
                                                class="lbs-badge lbs-status-trigger"
                                                @if($statusBg)
                                                    style="background-color: {{ $statusBg }};"
                                                @endif
                                                data-status-trigger
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                                data-reference="{{ $job->job_reference_no }}"
                                            >
                                                {{ $status }}
                                            </button>
                                            <div class="lbs-status-menu" role="menu" hidden>
                                                @foreach($statusOptions as $opt)
                                                    <button type="button" role="menuitem" class="lbs-status-option" data-status-value="{{ $opt }}">{{ $opt }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span
                                            class="lbs-badge lbs-status-badge-readonly"
                                            @if($statusBg)
                                                style="background-color: {{ $statusBg }};"
                                            @endif
                                            aria-disabled="true"
                                        >
                                            {{ $status }}
                                        </span>
                                    @endif
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
                                            <div class="lbs-detail-item"><span class="lbs-detail-label">Reference</span><span class="lbs-detail-value">{{ $job->job_reference_no }}</span></div>
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
                                                        class="lbs-detail-status-badge lbs-badge"
                                                        @if($statusBg)
                                                            style="background-color: {{ $statusBg }};"
                                                        @endif
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
                                    No LBS jobs found.
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
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
@endpush
