@extends('layouts.dashboard')

@section('title', 'LBS Completed')

@section('body_class', 'page-lbs-completed')

@section('content')
    <div class="lbs-list-page">
        <div class="lbs-list-header">
            <div class="lbs-list-header-text">
                <h1 class="lbs-list-title">LBS Completed</h1>
                <p class="lbs-list-subtitle">View completed LBS jobs.</p>
            </div>
            <div class="lbs-list-search-wrap">
                <label for="lbsSearch" class="lbs-search-label">Search</label>
                <div class="lbs-search-input-wrap">
                    <svg class="lbs-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="lbs-search-input" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search completed LBS jobs">
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
                        <tr>
                            <td class="lbs-td lbs-td-action">
                                <div class="lbs-action-btns">
                                    <button type="button" class="lbs-action-icon lbs-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <button type="button" class="lbs-action-icon lbs-action-view" title="View" aria-label="View">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" class="lbs-action-icon lbs-action-expand" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row>
                                        <svg class="lbs-expand-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="lbs-td lbs-td-log-date" data-label="Log Date">
                                <span class="lbs-date-line1">March 1, 2026</span>
                                <span class="lbs-date-line2">9:00 AM</span>
                            </td>
                            <td class="lbs-td lbs-td-client" data-label="Client">
                                <span class="lbs-client-name">Summit Homes Group</span>
                                <span class="lbs-client-project">2022 Whole of Home (WOH)</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Client Name">LBS</td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Reference">42250</td>
                            <td class="lbs-td lbs-td-job-type" data-label="Job Type">
                                <span class="lbs-job-line1">1S DB Base Model</span>
                                <span class="lbs-job-line2">1S Design Builder Model</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Priority"><span class="lbs-priority lbs-priority-high">High 1 day</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Staff"><span class="lbs-initials">SB</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Checker"><span class="lbs-initials">GM</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Status"><span class="lbs-badge lbs-badge-completed">Completed</span></td>
                            <td class="lbs-td lbs-td-due" data-label="Due Date">
                                <span class="lbs-date-line1">March 3, 2026</span>
                                <span class="lbs-date-line2">8:00 AM</span>
                            </td>
                            <td class="lbs-td lbs-td-due" data-label="Completion Date">
                                <span class="lbs-date-line1">March 2, 2026</span>
                                <span class="lbs-date-line2">5:30 PM</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-sort="4" data-label="Complexity"><span class="lbs-stars" data-rating="4" aria-label="4 out of 5">@include('lbs.partials.stars', ['rating' => 4])</span></td>
                        </tr>
                        <tr class="lbs-row-detail" id="lbs-detail-0" hidden>
                            <td colspan="13" class="lbs-td-detail">
                                <div class="lbs-detail-panel">
                                    <div class="lbs-detail-grid">
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Log Date</span><span class="lbs-detail-value">March 1, 2026 9:00 AM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client</span><span class="lbs-detail-value">Summit Homes Group · 2022 Whole of Home (WOH)</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client Name</span><span class="lbs-detail-value">LBS</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Reference</span><span class="lbs-detail-value">42250</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Job Type</span><span class="lbs-detail-value">1S DB Base Model · 1S Design Builder Model</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Priority</span><span class="lbs-detail-value"><span class="lbs-priority lbs-priority-high">High 1 day</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Staff</span><span class="lbs-detail-value"><span class="lbs-initials">SB</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Checker</span><span class="lbs-detail-value"><span class="lbs-initials">GM</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Status</span><span class="lbs-detail-value"><span class="lbs-badge lbs-badge-completed">Completed</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Due Date</span><span class="lbs-detail-value">March 3, 2026 8:00 AM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Completion Date</span><span class="lbs-detail-value">March 2, 2026 5:30 PM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Complexity</span><span class="lbs-detail-value">@include('lbs.partials.stars', ['rating' => 4])</span></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="lbs-td lbs-td-action">
                                <div class="lbs-action-btns">
                                    <button type="button" class="lbs-action-icon lbs-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <button type="button" class="lbs-action-icon lbs-action-view" title="View" aria-label="View">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" class="lbs-action-icon lbs-action-expand" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row>
                                        <svg class="lbs-expand-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="lbs-td lbs-td-log-date" data-label="Log Date">
                                <span class="lbs-date-line1">February 28, 2026</span>
                                <span class="lbs-date-line2">2:15 PM</span>
                            </td>
                            <td class="lbs-td lbs-td-client" data-label="Client">
                                <span class="lbs-client-name">Leigh Homes</span>
                                <span class="lbs-client-project">2022 Whole of Home (WOH)</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Client Name">LBS</td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Reference">42230</td>
                            <td class="lbs-td lbs-td-job-type" data-label="Job Type">
                                <span class="lbs-job-line1">2S DB Base Model</span>
                                <span class="lbs-job-line2">2S Design Builder Model</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Priority"><span class="lbs-priority lbs-priority-standard">Standard 2 days</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Staff"><span class="lbs-initials">JDR</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Checker"><span class="lbs-initials">JDR</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Status"><span class="lbs-badge lbs-badge-completed">Completed</span></td>
                            <td class="lbs-td lbs-td-due" data-label="Due Date">
                                <span class="lbs-date-line1">March 2, 2026</span>
                                <span class="lbs-date-line2">8:00 AM</span>
                            </td>
                            <td class="lbs-td lbs-td-due" data-label="Completion Date">
                                <span class="lbs-date-line1">March 1, 2026</span>
                                <span class="lbs-date-line2">11:00 AM</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-sort="3" data-label="Complexity"><span class="lbs-stars" data-rating="3" aria-label="3 out of 5">@include('lbs.partials.stars', ['rating' => 3])</span></td>
                        </tr>
                        <tr class="lbs-row-detail" id="lbs-detail-1" hidden>
                            <td colspan="13" class="lbs-td-detail">
                                <div class="lbs-detail-panel">
                                    <div class="lbs-detail-grid">
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Log Date</span><span class="lbs-detail-value">February 28, 2026 2:15 PM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client</span><span class="lbs-detail-value">Leigh Homes · 2022 Whole of Home (WOH)</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client Name</span><span class="lbs-detail-value">LBS</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Reference</span><span class="lbs-detail-value">42230</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Job Type</span><span class="lbs-detail-value">2S DB Base Model · 2S Design Builder Model</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Priority</span><span class="lbs-detail-value"><span class="lbs-priority lbs-priority-standard">Standard 2 days</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Staff</span><span class="lbs-detail-value"><span class="lbs-initials">JDR</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Checker</span><span class="lbs-detail-value"><span class="lbs-initials">JDR</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Status</span><span class="lbs-detail-value"><span class="lbs-badge lbs-badge-completed">Completed</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Due Date</span><span class="lbs-detail-value">March 2, 2026 8:00 AM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Completion Date</span><span class="lbs-detail-value">March 1, 2026 11:00 AM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Complexity</span><span class="lbs-detail-value">@include('lbs.partials.stars', ['rating' => 3])</span></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="lbs-td lbs-td-action">
                                <div class="lbs-action-btns">
                                    <button type="button" class="lbs-action-icon lbs-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <button type="button" class="lbs-action-icon lbs-action-view" title="View" aria-label="View">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" class="lbs-action-icon lbs-action-expand" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row>
                                        <svg class="lbs-expand-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="lbs-td lbs-td-log-date" data-label="Log Date">
                                <span class="lbs-date-line1">February 26, 2026</span>
                                <span class="lbs-date-line2">10:45 AM</span>
                            </td>
                            <td class="lbs-td lbs-td-client" data-label="Client">
                                <span class="lbs-client-name">Non Account</span>
                                <span class="lbs-client-project">2022 Whole of Home (WOH)</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Client Name">LBS</td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Reference">42201</td>
                            <td class="lbs-td lbs-td-job-type" data-label="Job Type">
                                <span class="lbs-job-line1">1S DB Base Model</span>
                                <span class="lbs-job-line2">1S Design Builder Model</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Priority"><span class="lbs-priority lbs-priority-high">High 1 day</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Staff"><span class="lbs-initials">PEP</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Checker"><span class="lbs-initials">GM</span></td>
                            <td class="lbs-td lbs-td-nowrap" data-label="Status"><span class="lbs-badge lbs-badge-completed">Completed</span></td>
                            <td class="lbs-td lbs-td-due" data-label="Due Date">
                                <span class="lbs-date-line1">February 27, 2026</span>
                                <span class="lbs-date-line2">8:00 AM</span>
                            </td>
                            <td class="lbs-td lbs-td-due" data-label="Completion Date">
                                <span class="lbs-date-line1">February 27, 2026</span>
                                <span class="lbs-date-line2">9:20 AM</span>
                            </td>
                            <td class="lbs-td lbs-td-nowrap" data-sort="4" data-label="Complexity"><span class="lbs-stars" data-rating="4" aria-label="4 out of 5">@include('lbs.partials.stars', ['rating' => 4])</span></td>
                        </tr>
                        <tr class="lbs-row-detail" id="lbs-detail-2" hidden>
                            <td colspan="13" class="lbs-td-detail">
                                <div class="lbs-detail-panel">
                                    <div class="lbs-detail-grid">
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Log Date</span><span class="lbs-detail-value">February 26, 2026 10:45 AM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client</span><span class="lbs-detail-value">Non Account · 2022 Whole of Home (WOH)</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Client Name</span><span class="lbs-detail-value">LBS</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Reference</span><span class="lbs-detail-value">42201</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Job Type</span><span class="lbs-detail-value">1S DB Base Model · 1S Design Builder Model</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Priority</span><span class="lbs-detail-value"><span class="lbs-priority lbs-priority-high">High 1 day</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Staff</span><span class="lbs-detail-value"><span class="lbs-initials">PEP</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Checker</span><span class="lbs-detail-value"><span class="lbs-initials">GM</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Status</span><span class="lbs-detail-value"><span class="lbs-badge lbs-badge-completed">Completed</span></span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Due Date</span><span class="lbs-detail-value">February 27, 2026 8:00 AM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Completion Date</span><span class="lbs-detail-value">February 27, 2026 9:20 AM</span></div>
                                        <div class="lbs-detail-item"><span class="lbs-detail-label">Complexity</span><span class="lbs-detail-value">@include('lbs.partials.stars', ['rating' => 4])</span></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
            var table = document.getElementById('lbsTable');
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
                        if (expandBtn) {
                            expandBtn.setAttribute('aria-expanded', 'false');
                            expandBtn.setAttribute('title', 'View full row details below');
                        }
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
                dataRows.forEach(function(r) {
                    tbody.appendChild(r);
                    var detail = r.nextElementSibling;
                    if (detail && detail.classList.contains('lbs-row-detail')) tbody.appendChild(detail);
                });
            });
        })();
    </script>
@endpush
