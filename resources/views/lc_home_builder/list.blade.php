@extends('layouts.dashboard')

@section('title', 'LC HOME BUILDER List')

@section('body_class', 'page-lc_home_builder-list')

@section('content')
    <div class="lc_home_builder-list-page">
        <div class="lc_home_builder-list-header">
            <div class="lc_home_builder-list-header-text">
                <h1 class="lc_home_builder-list-title">LC HOME BUILDER List</h1>
                <p class="lc_home_builder-list-subtitle">View and manage all LC HOME BUILDER jobs.</p>
            </div>
            <div class="lc_home_builder-list-search-wrap">
                <label for="lc_home_builderSearch" class="lc_home_builder-search-label">Search</label>
                <div class="lc_home_builder-search-input-wrap">
                    <svg class="lc_home_builder-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lc_home_builderSearch" class="lc_home_builder-search-input" placeholder="Search by client, job number, email..." autocomplete="off" aria-label="Search LC HOME BUILDER jobs">
                </div>
            </div>
        </div>

        <div class="lc_home_builder-table-card">
            <div class="lc_home_builder-table-wrap">
                <table class="lc_home_builder-table" id="lc_home_builderTable">
                    <colgroup>
                        <col class="lc_home_builder-col-action">
                        <col class="lc_home_builder-col-log-date">
                        <col class="lc_home_builder-col-client">
                        <col class="lc_home_builder-col-urgent">
                        <col class="lc_home_builder-col-job-type">
                        <col class="lc_home_builder-col-ncc">
                        <col class="lc_home_builder-col-job-number">
                        <col class="lc_home_builder-col-client-name">
                        <col class="lc_home_builder-col-client-email">
                        <col class="lc_home_builder-col-status">
                        <col class="lc_home_builder-col-assigned">
                        <col class="lc_home_builder-col-checked">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lc_home_builder-th lc_home_builder-th-action" data-sort="">
                                <span>Action</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Log Date</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Client</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Urgent</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Job Type</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>NCC</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Job Number</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Client Name</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Client Email</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Status</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Assigned To</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="lc_home_builder-th" data-sort="">
                                <span>Checked By</span>
                                <span class="lc_home_builder-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="lc_home_builder-td lc_home_builder-td-action">
                                <div class="lc_home_builder-action-btns">
                                    <button type="button" class="lc_home_builder-action-icon lc_home_builder-action-duplicate" title="Duplicate" aria-label="Duplicate">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <a href="#" class="lc_home_builder-action-icon lc_home_builder-action-view" title="View" aria-label="View job 011298">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                            <td class="lc_home_builder-td lc_home_builder-td-log-date" data-label="Log Date">
                                <span class="lc_home_builder-date-line1">November 29, 2025</span>
                                <span class="lc_home_builder-date-line2">03:21 AM</span>
                            </td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Client">LC HOME BUILDER01</td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Urgent">NO</td>
                            <td class="lc_home_builder-td lc_home_builder-td-job-type" data-label="Job Type">
                                <span class="lc_home_builder-job-line1">EA_LC HOME BUILDER_1S</span>
                                <span class="lc_home_builder-job-line2">Prelim</span>
                            </td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="NCC">2022 Whole of Home (WOH)</td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Job Number">011298</td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Client Name">TEST</td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Client Email">admin@luntiands.com</td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Status">
                                <div class="lc_home_builder-status-wrap" data-status-wrap>
                                    <button type="button" class="lc_home_builder-badge lc_home_builder-badge-completed lc_home_builder-status-trigger" data-status-trigger aria-haspopup="true" aria-expanded="false" data-reference="011298">Completed</button>
                                    <div class="lc_home_builder-status-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="lc_home_builder-status-option" data-status-value="Pending">Pending</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-status-option" data-status-value="Accepted">Accepted</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-status-option" data-status-value="Allocated">Allocated</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-status-option" data-status-value="Awaiting Further Information">Awaiting Further Information</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-status-option" data-status-value="Completed">Completed</button>
                                    </div>
                                </div>
                            </td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Assigned To">
                                <div class="lc_home_builder-initials-wrap" data-initials-wrap data-role="assigned">
                                    <button type="button" class="lc_home_builder-initials lc_home_builder-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">AJS</button>
                                    <div class="lc_home_builder-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="JS">JS</button>
                                    </div>
                                </div>
                            </td>
                            <td class="lc_home_builder-td lc_home_builder-td-nowrap" data-label="Checked By">
                                <div class="lc_home_builder-initials-wrap" data-initials-wrap data-role="checker">
                                    <button type="button" class="lc_home_builder-initials lc_home_builder-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false">JDR</button>
                                    <div class="lc_home_builder-initials-menu" role="menu" hidden>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="AJS">AJS</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="SB">SB</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="GM">GM</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="PEP">PEP</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="JDR">JDR</button>
                                        <button type="button" role="menuitem" class="lc_home_builder-initials-option" data-value="JS">JS</button>
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
            var searchEl = document.getElementById('lc_home_builderSearch');
            var table = document.getElementById('lc_home_builderTable');
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
                document.querySelectorAll('.lc_home_builder-status-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-status-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            function closeAllInitialsMenus() {
                document.querySelectorAll('.lc_home_builder-initials-menu').forEach(function(m) { m.hidden = true; });
                document.querySelectorAll('[data-initials-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
            }
            document.querySelectorAll('[data-initials-wrap]').forEach(function(wrap) {
                var trigger = wrap.querySelector('[data-initials-trigger]');
                var menu = wrap.querySelector('.lc_home_builder-initials-menu');
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
                menu.querySelectorAll('.lc_home_builder-initials-option').forEach(function(opt) {
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
                var menu = wrap.querySelector('.lc_home_builder-status-menu');
                var statusClasses = ['lc_home_builder-badge-pending', 'lc_home_builder-badge-accepted', 'lc_home_builder-badge-allocated', 'lc_home_builder-badge-awaiting-further-information', 'lc_home_builder-badge-completed'];
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
                menu.querySelectorAll('.lc_home_builder-status-option').forEach(function(opt) {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var val = this.getAttribute('data-status-value');
                        var badgeClass = 'lc_home_builder-badge-' + val.toLowerCase().replace(/\s+/g, '-');
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
                if (!th || th.classList.contains('lc_home_builder-th-action')) return;
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
