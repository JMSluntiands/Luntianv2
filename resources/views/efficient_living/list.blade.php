@extends('layouts.dashboard')

@section('title', 'EFFICIENT LIVING List')

@section('body_class', 'page-efficient_living-list')

@section('content')
    <div class="efficient_living-list-page">
        <div class="efficient_living-list-header">
            <div class="efficient_living-list-header-text">
                <h1 class="efficient_living-list-title">EFFICIENT LIVING List</h1>
                <p class="efficient_living-list-subtitle">View and manage all EFFICIENT LIVING jobs.</p>
            </div>
            <div class="efficient_living-list-search-wrap">
                <label for="efficient_livingSearch" class="efficient_living-search-label">Search</label>
                <div class="efficient_living-search-input-wrap">
                    <svg class="efficient_living-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="efficient_livingSearch" class="efficient_living-search-input" placeholder="Search by client, job number, email..." autocomplete="off" aria-label="Search EFFICIENT LIVING jobs">
                </div>
            </div>
        </div>

        <div class="efficient_living-table-card">
            <div class="efficient_living-table-wrap">
                <table class="efficient_living-table" id="efficient_livingTable">
                    <colgroup>
                        <col class="efficient_living-col-action">
                        <col class="efficient_living-col-log-date">
                        <col class="efficient_living-col-client">
                        <col class="efficient_living-col-urgent">
                        <col class="efficient_living-col-job-type">
                        <col class="efficient_living-col-ncc">
                        <col class="efficient_living-col-job-number">
                        <col class="efficient_living-col-client-name">
                        <col class="efficient_living-col-client-email">
                        <col class="efficient_living-col-status">
                        <col class="efficient_living-col-assigned">
                        <col class="efficient_living-col-checked">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="efficient_living-th efficient_living-th-action" data-sort="">
                                <span>Action</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Log Date</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Client</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Urgent</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Job Type</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>NCC</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Job Number</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Client Name</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Client Email</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Status</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Assigned To</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="efficient_living-th" data-sort="">
                                <span>Checked By</span>
                                <span class="efficient_living-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="efficient_living-td efficient_living-td-action">
                                <div class="efficient_living-action-btns">
                                    <button type="button" class="efficient_living-action-icon efficient_living-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <a href="#" class="efficient_living-action-icon efficient_living-action-view" title="View" aria-label="View job 011298">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                            <td class="efficient_living-td efficient_living-td-log-date" data-label="Log Date">
                                <span class="efficient_living-date-line1">November 29, 2025</span>
                                <span class="efficient_living-date-line2">03:21 AM</span>
                            </td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Client">EFFICIENT LIVING01</td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Urgent">NO</td>
                            <td class="efficient_living-td efficient_living-td-job-type" data-label="Job Type">
                                <span class="efficient_living-job-line1">EA_EFFICIENT LIVING_1S</span>
                                <span class="efficient_living-job-line2">Prelim</span>
                            </td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="NCC">2022 Whole of Home (WOH)</td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Job Number">011298</td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Client Name">TEST</td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Client Email">admin@luntiands.com</td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Status">
                                <div class="efficient_living-status-wrap" data-status-wrap>
                                    <button type="button" class="efficient_living-badge efficient_living-badge-completed efficient_living-status-trigger" data-status-trigger aria-haspopup="true" aria-expanded="false" data-reference="011298">Completed</button>
                                    <div class="efficient_living-status-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="efficient_living-status-option" data-status-value="Pending">Pending</button>
                                        <button type="button" role="menuitem" class="efficient_living-status-option" data-status-value="Accepted">Accepted</button>
                                        <button type="button" role="menuitem" class="efficient_living-status-option" data-status-value="Allocated">Allocated</button>
                                        <button type="button" role="menuitem" class="efficient_living-status-option" data-status-value="Awaiting Further Information">Awaiting Further Information</button>
                                        <button type="button" role="menuitem" class="efficient_living-status-option" data-status-value="Completed">Completed</button>
                                    </div>
                                </div>
                            </td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Assigned To">
                                <div class="efficient_living-initials-wrap" data-initials-wrap data-role="assigned">
                                    <button type="button" class="efficient_living-initials efficient_living-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">AJS</button>
                                    <div class="efficient_living-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="JS">JS</button>
                                    </div>
                                </div>
                            </td>
                            <td class="efficient_living-td efficient_living-td-nowrap" data-label="Checked By">
                                <div class="efficient_living-initials-wrap" data-initials-wrap data-role="checker">
                                    <button type="button" class="efficient_living-initials efficient_living-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">JDR</button>
                                    <div class="efficient_living-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="efficient_living-initials-option" data-value="JS">JS</button>
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
            var searchEl = document.getElementById('efficient_livingSearch');
            var table = document.getElementById('efficient_livingTable');
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
                document.querySelectorAll('.efficient_living-status-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-status-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            function closeAllInitialsMenus() {
                document.querySelectorAll('.efficient_living-initials-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-initials-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            document.querySelectorAll('[data-initials-wrap]').forEach(function(wrap) {
                var trigger = wrap.querySelector('[data-initials-trigger]');
                var menu = wrap.querySelector('.efficient_living-initials-menu');
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
                menu.querySelectorAll('.efficient_living-initials-option').forEach(function(opt) {
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
                var menu = wrap.querySelector('.efficient_living-status-menu');
                var statusClasses = ['efficient_living-badge-pending', 'efficient_living-badge-accepted', 'efficient_living-badge-allocated', 'efficient_living-badge-awaiting-further-information', 'efficient_living-badge-completed'];
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
                menu.querySelectorAll('.efficient_living-status-option').forEach(function(opt) {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var val = this.getAttribute('data-status-value');
                        var badgeClass = 'efficient_living-badge-' + val.toLowerCase().replace(/\s+/g, '-');
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
                if (!th || th.classList.contains('efficient_living-th-action')) return;
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
