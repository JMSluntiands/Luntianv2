@extends('layouts.dashboard')

@section('title', 'CSP List')

@section('body_class', 'page-csp-list')

@section('content')
    <div class="csp-list-page">
        <div class="csp-list-header">
            <div class="csp-list-header-text">
                <h1 class="csp-list-title">CSP List</h1>
                <p class="csp-list-subtitle">View and manage all CSP jobs.</p>
            </div>
            <div class="csp-list-search-wrap">
                <label for="cspSearch" class="csp-search-label">Search</label>
                <div class="csp-search-input-wrap">
                    <svg class="csp-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="cspSearch" class="csp-search-input" placeholder="Search by client, job number, email..." autocomplete="off" aria-label="Search CSP jobs">
                </div>
            </div>
        </div>

        <div class="csp-table-card">
            <div class="csp-table-wrap">
                <table class="csp-table" id="cspTable">
                    <colgroup>
                        <col class="csp-col-action">
                        <col class="csp-col-log-date">
                        <col class="csp-col-client">
                        <col class="csp-col-urgent">
                        <col class="csp-col-job-type">
                        <col class="csp-col-ncc">
                        <col class="csp-col-job-number">
                        <col class="csp-col-client-name">
                        <col class="csp-col-client-email">
                        <col class="csp-col-status">
                        <col class="csp-col-assigned">
                        <col class="csp-col-checked">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="csp-th csp-th-action" data-sort="">
                                <span>Action</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Log Date</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Client</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Urgent</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Job Type</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>NCC</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Job Number</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Client Name</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Client Email</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Status</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Assigned To</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="csp-th" data-sort="">
                                <span>Checked By</span>
                                <span class="csp-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="csp-td csp-td-action">
                                <div class="csp-action-btns">
                                    <button type="button" class="csp-action-icon csp-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <a href="#" class="csp-action-icon csp-action-view" title="View" aria-label="View job 011298">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                            <td class="csp-td csp-td-log-date" data-label="Log Date">
                                <span class="csp-date-line1">November 29, 2025</span>
                                <span class="csp-date-line2">03:21 AM</span>
                            </td>
                            <td class="csp-td csp-td-nowrap" data-label="Client">CSP01</td>
                            <td class="csp-td csp-td-nowrap" data-label="Urgent">NO</td>
                            <td class="csp-td csp-td-job-type" data-label="Job Type">
                                <span class="csp-job-line1">EA_CSP_1S</span>
                                <span class="csp-job-line2">Prelim</span>
                            </td>
                            <td class="csp-td csp-td-nowrap" data-label="NCC">2022 Whole of Home (WOH)</td>
                            <td class="csp-td csp-td-nowrap" data-label="Job Number">011298</td>
                            <td class="csp-td csp-td-nowrap" data-label="Client Name">TEST</td>
                            <td class="csp-td csp-td-nowrap" data-label="Client Email">admin@luntiands.com</td>
                            <td class="csp-td csp-td-nowrap" data-label="Status">
                                <div class="csp-status-wrap" data-status-wrap>
                                    <button type="button" class="csp-badge csp-badge-completed csp-status-trigger" data-status-trigger aria-haspopup="true" aria-expanded="false" data-reference="011298">Completed</button>
                                    <div class="csp-status-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="csp-status-option" data-status-value="Pending">Pending</button>
                                        <button type="button" role="menuitem" class="csp-status-option" data-status-value="Accepted">Accepted</button>
                                        <button type="button" role="menuitem" class="csp-status-option" data-status-value="Allocated">Allocated</button>
                                        <button type="button" role="menuitem" class="csp-status-option" data-status-value="Awaiting Further Information">Awaiting Further Information</button>
                                        <button type="button" role="menuitem" class="csp-status-option" data-status-value="Completed">Completed</button>
                                    </div>
                                </div>
                            </td>
                            <td class="csp-td csp-td-nowrap" data-label="Assigned To">
                                <div class="csp-initials-wrap" data-initials-wrap data-role="assigned">
                                    <button type="button" class="csp-initials csp-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">AJS</button>
                                    <div class="csp-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="JS">JS</button>
                                    </div>
                                </div>
                            </td>
                            <td class="csp-td csp-td-nowrap" data-label="Checked By">
                                <div class="csp-initials-wrap" data-initials-wrap data-role="checker">
                                    <button type="button" class="csp-initials csp-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">JDR</button>
                                    <div class="csp-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="csp-initials-option" data-value="JS">JS</button>
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
        .csp-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-csp-list .content { padding-bottom: 0; }
        .csp-list-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; }
        .csp-list-header-text { min-width: 0; }
        .csp-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .csp-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .csp-list-search-wrap { flex-shrink: 0; }
        .csp-search-label { font-size: 0.75rem; font-weight: 600; color: #94a3b8; display: block; margin-bottom: 0.35rem; }
        .csp-search-input-wrap { position: relative; display: flex; align-items: center; min-width: 260px; }
        .csp-search-icon { position: absolute; left: 0.75rem; color: #64748b; pointer-events: none; }
        .csp-search-input { width: 100%; padding: 0.5rem 0.875rem 0.5rem 2.25rem; font-size: 0.9rem; line-height: 1.4; border: 1px solid #334155; border-radius: 8px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; }
        .csp-search-input::placeholder { color: #64748b; }
        .csp-search-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        html[data-theme="light"] .csp-search-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .csp-search-input::placeholder { color: #94a3b8; }
        html[data-theme="light"] .csp-search-icon { color: #94a3b8; }
        .csp-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.15); max-width: 100%; }
        .csp-table-wrap { overflow-x: auto; max-width: 100%; -webkit-overflow-scrolling: touch; }
        .csp-table { width: 100%; min-width: 1200px; border-collapse: collapse; font-size: 0.875rem; table-layout: fixed; }
        .csp-col-action { width: 90px; }
        .csp-col-log-date { width: 130px; }
        .csp-col-client { width: 80px; }
        .csp-col-urgent { width: 70px; }
        .csp-col-job-type { width: 120px; }
        .csp-col-ncc { width: 180px; }
        .csp-col-job-number { width: 90px; }
        .csp-col-client-name { width: 100px; }
        .csp-col-client-email { width: 160px; }
        .csp-col-status { width: 110px; }
        .csp-col-assigned { width: 80px; }
        .csp-col-checked { width: 80px; }
        .csp-th { text-align: left; padding: 0.75rem 1rem; white-space: nowrap; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; cursor: pointer; user-select: none; vertical-align: middle; }
        .csp-th:hover { color: #e2e8f0; }
        .csp-th .csp-sort-icon { margin-left: 0.25rem; opacity: 0.6; font-size: 0.75rem; }
        .csp-th[data-sort="asc"] .csp-sort-icon,
        .csp-th[data-sort="desc"] .csp-sort-icon { font-size: 0; }
        .csp-th[data-sort="asc"] .csp-sort-icon::before { content: '↑'; font-size: 0.75rem; }
        .csp-th[data-sort="desc"] .csp-sort-icon::before { content: '↓'; font-size: 0.75rem; }
        .csp-th:not([data-sort=""]) .csp-sort-icon { opacity: 1; }
        .csp-th-action { cursor: default; }
        .csp-td { padding: 0.75rem 1rem; border-bottom: 1px solid #334155; color: #e2e8f0; vertical-align: middle; overflow: hidden; }
        .csp-td-action { overflow: visible; text-align: center; white-space: nowrap; }
        .csp-td-nowrap { white-space: nowrap; }
        .csp-td-log-date, .csp-td-job-type { white-space: normal; line-height: 1.35; }
        .csp-td-log-date .csp-date-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .csp-td-log-date .csp-date-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .csp-td-job-type .csp-job-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .csp-td-job-type .csp-job-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .csp-action-btns { display: flex; align-items: center; gap: 0.35rem; flex-wrap: nowrap; justify-content: center; }
        .csp-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer; transition: background 0.15s, color 0.15s; text-decoration: none; }
        .csp-action-icon:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
        .csp-action-icon svg { display: block; pointer-events: none; }
        .csp-action-duplicate:hover { color: #93c5fd; background: rgba(44,82,139,0.25); }
        .csp-action-view:hover { color: #86efac; background: rgba(34,197,94,0.15); }
        .csp-badge { display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; }
        .csp-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .csp-badge-completed { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .csp-badge-pending { background: rgba(234, 179, 8, 0.2); color: #fde047; }
        .csp-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .csp-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .csp-status-wrap { position: relative; display: inline-block; }
        .csp-status-trigger { border: none; cursor: pointer; font-family: inherit; margin: 0; line-height: 1.3; }
        .csp-status-trigger:hover { opacity: 0.9; }
        .csp-status-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .csp-status-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 90px; }
        .csp-status-menu[hidden] { display: none !important; }
        .csp-status-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .csp-status-option:hover { background: rgba(255,255,255,0.08); }
        .csp-status-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        .csp-initials { display: inline-block; padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 600; border: 1px solid #475569; border-radius: 6px; background: rgba(30,41,59,0.5); color: #e2e8f0; }
        .csp-initials-wrap { position: relative; display: inline-block; }
        .csp-initials-trigger { cursor: pointer; margin: 0; font-family: inherit; }
        .csp-initials-trigger:hover { opacity: 0.9; }
        .csp-initials-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .csp-initials-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 70px; }
        .csp-initials-menu[hidden] { display: none !important; }
        .csp-initials-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .csp-initials-option:hover { background: rgba(255,255,255,0.08); }
        .csp-initials-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        html[data-theme="light"] .csp-status-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .csp-status-option { color: #1e293b; }
        html[data-theme="light"] .csp-status-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .csp-status-option:focus { background: rgba(44,82,139,0.12); }
        html[data-theme="light"] .csp-initials-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .csp-initials { border-color: #cbd5e1; background: #f8fafc; color: #334155; }
        html[data-theme="light"] .csp-initials-option { color: #1e293b; }
        html[data-theme="light"] .csp-initials-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .csp-initials-option:focus { background: rgba(44,82,139,0.12); }
        .csp-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        html[data-theme="light"] .csp-table-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .csp-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .csp-th:hover { color: #334155; }
        html[data-theme="light"] .csp-td { border-bottom-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .csp-table tbody tr:hover { background: #f8fafc; }
        html[data-theme="light"] .csp-action-icon { color: #64748b; }
        html[data-theme="light"] .csp-action-icon:hover { color: #334155; background: #e2e8f0; }
        html[data-theme="light"] .csp-action-duplicate:hover { color: #2C528B; background: rgba(44,82,139,0.12); }
        html[data-theme="light"] .csp-action-view:hover { color: #15803d; background: rgba(34,197,94,0.12); }
        html[data-theme="light"] .csp-list-title { color: #1e293b; }
        html[data-theme="light"] .csp-list-subtitle { color: #64748b; }
        html[data-theme="light"] .csp-td-log-date .csp-date-line1,
        html[data-theme="light"] .csp-td-job-type .csp-job-line1 { color: #1e293b; }
        html[data-theme="light"] .csp-td-log-date .csp-date-line2,
        html[data-theme="light"] .csp-td-job-type .csp-job-line2 { color: #64748b; }
        html[data-theme="light"] .csp-badge-completed,
        html[data-theme="light"] .csp-badge-accepted { color: #15803d; }
        html[data-theme="light"] .csp-badge-pending { color: #a16207; }
        html[data-theme="light"] .csp-badge-allocated { color: #2563eb; }
        html[data-theme="light"] .csp-badge-awaiting-further-information { color: #b45309; }
        @media (max-width: 768px) {
            .csp-list-header { margin-bottom: 1.25rem; flex-direction: column; align-items: stretch; }
            .csp-list-search-wrap { width: 100%; }
            .csp-search-input-wrap { min-width: 0; width: 100%; }
            .csp-list-title { font-size: 1.25rem; }
            .csp-list-subtitle { font-size: 0.875rem; }
            .csp-table { min-width: 1200px; }
            .csp-th { padding: 0.6rem 0.75rem; font-size: 0.8125rem; }
            .csp-td { padding: 0.6rem 0.75rem; font-size: 0.8125rem; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var searchEl = document.getElementById('cspSearch');
            var table = document.getElementById('cspTable');
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
                document.querySelectorAll('.csp-status-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-status-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            function closeAllInitialsMenus() {
                document.querySelectorAll('.csp-initials-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-initials-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            document.querySelectorAll('[data-initials-wrap]').forEach(function(wrap) {
                var trigger = wrap.querySelector('[data-initials-trigger]');
                var menu = wrap.querySelector('.csp-initials-menu');
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
                menu.querySelectorAll('.csp-initials-option').forEach(function(opt) {
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
                var menu = wrap.querySelector('.csp-status-menu');
                var statusClasses = ['csp-badge-pending', 'csp-badge-accepted', 'csp-badge-allocated', 'csp-badge-awaiting-further-information', 'csp-badge-completed'];
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
                menu.querySelectorAll('.csp-status-option').forEach(function(opt) {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var val = this.getAttribute('data-status-value');
                        var badgeClass = 'csp-badge-' + val.toLowerCase().replace(/\s+/g, '-');
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
                if (!th || th.classList.contains('csp-th-action')) return;
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
