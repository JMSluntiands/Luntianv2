@extends('layouts.dashboard')

@section('title', 'LEADING ENERGY List')

@section('body_class', 'page-leading_energy-list')

@section('content')
    <div class="leading_energy-list-page">
        <div class="leading_energy-list-header">
            <div class="leading_energy-list-header-text">
                <h1 class="leading_energy-list-title">LEADING ENERGY List</h1>
                <p class="leading_energy-list-subtitle">View and manage all LEADING ENERGY jobs.</p>
            </div>
            <div class="leading_energy-list-search-wrap">
                <label for="leading_energySearch" class="leading_energy-search-label">Search</label>
                <div class="leading_energy-search-input-wrap">
                    <svg class="leading_energy-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="leading_energySearch" class="leading_energy-search-input" placeholder="Search by client, job number, email..." autocomplete="off" aria-label="Search LEADING ENERGY jobs">
                </div>
            </div>
        </div>

        <div class="leading_energy-table-card">
            <div class="leading_energy-table-wrap">
                <table class="leading_energy-table" id="leading_energyTable">
                    <colgroup>
                        <col class="leading_energy-col-action">
                        <col class="leading_energy-col-log-date">
                        <col class="leading_energy-col-client">
                        <col class="leading_energy-col-urgent">
                        <col class="leading_energy-col-job-type">
                        <col class="leading_energy-col-ncc">
                        <col class="leading_energy-col-job-number">
                        <col class="leading_energy-col-client-name">
                        <col class="leading_energy-col-client-email">
                        <col class="leading_energy-col-status">
                        <col class="leading_energy-col-assigned">
                        <col class="leading_energy-col-checked">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="leading_energy-th leading_energy-th-action" data-sort="">
                                <span>Action</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Log Date</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Client</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Urgent</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Job Type</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>NCC</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Job Number</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Client Name</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Client Email</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Status</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Assigned To</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="leading_energy-th" data-sort="">
                                <span>Checked By</span>
                                <span class="leading_energy-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="leading_energy-td leading_energy-td-action">
                                <div class="leading_energy-action-btns">
                                    <button type="button" class="leading_energy-action-icon leading_energy-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <a href="#" class="leading_energy-action-icon leading_energy-action-view" title="View" aria-label="View job 011298">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                            <td class="leading_energy-td leading_energy-td-log-date" data-label="Log Date">
                                <span class="leading_energy-date-line1">November 29, 2025</span>
                                <span class="leading_energy-date-line2">03:21 AM</span>
                            </td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Client">LEADING ENERGY01</td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Urgent">NO</td>
                            <td class="leading_energy-td leading_energy-td-job-type" data-label="Job Type">
                                <span class="leading_energy-job-line1">EA_LEADING ENERGY_1S</span>
                                <span class="leading_energy-job-line2">Prelim</span>
                            </td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="NCC">2022 Whole of Home (WOH)</td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Job Number">011298</td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Client Name">TEST</td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Client Email">admin@luntiands.com</td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Status">
                                <div class="leading_energy-status-wrap" data-status-wrap>
                                    <button type="button" class="leading_energy-badge leading_energy-badge-completed leading_energy-status-trigger" data-status-trigger aria-haspopup="true" aria-expanded="false" data-reference="011298">Completed</button>
                                    <div class="leading_energy-status-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="leading_energy-status-option" data-status-value="Pending">Pending</button>
                                        <button type="button" role="menuitem" class="leading_energy-status-option" data-status-value="Accepted">Accepted</button>
                                        <button type="button" role="menuitem" class="leading_energy-status-option" data-status-value="Allocated">Allocated</button>
                                        <button type="button" role="menuitem" class="leading_energy-status-option" data-status-value="Awaiting Further Information">Awaiting Further Information</button>
                                        <button type="button" role="menuitem" class="leading_energy-status-option" data-status-value="Completed">Completed</button>
                                    </div>
                                </div>
                            </td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Assigned To">
                                <div class="leading_energy-initials-wrap" data-initials-wrap data-role="assigned">
                                    <button type="button" class="leading_energy-initials leading_energy-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">AJS</button>
                                    <div class="leading_energy-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="JS">JS</button>
                                    </div>
                                </div>
                            </td>
                            <td class="leading_energy-td leading_energy-td-nowrap" data-label="Checked By">
                                <div class="leading_energy-initials-wrap" data-initials-wrap data-role="checker">
                                    <button type="button" class="leading_energy-initials leading_energy-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">JDR</button>
                                    <div class="leading_energy-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="leading_energy-initials-option" data-value="JS">JS</button>
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
    <style>
        .leading_energy-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-leading_energy-list .content { padding-bottom: 0; }
        .leading_energy-list-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; }
        .leading_energy-list-header-text { min-width: 0; }
        .leading_energy-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .leading_energy-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .leading_energy-list-search-wrap { flex-shrink: 0; }
        .leading_energy-search-label { font-size: 0.75rem; font-weight: 600; color: #94a3b8; display: block; margin-bottom: 0.35rem; }
        .leading_energy-search-input-wrap { position: relative; display: flex; align-items: center; min-width: 260px; }
        .leading_energy-search-icon { position: absolute; left: 0.75rem; color: #64748b; pointer-events: none; }
        .leading_energy-search-input { width: 100%; padding: 0.5rem 0.875rem 0.5rem 2.25rem; font-size: 0.9rem; line-height: 1.4; border: 1px solid #334155; border-radius: 8px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; }
        .leading_energy-search-input::placeholder { color: #64748b; }
        .leading_energy-search-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        html[data-theme="light"] .leading_energy-search-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .leading_energy-search-input::placeholder { color: #94a3b8; }
        html[data-theme="light"] .leading_energy-search-icon { color: #94a3b8; }
        .leading_energy-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.15); max-width: 100%; }
        .leading_energy-table-wrap { overflow-x: auto; max-width: 100%; -webkit-overflow-scrolling: touch; }
        .leading_energy-table { width: 100%; min-width: 1200px; border-collapse: collapse; font-size: 0.875rem; table-layout: fixed; }
        .leading_energy-col-action { width: 90px; }
        .leading_energy-col-log-date { width: 130px; }
        .leading_energy-col-client { width: 80px; }
        .leading_energy-col-urgent { width: 70px; }
        .leading_energy-col-job-type { width: 120px; }
        .leading_energy-col-ncc { width: 180px; }
        .leading_energy-col-job-number { width: 90px; }
        .leading_energy-col-client-name { width: 100px; }
        .leading_energy-col-client-email { width: 160px; }
        .leading_energy-col-status { width: 110px; }
        .leading_energy-col-assigned { width: 80px; }
        .leading_energy-col-checked { width: 80px; }
        .leading_energy-th { text-align: left; padding: 0.75rem 1rem; white-space: nowrap; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; cursor: pointer; user-select: none; vertical-align: middle; }
        .leading_energy-th:hover { color: #e2e8f0; }
        .leading_energy-th .leading_energy-sort-icon { margin-left: 0.25rem; opacity: 0.6; font-size: 0.75rem; }
        .leading_energy-th[data-sort="asc"] .leading_energy-sort-icon,
        .leading_energy-th[data-sort="desc"] .leading_energy-sort-icon { font-size: 0; }
        .leading_energy-th[data-sort="asc"] .leading_energy-sort-icon::before { content: '↑'; font-size: 0.75rem; }
        .leading_energy-th[data-sort="desc"] .leading_energy-sort-icon::before { content: '↓'; font-size: 0.75rem; }
        .leading_energy-th:not([data-sort=""]) .leading_energy-sort-icon { opacity: 1; }
        .leading_energy-th-action { cursor: default; }
        .leading_energy-td { padding: 0.75rem 1rem; border-bottom: 1px solid #334155; color: #e2e8f0; vertical-align: middle; overflow: hidden; }
        .leading_energy-td-action { overflow: visible; text-align: center; white-space: nowrap; }
        .leading_energy-td-nowrap { white-space: nowrap; }
        .leading_energy-td-log-date, .leading_energy-td-job-type { white-space: normal; line-height: 1.35; }
        .leading_energy-td-log-date .leading_energy-date-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .leading_energy-td-log-date .leading_energy-date-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .leading_energy-td-job-type .leading_energy-job-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .leading_energy-td-job-type .leading_energy-job-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .leading_energy-action-btns { display: flex; align-items: center; gap: 0.35rem; flex-wrap: nowrap; justify-content: center; }
        .leading_energy-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer; transition: background 0.15s, color 0.15s; text-decoration: none; }
        .leading_energy-action-icon:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
        .leading_energy-action-icon svg { display: block; pointer-events: none; }
        .leading_energy-action-duplicate:hover { color: #93c5fd; background: rgba(44,82,139,0.25); }
        .leading_energy-action-view:hover { color: #86efac; background: rgba(34,197,94,0.15); }
        .leading_energy-badge { display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; }
        .leading_energy-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .leading_energy-badge-completed { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .leading_energy-badge-pending { background: rgba(234, 179, 8, 0.2); color: #fde047; }
        .leading_energy-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .leading_energy-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .leading_energy-status-wrap { position: relative; display: inline-block; }
        .leading_energy-status-trigger { border: none; cursor: pointer; font-family: inherit; margin: 0; line-height: 1.3; }
        .leading_energy-status-trigger:hover { opacity: 0.9; }
        .leading_energy-status-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .leading_energy-status-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 90px; }
        .leading_energy-status-menu[hidden] { display: none !important; }
        .leading_energy-status-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .leading_energy-status-option:hover { background: rgba(255,255,255,0.08); }
        .leading_energy-status-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        .leading_energy-initials { display: inline-block; padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 600; border: 1px solid #475569; border-radius: 6px; background: rgba(30,41,59,0.5); color: #e2e8f0; }
        .leading_energy-initials-wrap { position: relative; display: inline-block; }
        .leading_energy-initials-trigger { cursor: pointer; margin: 0; font-family: inherit; }
        .leading_energy-initials-trigger:hover { opacity: 0.9; }
        .leading_energy-initials-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .leading_energy-initials-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 70px; }
        .leading_energy-initials-menu[hidden] { display: none !important; }
        .leading_energy-initials-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .leading_energy-initials-option:hover { background: rgba(255,255,255,0.08); }
        .leading_energy-initials-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        html[data-theme="light"] .leading_energy-status-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .leading_energy-status-option { color: #1e293b; }
        html[data-theme="light"] .leading_energy-status-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .leading_energy-status-option:focus { background: rgba(44,82,139,0.12); }
        html[data-theme="light"] .leading_energy-initials-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .leading_energy-initials { border-color: #cbd5e1; background: #f8fafc; color: #334155; }
        html[data-theme="light"] .leading_energy-initials-option { color: #1e293b; }
        html[data-theme="light"] .leading_energy-initials-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .leading_energy-initials-option:focus { background: rgba(44,82,139,0.12); }
        .leading_energy-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        html[data-theme="light"] .leading_energy-table-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .leading_energy-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .leading_energy-th:hover { color: #334155; }
        html[data-theme="light"] .leading_energy-td { border-bottom-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .leading_energy-table tbody tr:hover { background: #f8fafc; }
        html[data-theme="light"] .leading_energy-action-icon { color: #64748b; }
        html[data-theme="light"] .leading_energy-action-icon:hover { color: #334155; background: #e2e8f0; }
        html[data-theme="light"] .leading_energy-action-duplicate:hover { color: #2C528B; background: rgba(44,82,139,0.12); }
        html[data-theme="light"] .leading_energy-action-view:hover { color: #15803d; background: rgba(34,197,94,0.12); }
        html[data-theme="light"] .leading_energy-list-title { color: #1e293b; }
        html[data-theme="light"] .leading_energy-list-subtitle { color: #64748b; }
        html[data-theme="light"] .leading_energy-td-log-date .leading_energy-date-line1,
        html[data-theme="light"] .leading_energy-td-job-type .leading_energy-job-line1 { color: #1e293b; }
        html[data-theme="light"] .leading_energy-td-log-date .leading_energy-date-line2,
        html[data-theme="light"] .leading_energy-td-job-type .leading_energy-job-line2 { color: #64748b; }
        html[data-theme="light"] .leading_energy-badge-completed,
        html[data-theme="light"] .leading_energy-badge-accepted { color: #15803d; }
        html[data-theme="light"] .leading_energy-badge-pending { color: #a16207; }
        html[data-theme="light"] .leading_energy-badge-allocated { color: #2563eb; }
        html[data-theme="light"] .leading_energy-badge-awaiting-further-information { color: #b45309; }
        @media (max-width: 768px) {
            .leading_energy-list-header { margin-bottom: 1.25rem; flex-direction: column; align-items: stretch; }
            .leading_energy-list-search-wrap { width: 100%; }
            .leading_energy-search-input-wrap { min-width: 0; width: 100%; }
            .leading_energy-list-title { font-size: 1.25rem; }
            .leading_energy-list-subtitle { font-size: 0.875rem; }
            .leading_energy-table { min-width: 1200px; }
            .leading_energy-th { padding: 0.6rem 0.75rem; font-size: 0.8125rem; }
            .leading_energy-td { padding: 0.6rem 0.75rem; font-size: 0.8125rem; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var searchEl = document.getElementById('leading_energySearch');
            var table = document.getElementById('leading_energyTable');
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
            function closeAllStatusMenus() {
                document.querySelectorAll('.leading_energy-status-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-status-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            function closeAllInitialsMenus() {
                document.querySelectorAll('.leading_energy-initials-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-initials-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            document.querySelectorAll('[data-initials-wrap]').forEach(function(wrap) {
                var trigger = wrap.querySelector('[data-initials-trigger]');
                var menu = wrap.querySelector('.leading_energy-initials-menu');
                if (!trigger || !menu) return;
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (!menu.hidden) {
                        menu.hidden = true;
                        trigger.setAttribute('aria-expanded', 'false');
                        return;
                    }
                    closeAllStatusMenus();
                    closeAllInitialsMenus();
                    var rect = trigger.getBoundingClientRect();
                    menu.style.top = (rect.bottom + 4) + 'px';
                    menu.style.left = rect.left + 'px';
                    menu.style.minWidth = Math.max(rect.width, 70) + 'px';
                    menu.hidden = false;
                    trigger.setAttribute('aria-expanded', 'true');
                });
                menu.querySelectorAll('.leading_energy-initials-option').forEach(function(opt) {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        trigger.textContent = this.getAttribute('data-value');
                        menu.hidden = true;
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                });
            });
            document.querySelectorAll('[data-status-wrap]').forEach(function(wrap) {
                var trigger = wrap.querySelector('[data-status-trigger]');
                var menu = wrap.querySelector('.leading_energy-status-menu');
                var statusClasses = ['leading_energy-badge-pending', 'leading_energy-badge-accepted', 'leading_energy-badge-allocated', 'leading_energy-badge-awaiting-further-information', 'leading_energy-badge-completed'];
                if (!trigger || !menu) return;
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (!menu.hidden) {
                        menu.hidden = true;
                        trigger.setAttribute('aria-expanded', 'false');
                        return;
                    }
                    closeAllStatusMenus();
                    closeAllInitialsMenus();
                    var rect = trigger.getBoundingClientRect();
                    menu.style.top = (rect.bottom + 4) + 'px';
                    menu.style.left = rect.left + 'px';
                    menu.style.minWidth = Math.max(rect.width, 90) + 'px';
                    menu.hidden = false;
                    trigger.setAttribute('aria-expanded', 'true');
                });
                menu.querySelectorAll('.leading_energy-status-option').forEach(function(opt) {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var val = this.getAttribute('data-status-value');
                        var badgeClass = 'leading_energy-badge-' + val.toLowerCase().replace(/\s+/g, '-');
                        statusClasses.forEach(function(c) { trigger.classList.remove(c); });
                        trigger.classList.add(badgeClass);
                        trigger.textContent = val;
                        menu.hidden = true;
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                });
            });
            document.addEventListener('click', function() { closeAllStatusMenus(); closeAllInitialsMenus(); });
            if (!table) return;
            var thead = table.querySelector('thead');
            thead.addEventListener('click', function(e) {
                var th = e.target.closest('th');
                if (!th || th.classList.contains('leading_energy-th-action')) return;
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
