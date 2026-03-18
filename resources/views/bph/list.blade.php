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
                        <tr class="bph-data-row" data-row-index="0">
                            <td class="bph-td bph-td-action">
                                <div class="bph-action-btns">
                                    <button type="button" class="bph-action-icon bph-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <a href="#" class="bph-action-icon bph-action-view" title="View" aria-label="View job 011298">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <button type="button" class="bph-action-icon bph-action-expand" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row data-row-index="0">
                                        <svg class="bph-expand-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                    </button>
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
                        <tr class="bph-row-detail" data-detail-index="0" hidden>
                            <td colspan="12" class="bph-row-detail-td">
                                <div class="bph-row-detail-inner">
                                    <div class="bph-row-detail-grid">
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Log Date</span>
                                            <span class="bph-row-detail-value">November 29, 2025 · 03:21 AM</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Client</span>
                                            <span class="bph-row-detail-value">BPH01</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Urgent</span>
                                            <span class="bph-row-detail-value">NO</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Job Type</span>
                                            <span class="bph-row-detail-value">EA_BPH_1S · Prelim</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">NCC</span>
                                            <span class="bph-row-detail-value">2022 Whole of Home (WOH)</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Job Number</span>
                                            <span class="bph-row-detail-value">011298</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Client Name</span>
                                            <span class="bph-row-detail-value">TEST</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Client Email</span>
                                            <span class="bph-row-detail-value">admin@luntiands.com</span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Status</span>
                                            <span class="bph-row-detail-value"><span class="bph-badge bph-badge-completed">Completed</span></span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Assigned To</span>
                                            <span class="bph-row-detail-value"><span class="bph-initials">AJS</span></span>
                                        </div>
                                        <div class="bph-row-detail-item">
                                            <span class="bph-row-detail-label">Checked By</span>
                                            <span class="bph-row-detail-value"><span class="bph-initials">JDR</span></span>
                                        </div>
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

            // Expand / collapse detail rows (similar behavior to LBS list)
            var expandButtons = table.querySelectorAll('[data-expand-row]');
            function closeAllDetails() {
                table.querySelectorAll('.bph-row-detail').forEach(function(row) {
                    row.hidden = true;
                });
                expandButtons.forEach(function(btn) {
                    btn.setAttribute('aria-expanded', 'false');
                });
            }
            expandButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var rowIndex = btn.getAttribute('data-row-index');
                    var detail = table.querySelector('.bph-row-detail[data-detail-index="' + rowIndex + '"]');
                    if (!detail) return;
                    var isOpen = !detail.hidden;
                    closeAllDetails();
                    if (!isOpen) {
                        detail.hidden = false;
                        btn.setAttribute('aria-expanded', 'true');
                    }
                });
            });
        })();
    </script>
@endpush
