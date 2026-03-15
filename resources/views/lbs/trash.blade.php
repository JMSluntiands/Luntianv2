@extends('layouts.dashboard')

@section('title', 'LBS Archive')

@section('body_class', 'page-lbs-trash')

@section('content')
    <div class="lbs-list-page">
        <div class="lbs-list-header">
            <div class="lbs-list-header-text">
                <h1 class="lbs-list-title">LBS Archive</h1>
                <p class="lbs-list-subtitle">View archived LBS jobs.</p>
            </div>
            <div class="lbs-list-search-wrap">
                <label for="lbsSearch" class="lbs-search-label">Search</label>
                <div class="lbs-search-input-wrap">
                    <svg class="lbs-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="lbs-search-input" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search archived LBS jobs">
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

                                $priorityText = $job->priority ?? '';
                                $priorityLower = strtolower($priorityText);
                                $due = null;
                                if ($log) {
                                    $start = $log->copy();
                                    $startOfDay = $start->copy()->setTime(8, 0, 0);
                                    $cutoff = $start->copy()->setTime(15, 0, 0);
                                    if ($start->lt($startOfDay)) $start = $startOfDay;
                                    $isTop = str_contains($priorityLower, 'top');
                                    if (!$isTop && $start->gt($cutoff)) $start = $start->copy()->addDay()->setTime(8, 0, 0);
                                    if ($isTop) {
                                        $due = $start->copy()->addHours(6);
                                    } else {
                                        $days = 0;
                                        if (preg_match('/(\d+)\s*day/', $priorityLower, $m)) $days = (int) ($m[1] ?? 0);
                                        if ($days > 0) $due = $start->copy()->addDays($days);
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
                                $statusBg = $statusColors['Archived'] ?? null;
                                $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                                $complexity = max(0, min(5, $complexity));
                            @endphp
                            <tr>
                                <td class="lbs-td lbs-td-action">
                                    <div class="lbs-action-btns">
                                        <button type="button" class="lbs-action-icon lbs-action-restore lbs-restore-trigger" title="Restore" aria-label="Restore" data-restore-url="{{ route('lbs.job.restore', ['id' => $job->job_id]) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></button>
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}" class="lbs-action-icon lbs-action-view" title="View" aria-label="View"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                        <button type="button" class="lbs-action-icon lbs-action-expand" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row><svg class="lbs-expand-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-log-date" data-label="Log Date" data-sort="{{ $job->log_date }}"><span class="lbs-date-line1">{{ $logDate1 }}</span>@if($logDate2)<span class="lbs-date-line2">{{ $logDate2 }}</span>@endif</td>
                                <td class="lbs-td lbs-td-client" data-label="Client"><span class="lbs-client-name">{{ $job->client_account_name ?? $job->client_code ?? '—' }}</span><span class="lbs-client-project">{{ $job->ncc_compliance ?? '' }}</span></td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Client Name">{{ $job->client_code ?? 'LBS' }}</td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Reference" data-sort="{{ $job->job_reference_no }}">{{ $job->job_reference_no ?? $job->reference ?? '—' }}</td>
                                <td class="lbs-td lbs-td-job-type" data-label="Job Type"><span class="lbs-job-line1">{{ $job->job_type ?? '—' }}</span><span class="lbs-job-line2">{{ $job->job_request_id ?? '' }}</span></td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Priority"><span class="lbs-priority" @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif>{{ $priorityText ?: '—' }}</span></td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Staff"><span class="lbs-initials">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</span></td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Checker"><span class="lbs-initials">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</span></td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Status"><span class="lbs-badge lbs-badge-trashed">Archived</span></td>
                                <td class="lbs-td lbs-td-due" data-label="Due Date" data-sort="{{ $due ? $due->format('Y-m-d H:i:s') : '' }}"><span class="lbs-date-line1 {{ $isOverdue ? 'lbs-overdue' : '' }}">{{ $dueDate1 }}</span>@if($dueDate2)<span class="lbs-date-line2">{{ $dueDate2 }}</span>@endif @if($isOverdue)<span class="lbs-overdue">(Overdue)</span>@endif</td>
                                <td class="lbs-td lbs-td-nowrap" data-label="Completion Date"><span class="lbs-date-line1">{{ $completionDate1 }}</span>@if($completionDate2)<span class="lbs-date-line2">{{ $completionDate2 }}</span>@endif</td>
                                <td class="lbs-td lbs-td-nowrap" data-sort="{{ $complexity }}" data-label="Complexity"><span class="lbs-stars" data-rating="{{ $complexity }}" aria-label="{{ $complexity }} out of 5">@include('lbs.partials.stars', ['rating' => $complexity])</span></td>
                            </tr>
                            <tr class="lbs-row-detail" id="lbs-detail-{{ $index }}" hidden>
                                <td colspan="13" class="lbs-td-detail">
                                    <div class="lbs-detail-panel"><div class="lbs-detail-grid">
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Log Date</span><span class="lbs-detail-value">{{ $logDate1 }} {{ $logDate2 }}</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client</span><span class="lbs-detail-value">{{ $job->client_account_name ?? $job->client_code ?? '—' }} @if($job->ncc_compliance) · {{ $job->ncc_compliance }} @endif</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client Name</span><span class="lbs-detail-value">{{ $job->client_code ?? 'LBS' }}</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Reference</span><span class="lbs-detail-value">{{ $job->job_reference_no ?? $job->reference ?? '—' }}</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Job Type</span><span class="lbs-detail-value">{{ $job->job_type ?? '—' }} @if($job->job_request_id) · {{ $job->job_request_id }} @endif</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Priority</span><span class="lbs-detail-value"><span class="lbs-priority" @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif>{{ $priorityText ?: '—' }}</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Staff</span><span class="lbs-detail-value"><span class="lbs-initials">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Checker</span><span class="lbs-detail-value"><span class="lbs-initials">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Status</span><span class="lbs-detail-value"><span class="lbs-badge lbs-badge-trashed">Archived</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Due Date</span><span class="lbs-detail-value">{{ $dueDate1 }} {{ $dueDate2 }} @if($isOverdue)<br><span class="lbs-overdue">(Overdue)</span>@endif</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Completion Date</span><span class="lbs-detail-value">{{ $completionText }}</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Complexity</span><span class="lbs-detail-value">@include('lbs.partials.stars', ['rating' => $complexity])</span></div>
                                    </div></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="lbs-td" style="text-align:center; padding:1.5rem; color:#94a3b8;">No archived jobs.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lbs-trash-modal-overlay" id="lbsTrashRestoreModalOverlay" aria-hidden="true">
            <div class="lbs-trash-modal" role="dialog" aria-modal="true" aria-labelledby="lbsTrashRestoreModalTitle">
                <div class="lbs-trash-modal-header">
                    <h2 class="lbs-trash-modal-title" id="lbsTrashRestoreModalTitle">Restore job</h2>
                </div>
                <div class="lbs-trash-modal-body">
                    <div class="lbs-trash-restore-confirm" id="lbsTrashRestoreConfirm">
                        <p class="lbs-trash-modal-label">Restore this job back to the list? It will be set to Allocated.</p>
                    </div>
                    <div class="lbs-trash-restore-countdown" id="lbsTrashRestoreCountdown" hidden>
                        <p class="lbs-trash-countdown-text">Restoring in</p>
                        <div class="lbs-trash-countdown-number" id="lbsTrashRestoreCountdownNumber">3</div>
                        <p class="lbs-trash-countdown-cancel-hint">Click Cancel to abort</p>
                    </div>
                </div>
                <div class="lbs-trash-modal-footer">
                    <button type="button" class="lbs-trash-modal-btn lbs-trash-modal-btn-cancel" id="lbsTrashRestoreModalCancel">Cancel</button>
                    <button type="button" class="lbs-trash-modal-btn lbs-trash-modal-btn-primary lbs-trash-modal-btn-restore" id="lbsTrashRestoreModalConfirm"><span class="lbs-trash-restore-btn-text">Restore</span></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush

@push('scripts')
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

        (function restoreModal() {
            var overlay = document.getElementById('lbsTrashRestoreModalOverlay');
            var confirmBlock = document.getElementById('lbsTrashRestoreConfirm');
            var countdownBlock = document.getElementById('lbsTrashRestoreCountdown');
            var countdownNumber = document.getElementById('lbsTrashRestoreCountdownNumber');
            var cancelBtn = document.getElementById('lbsTrashRestoreModalCancel');
            var confirmBtn = document.getElementById('lbsTrashRestoreModalConfirm');
            var btnTextEl = confirmBtn && confirmBtn.querySelector('.lbs-trash-restore-btn-text');
            var countdownTimer = null;
            var pendingRestoreUrl = null;

            function resetRestoreModal() {
                if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
                if (confirmBlock) confirmBlock.hidden = false;
                if (countdownBlock) countdownBlock.hidden = true;
                if (confirmBtn) confirmBtn.disabled = false;
                if (btnTextEl) btnTextEl.textContent = 'Restore';
            }
            function closeRestoreModal() {
                if (overlay) { overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden', 'true'); }
                pendingRestoreUrl = null;
                resetRestoreModal();
            }

            document.addEventListener('click', function(e) {
                var trigger = e.target.closest('.lbs-restore-trigger');
                if (trigger) {
                    e.preventDefault();
                    pendingRestoreUrl = trigger.getAttribute('data-restore-url');
                    if (!pendingRestoreUrl) return;
                    resetRestoreModal();
                    if (overlay) { overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden', 'false'); }
                }
            });

            if (cancelBtn) cancelBtn.addEventListener('click', closeRestoreModal);
            if (overlay) overlay.addEventListener('click', function(e) { if (e.target === overlay) closeRestoreModal(); });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) closeRestoreModal();
            });

            if (confirmBtn && confirmBlock && countdownBlock && countdownNumber) {
                confirmBtn.addEventListener('click', function() {
                    if (!pendingRestoreUrl || countdownTimer) return;
                    confirmBlock.hidden = true;
                    countdownBlock.hidden = false;
                    confirmBtn.disabled = true;
                    if (btnTextEl) btnTextEl.textContent = 'Restoring...';
                    var count = 3;
                    countdownNumber.textContent = count;
                    countdownNumber.style.animation = 'none';
                    countdownNumber.offsetHeight;
                    countdownNumber.style.animation = '';
                    countdownTimer = setInterval(function() {
                        count--;
                        if (count <= 0) {
                            clearInterval(countdownTimer);
                            countdownTimer = null;
                            window.location.href = pendingRestoreUrl;
                            return;
                        }
                        countdownNumber.textContent = count;
                        countdownNumber.style.animation = 'none';
                        countdownNumber.offsetHeight;
                        countdownNumber.style.animation = '';
                    }, 1000);
                });
            }
        })();
    </script>
@endpush
