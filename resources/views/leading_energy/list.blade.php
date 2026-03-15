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
