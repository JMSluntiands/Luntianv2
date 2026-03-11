@extends('layouts.dashboard')

@section('title', 'BPH List')

@section('body_class', 'page-bph-list')

@section('content')
    <div class="bph-list-page">
        <div class="bph-list-header">
            <div class="bph-list-header-text">
                <h1 class="bph-list-title">BPH List</h1>
                <p class="bph-list-subtitle">View and manage all BPH jobs.</p>
            </div>
            <div class="bph-list-search-wrap">
                <label for="bphSearch" class="bph-search-label">Search</label>
                <div class="bph-search-input-wrap">
                    <svg class="bph-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="bphSearch" class="bph-search-input" placeholder="Search by client, job number, email..." autocomplete="off" aria-label="Search BPH jobs">
                </div>
            </div>
        </div>

        <div class="bph-table-card">
            <div class="bph-table-wrap">
                <table class="bph-table" id="bphTable">
                    <colgroup>
                        <col class="bph-col-action">
                        <col class="bph-col-log-date">
                        <col class="bph-col-client">
                        <col class="bph-col-urgent">
                        <col class="bph-col-job-type">
                        <col class="bph-col-ncc">
                        <col class="bph-col-job-number">
                        <col class="bph-col-client-name">
                        <col class="bph-col-client-email">
                        <col class="bph-col-status">
                        <col class="bph-col-assigned">
                        <col class="bph-col-checked">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="bph-th bph-th-action" data-sort="">
                                <span>Action</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Log Date</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Client</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Urgent</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Job Type</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>NCC</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Job Number</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Client Name</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Client Email</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Status</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Assigned To</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="bph-th" data-sort="">
                                <span>Checked By</span>
                                <span class="bph-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="bph-td bph-td-action">
                                <div class="bph-action-btns">
                                    <button type="button" class="bph-action-icon bph-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <a href="#" class="bph-action-icon bph-action-view" title="View" aria-label="View job 011298">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                            <td class="bph-td bph-td-log-date" data-label="Log Date">
                                <span class="bph-date-line1">November 29, 2025</span>
                                <span class="bph-date-line2">03:21 AM</span>
                            </td>
                            <td class="bph-td bph-td-nowrap" data-label="Client">BPH01</td>
                            <td class="bph-td bph-td-nowrap" data-label="Urgent">NO</td>
                            <td class="bph-td bph-td-job-type" data-label="Job Type">
                                <span class="bph-job-line1">EA_BPH_1S</span>
                                <span class="bph-job-line2">Prelim</span>
                            </td>
                            <td class="bph-td bph-td-nowrap" data-label="NCC">2022 Whole of Home (WOH)</td>
                            <td class="bph-td bph-td-nowrap" data-label="Job Number">011298</td>
                            <td class="bph-td bph-td-nowrap" data-label="Client Name">TEST</td>
                            <td class="bph-td bph-td-nowrap" data-label="Client Email">admin@luntiands.com</td>
                            <td class="bph-td bph-td-nowrap" data-label="Status">
                                <div class="bph-status-wrap" data-status-wrap>
                                    <button type="button" class="bph-badge bph-badge-completed bph-status-trigger" data-status-trigger aria-haspopup="true" aria-expanded="false" data-reference="011298">Completed</button>
                                    <div class="bph-status-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="bph-status-option" data-status-value="Pending">Pending</button>
                                        <button type="button" role="menuitem" class="bph-status-option" data-status-value="Accepted">Accepted</button>
                                        <button type="button" role="menuitem" class="bph-status-option" data-status-value="Allocated">Allocated</button>
                                        <button type="button" role="menuitem" class="bph-status-option" data-status-value="Awaiting Further Information">Awaiting Further Information</button>
                                        <button type="button" role="menuitem" class="bph-status-option" data-status-value="Completed">Completed</button>
                                    </div>
                                </div>
                            </td>
                            <td class="bph-td bph-td-nowrap" data-label="Assigned To">
                                <div class="bph-initials-wrap" data-initials-wrap data-role="assigned">
                                    <button type="button" class="bph-initials bph-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">AJS</button>
                                    <div class="bph-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="JS">JS</button>
                                    </div>
                                </div>
                            </td>
                            <td class="bph-td bph-td-nowrap" data-label="Checked By">
                                <div class="bph-initials-wrap" data-initials-wrap data-role="checker">
                                    <button type="button" class="bph-initials bph-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">JDR</button>
                                    <div class="bph-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="bph-initials-option" data-value="JS">JS</button>
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
        .bph-list-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-bph-list .content { padding-bottom: 0; }
        .bph-list-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; }
        .bph-list-header-text { min-width: 0; }
        .bph-list-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .bph-list-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .bph-list-search-wrap { flex-shrink: 0; }
        .bph-search-label { font-size: 0.75rem; font-weight: 600; color: #94a3b8; display: block; margin-bottom: 0.35rem; }
        .bph-search-input-wrap { position: relative; display: flex; align-items: center; min-width: 260px; }
        .bph-search-icon { position: absolute; left: 0.75rem; color: #64748b; pointer-events: none; }
        .bph-search-input { width: 100%; padding: 0.5rem 0.875rem 0.5rem 2.25rem; font-size: 0.9rem; line-height: 1.4; border: 1px solid #334155; border-radius: 8px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; }
        .bph-search-input::placeholder { color: #64748b; }
        .bph-search-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        html[data-theme="light"] .bph-search-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .bph-search-input::placeholder { color: #94a3b8; }
        html[data-theme="light"] .bph-search-icon { color: #94a3b8; }
        .bph-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.15); max-width: 100%; }
        .bph-table-wrap { overflow-x: auto; max-width: 100%; -webkit-overflow-scrolling: touch; }
        .bph-table { width: 100%; min-width: 1200px; border-collapse: collapse; font-size: 0.875rem; table-layout: fixed; }
        .bph-col-action { width: 90px; }
        .bph-col-log-date { width: 130px; }
        .bph-col-client { width: 80px; }
        .bph-col-urgent { width: 70px; }
        .bph-col-job-type { width: 120px; }
        .bph-col-ncc { width: 180px; }
        .bph-col-job-number { width: 90px; }
        .bph-col-client-name { width: 100px; }
        .bph-col-client-email { width: 160px; }
        .bph-col-status { width: 110px; }
        .bph-col-assigned { width: 80px; }
        .bph-col-checked { width: 80px; }
        .bph-th { text-align: left; padding: 0.75rem 1rem; white-space: nowrap; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; cursor: pointer; user-select: none; vertical-align: middle; }
        .bph-th:hover { color: #e2e8f0; }
        .bph-th .bph-sort-icon { margin-left: 0.25rem; opacity: 0.6; font-size: 0.75rem; }
        .bph-th[data-sort="asc"] .bph-sort-icon,
        .bph-th[data-sort="desc"] .bph-sort-icon { font-size: 0; }
        .bph-th[data-sort="asc"] .bph-sort-icon::before { content: '↑'; font-size: 0.75rem; }
        .bph-th[data-sort="desc"] .bph-sort-icon::before { content: '↓'; font-size: 0.75rem; }
        .bph-th:not([data-sort=""]) .bph-sort-icon { opacity: 1; }
        .bph-th-action { cursor: default; }
        .bph-td { padding: 0.75rem 1rem; border-bottom: 1px solid #334155; color: #e2e8f0; vertical-align: middle; overflow: hidden; }
        .bph-td-action { overflow: visible; text-align: center; white-space: nowrap; }
        .bph-td-nowrap { white-space: nowrap; }
        .bph-td-log-date, .bph-td-job-type { white-space: normal; line-height: 1.35; }
        .bph-td-log-date .bph-date-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .bph-td-log-date .bph-date-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .bph-td-job-type .bph-job-line1 { display: block; font-weight: 500; color: #e2e8f0; }
        .bph-td-job-type .bph-job-line2 { display: block; font-size: 0.8125rem; color: #94a3b8; }
        .bph-action-btns { display: flex; align-items: center; gap: 0.35rem; flex-wrap: nowrap; justify-content: center; }
        .bph-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer; transition: background 0.15s, color 0.15s; text-decoration: none; }
        .bph-action-icon:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
        .bph-action-icon svg { display: block; pointer-events: none; }
        .bph-action-duplicate:hover { color: #93c5fd; background: rgba(44,82,139,0.25); }
        .bph-action-view:hover { color: #86efac; background: rgba(34,197,94,0.15); }
        .bph-badge { display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; }
        .bph-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .bph-badge-completed { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .bph-badge-pending { background: rgba(234, 179, 8, 0.2); color: #fde047; }
        .bph-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .bph-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .bph-status-wrap { position: relative; display: inline-block; }
        .bph-status-trigger { border: none; cursor: pointer; font-family: inherit; margin: 0; line-height: 1.3; }
        .bph-status-trigger:hover { opacity: 0.9; }
        .bph-status-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .bph-status-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 90px; }
        .bph-status-menu[hidden] { display: none !important; }
        .bph-status-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .bph-status-option:hover { background: rgba(255,255,255,0.08); }
        .bph-status-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        .bph-initials { display: inline-block; padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 600; border: 1px solid #475569; border-radius: 6px; background: rgba(30,41,59,0.5); color: #e2e8f0; }
        .bph-initials-wrap { position: relative; display: inline-block; }
        .bph-initials-trigger { cursor: pointer; margin: 0; font-family: inherit; }
        .bph-initials-trigger:hover { opacity: 0.9; }
        .bph-initials-trigger:focus { outline: none; box-shadow: 0 0 0 2px rgba(44,82,139,0.4); border-radius: 6px; }
        .bph-initials-menu { position: fixed; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; padding: 4px; display: flex; flex-direction: column; gap: 2px; min-width: 70px; }
        .bph-initials-menu[hidden] { display: none !important; }
        .bph-initials-option { display: block; width: 100%; padding: 0.4rem 0.6rem; font-size: 0.75rem; font-weight: 500; text-align: left; border: none; border-radius: 6px; background: transparent; color: #e2e8f0; cursor: pointer; font-family: inherit; }
        .bph-initials-option:hover { background: rgba(255,255,255,0.08); }
        .bph-initials-option:focus { outline: none; background: rgba(44,82,139,0.25); }
        html[data-theme="light"] .bph-status-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .bph-status-option { color: #1e293b; }
        html[data-theme="light"] .bph-status-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .bph-status-option:focus { background: rgba(44,82,139,0.12); }
        html[data-theme="light"] .bph-initials-menu { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .bph-initials { border-color: #cbd5e1; background: #f8fafc; color: #334155; }
        html[data-theme="light"] .bph-initials-option { color: #1e293b; }
        html[data-theme="light"] .bph-initials-option:hover { background: #f1f5f9; }
        html[data-theme="light"] .bph-initials-option:focus { background: rgba(44,82,139,0.12); }
        .bph-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        html[data-theme="light"] .bph-table-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .bph-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .bph-th:hover { color: #334155; }
        html[data-theme="light"] .bph-td { border-bottom-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .bph-table tbody tr:hover { background: #f8fafc; }
        html[data-theme="light"] .bph-action-icon { color: #64748b; }
        html[data-theme="light"] .bph-action-icon:hover { color: #334155; background: #e2e8f0; }
        html[data-theme="light"] .bph-action-duplicate:hover { color: #2C528B; background: rgba(44,82,139,0.12); }
        html[data-theme="light"] .bph-action-view:hover { color: #15803d; background: rgba(34,197,94,0.12); }
        html[data-theme="light"] .bph-list-title { color: #1e293b; }
        html[data-theme="light"] .bph-list-subtitle { color: #64748b; }
        html[data-theme="light"] .bph-td-log-date .bph-date-line1,
        html[data-theme="light"] .bph-td-job-type .bph-job-line1 { color: #1e293b; }
        html[data-theme="light"] .bph-td-log-date .bph-date-line2,
        html[data-theme="light"] .bph-td-job-type .bph-job-line2 { color: #64748b; }
        html[data-theme="light"] .bph-badge-completed,
        html[data-theme="light"] .bph-badge-accepted { color: #15803d; }
        html[data-theme="light"] .bph-badge-pending { color: #a16207; }
        html[data-theme="light"] .bph-badge-allocated { color: #2563eb; }
        html[data-theme="light"] .bph-badge-awaiting-further-information { color: #b45309; }
        @media (max-width: 768px) {
            .bph-list-header { margin-bottom: 1.25rem; flex-direction: column; align-items: stretch; }
            .bph-list-search-wrap { width: 100%; }
            .bph-search-input-wrap { min-width: 0; width: 100%; }
            .bph-list-title { font-size: 1.25rem; }
            .bph-list-subtitle { font-size: 0.875rem; }
            .bph-table { min-width: 1200px; }
            .bph-th { padding: 0.6rem 0.75rem; font-size: 0.8125rem; }
            .bph-td { padding: 0.6rem 0.75rem; font-size: 0.8125rem; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var searchEl = document.getElementById('bphSearch');
            var table = document.getElementById('bphTable');
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
                document.querySelectorAll('.bph-status-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-status-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            function closeAllInitialsMenus() {
                document.querySelectorAll('.bph-initials-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-initials-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            document.querySelectorAll('[data-initials-wrap]').forEach(function(wrap) {
                var trigger = wrap.querySelector('[data-initials-trigger]');
                var menu = wrap.querySelector('.bph-initials-menu');
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
                menu.querySelectorAll('.bph-initials-option').forEach(function(opt) {
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
                var menu = wrap.querySelector('.bph-status-menu');
                var statusClasses = ['bph-badge-pending', 'bph-badge-accepted', 'bph-badge-allocated', 'bph-badge-awaiting-further-information', 'bph-badge-completed'];
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
                menu.querySelectorAll('.bph-status-option').forEach(function(opt) {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var val = this.getAttribute('data-status-value');
                        var badgeClass = 'bph-badge-' + val.toLowerCase().replace(/\s+/g, '-');
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
                if (!th || th.classList.contains('bph-th-action')) return;
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
