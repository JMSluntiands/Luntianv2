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
