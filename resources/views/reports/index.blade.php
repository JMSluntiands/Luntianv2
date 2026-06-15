@extends('layouts.dashboard')

@section('title', 'Reports')

@section('body_class', 'page-reports')

@section('content')
    <div class="reports-page">
        <div class="reports-header">
            <h1 class="reports-title">Reports</h1>
            <p class="reports-subtitle">Completion dates within your range, units summed per day, job type, and user.</p>
        </div>

        <div class="reports-summary-card">
            <span class="reports-summary-briefcase" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
            <div class="reports-summary-content">
                <span class="reports-summary-title">Jobs (in filter)</span>
                <span class="reports-summary-total">{{ $totalJobsInFilter ?? 0 }}</span>
                <div class="reports-summary-sep"></div>
                <div class="reports-summary-meta">Total units: <strong>{{ $totalUnitsInFilter ?? 0 }}</strong></div>
                <ul class="reports-summary-list">
                    @foreach(['LBS', 'LUNTIAN', 'EFFICIENT LIVING', 'BPH', 'BLUINQ', 'CSP', 'NH', 'LC HOME BUILDER', 'LEADING ENERGY'] as $label)
                        @php
                            $s = ($summaryByLabel ?? collect())[$label] ?? null;
                            $u = $s ? (int) ($s->units_sum ?? 0) : 0;
                        @endphp
                        <li class="reports-summary-row">
                            <span class="reports-summary-label">{{ $label }}</span>
                            <span class="reports-summary-value">{{ $u }} <span class="reports-summary-sub">u</span></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="reports-filter-card">
            <h2 class="reports-section-title">Report Filter</h2>
            <div class="reports-filter-row">
                <div class="reports-filter-group reports-filter-group-client">
                    <label for="reportsClient" class="reports-filter-label">Client</label>
                    <select id="reportsClient" name="client" class="reports-filter-select select2-single" aria-label="Client filter">
                        <option value="">Select client</option>
                        <option value="all" @selected(($filterClient ?? 'all') === 'all')>ALL</option>
                            @foreach(($clientOptions ?? []) as $opt)
                                <option value="{{ $opt }}" @selected(($filterClient ?? 'all') === $opt)>{{ $opt }}</option>
                            @endforeach
                    </select>
                </div>
                <div class="reports-filter-group reports-filter-group-staff">
                    <label for="reportsStaff" class="reports-filter-label">Staff</label>
                    <select id="reportsStaff" name="staff" class="reports-filter-select select2-single" aria-label="Staff filter">
                        <option value="">Select staff</option>
                        <option value="all" @selected(($filterStaff ?? 'all') === 'all')>ALL</option>
                        @foreach(($staffOptions ?? []) as $sopt)
                            <option value="{{ $sopt }}" @selected(($filterStaff ?? 'all') === $sopt)>{{ $sopt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="reports-filter-group reports-filter-group-daterange">
                    <label class="reports-filter-label">Completion Date</label>
                    <div class="reports-filter-date-wrap reports-filter-date-range-wrap">
                        <input
                            type="text"
                            id="reportsDateRange"
                            class="reports-filter-input reports-filter-input-range"
                                value=""
                                placeholder="Any completion date range"
                            aria-label="Completion date range"
                            autocomplete="off"
                            readonly
                        >
                        <input
                            type="date"
                            id="reportsDateFrom"
                            value="{{ $filterDateFrom ?? '' }}"
                            class="reports-date-hidden-input reports-date-hidden-from"
                            aria-label="Completion date from"
                            tabindex="-1"
                        >
                        <input
                            type="date"
                            id="reportsDateTo"
                            value="{{ $filterDateTo ?? '' }}"
                            class="reports-date-hidden-input reports-date-hidden-to"
                            aria-label="Completion date to"
                            tabindex="-1"
                        >
                        <svg id="reportsDateRangeCalendar" class="reports-filter-calendar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>
                <div class="reports-filter-actions">
                    <button type="button" class="reports-btn reports-btn-apply">Apply</button>
                </div>
            </div>
        </div>

        <div class="reports-data-card">
            <div class="reports-data-header">
                <h2 class="reports-section-title">Report Data</h2>
                <button type="button" class="reports-btn reports-btn-export">
                    <svg class="reports-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M9 15l3 3 3-3"/></svg>
                    Export to Excel
                </button>
            </div>
            <p class="reports-records-count">{{ $recordsCount ?? 0 }} records</p>
            <div class="reports-data-toolbar">
                <div class="reports-entries-wrap">
                    <label for="reportsEntries" class="reports-entries-label">Show</label>
                    <select id="reportsEntries" class="reports-entries-select select2-single" aria-label="Entries per page">
                        <option value="10" @selected((int) ($filterEntries ?? 200) === 10)>10</option>
                        <option value="25" @selected((int) ($filterEntries ?? 200) === 25)>25</option>
                        <option value="50" @selected((int) ($filterEntries ?? 200) === 50)>50</option>
                        <option value="100" @selected((int) ($filterEntries ?? 200) === 100)>100</option>
                        <option value="200" @selected((int) ($filterEntries ?? 200) === 200)>200</option>
                    </select>
                    <span class="reports-entries-text">entries</span>
                </div>
                <div class="reports-search-wrap">
                    <label for="reportsSearch" class="reports-search-label">Search:</label>
                    <input type="search" id="reportsSearch" class="reports-search-input" placeholder="" aria-label="Search report data" autocomplete="off">
                </div>
            </div>
            <div class="reports-table-wrap">
                <table class="reports-table" id="reportsTable">
                    <thead>
                        <tr>
                            <th class="reports-th" data-sort="">
                                <span>Date Completion</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="reports-th" data-sort="">
                                <span>User</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="reports-th" data-sort="">
                                <span>Job Type</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="reports-th" data-sort="">
                                <span>Total Units</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($rows) && $rows->count() > 0)
                            @foreach($rows as $row)
                                <tr>
                                    <td class="reports-td">
                                        @php
                                            $cd = $row->completion_date ?? null;
                                            $cdText = $cd ? \Carbon\Carbon::parse($cd)->format('M d, Y') : '—';
                                        @endphp
                                        {{ $cdText }}
                                    </td>
                                    <td class="reports-td">{{ $row->user_code !== null && trim((string) $row->user_code) !== '' ? $row->user_code : '—' }}</td>
                                    <td class="reports-td">{{ $row->job_type ?? '—' }}</td>
                                    <td class="reports-td">{{ $row->units ?? 0 }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="reports-empty-row">
                                <td colspan="4" class="reports-empty-cell">No data for this filter.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.select2-single').select2({ width: '100%', allowClear: false });

            var rangeInput = document.getElementById('reportsDateRange');
            var fromHidden = document.getElementById('reportsDateFrom');
            var toHidden = document.getElementById('reportsDateTo');
            var rangeCalendar = document.getElementById('reportsDateRangeCalendar');

            function pad2(n) { return String(n).padStart(2, '0'); }
            function formatMmDdYyyy(iso) {
                // iso expected: yyyy-mm-dd
                if (!iso) return '';
                var parts = String(iso).split('-');
                if (parts.length !== 3) return iso;
                return pad2(parts[1]) + '/' + pad2(parts[2]) + '/' + parts[0];
            }
            function showPickerSafe(inputEl) {
                if (!inputEl) return;
                try {
                    inputEl.focus();
                    if (typeof inputEl.showPicker === 'function') {
                        inputEl.showPicker();
                    } else {
                        inputEl.click();
                    }
                } catch (e) {
                    // no-op
                }
            }
            function syncRangeText() {
                if (!rangeInput) return;
                var fromVal = fromHidden ? fromHidden.value : '';
                var toVal = toHidden ? toHidden.value : '';
                var fromText = formatMmDdYyyy(fromVal);
                var toText = formatMmDdYyyy(toVal);
                rangeInput.value = (fromText && toText) ? (fromText + ' - ' + toText) : (fromText ? (fromText + ' - ') : '');
            }

            if (rangeCalendar) {
                rangeCalendar.addEventListener('click', function (e) {
                    e.preventDefault();
                    showPickerSafe(fromHidden);
                });
            }
            if (rangeInput) {
                rangeInput.addEventListener('click', function (e) {
                    e.preventDefault();
                    showPickerSafe(fromHidden);
                });
            }

            if (fromHidden) {
                fromHidden.addEventListener('change', function () {
                    syncRangeText();
                    // After selecting From, enable To picker overlay
                    if (fromHidden && toHidden) {
                        try {
                            fromHidden.style.pointerEvents = 'none';
                            fromHidden.style.zIndex = '1';
                            toHidden.style.pointerEvents = 'auto';
                            toHidden.style.zIndex = '2';
                        } catch (e) {}
                    }
                    showPickerSafe(toHidden);
                });
            }
            if (toHidden) {
                toHidden.addEventListener('change', function () {
                    syncRangeText();
                    // Re-enable From overlay for next interaction
                    if (fromHidden) {
                        try {
                            fromHidden.style.pointerEvents = 'auto';
                            fromHidden.style.zIndex = '2';
                        } catch (e) {}
                    }
                    if (toHidden) {
                        try {
                            toHidden.style.pointerEvents = 'none';
                            toHidden.style.zIndex = '1';
                        } catch (e) {}
                    }
                });
            }

            syncRangeText();

            // Apply button: redirect with query params
            var applyBtn = document.querySelector('.reports-btn-apply');
            var clientSel = document.getElementById('reportsClient');
            var staffSel = document.getElementById('reportsStaff');
            var entriesSel = document.getElementById('reportsEntries');
            if (applyBtn) {
                applyBtn.addEventListener('click', function () {
                    var params = new URLSearchParams();
                    if (clientSel && clientSel.value) {
                        params.set('client', clientSel.value);
                    }
                    if (staffSel && staffSel.value) {
                        params.set('staff', staffSel.value);
                    }
                    if (fromHidden && fromHidden.value) {
                        params.set('date_from', fromHidden.value);
                    }
                    if (toHidden && toHidden.value) {
                        params.set('date_to', toHidden.value);
                    }
                    if (entriesSel && entriesSel.value) {
                        params.set('entries', entriesSel.value);
                    }
                    var qs = params.toString();
                    window.location.href = window.location.pathname + (qs ? ('?' + qs) : '');
                });
            }
        });
    </script>
@endpush
