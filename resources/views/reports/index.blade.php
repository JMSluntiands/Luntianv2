@extends('layouts.dashboard')

@section('title', 'Reports')

@section('body_class', 'page-reports')

@section('content')
    <div class="reports-page">
        <div class="reports-header">
            <h1 class="reports-title">Reports</h1>
            <p class="reports-subtitle">Completion dates within your range, units and active time spent (Allocated → Completed; On Hold paused) summed per day, job type, and user.</p>
        </div>

        <div class="reports-top-grid">
        <div class="reports-summary-card">
            <span class="reports-summary-briefcase" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
            <div class="reports-summary-content">
                <span class="reports-summary-title">Jobs (in filter)</span>
                <span class="reports-summary-total">{{ $totalJobsInFilter ?? 0 }}</span>
                <div class="reports-summary-sep"></div>
                <div class="reports-summary-meta">Total units: <strong>{{ $totalUnitsInFilter ?? 0 }}</strong></div>
                <ul class="reports-summary-list">
                    @foreach(['LBS', 'LUNTIAN', 'Efficient Living', 'BPH', 'BluInq', 'CSP', 'NH', 'LC Home Builder', 'Leading Energy'] as $label)
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

        <div class="reports-chart-card">
            <div class="reports-chart-head">
                <h2 class="reports-section-title">Units chart</h2>
                <p class="reports-chart-hint">Filter the graph independently of the table below.</p>
            </div>
            <div class="reports-chart-filters">
                <div class="reports-filter-group">
                    <label for="chartChecker" class="reports-filter-label">Checker</label>
                    <select id="chartChecker" class="reports-filter-select select2-single" aria-label="Checker filter">
                        <option value="all" @selected(($chartFilterChecker ?? 'all') === 'all')>ALL</option>
                        @foreach(($checkerOptions ?? []) as $copt)
                            <option value="{{ $copt }}" @selected(($chartFilterChecker ?? 'all') === $copt)>{{ $copt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="reports-filter-group">
                    <label for="chartClient" class="reports-filter-label">Client</label>
                    <select id="chartClient" class="reports-filter-select select2-single" aria-label="Client filter">
                        <option value="all" @selected(($chartFilterClient ?? 'all') === 'all')>ALL</option>
                        @foreach(($clientOptions ?? []) as $opt)
                            <option value="{{ $opt }}" @selected(($chartFilterClient ?? 'all') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="reports-filter-group reports-filter-group-daterange">
                    <label class="reports-filter-label">Date Range</label>
                    <div class="reports-filter-date-wrap reports-filter-date-range-wrap">
                        <input
                            type="text"
                            id="chartDateRange"
                            class="reports-filter-input reports-filter-input-range"
                            value=""
                            placeholder="Any completion date range"
                            aria-label="Chart date range"
                            autocomplete="off"
                            readonly
                        >
                        <input type="date" id="chartDateFrom" value="{{ $chartDateFrom ?? '' }}" class="reports-date-hidden-input reports-date-hidden-from" aria-label="Chart date from" tabindex="-1">
                        <input type="date" id="chartDateTo" value="{{ $chartDateTo ?? '' }}" class="reports-date-hidden-input reports-date-hidden-to" aria-label="Chart date to" tabindex="-1">
                        <svg id="chartDateRangeCalendar" class="reports-filter-calendar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>
                <div class="reports-filter-group">
                    <label for="chartJobType" class="reports-filter-label">Job Type</label>
                    <select id="chartJobType" class="reports-filter-select select2-single" aria-label="Job type filter">
                        <option value="all" @selected(($chartFilterJobType ?? 'all') === 'all')>ALL</option>
                        @foreach(($jobTypeOptions ?? []) as $jopt)
                            <option value="{{ $jopt }}" @selected(($chartFilterJobType ?? 'all') === $jopt)>{{ $jopt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="reports-filter-group">
                    <label for="chartStaff" class="reports-filter-label">Staff</label>
                    <select id="chartStaff" class="reports-filter-select select2-single" aria-label="Staff filter">
                        <option value="all" @selected(($chartFilterStaff ?? 'all') === 'all')>ALL</option>
                        @foreach(($staffOptions ?? []) as $sopt)
                            <option value="{{ $sopt }}" @selected(($chartFilterStaff ?? 'all') === $sopt)>{{ $sopt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="reports-filter-actions">
                    <button type="button" class="reports-btn reports-btn-apply" id="chartApplyBtn">Apply</button>
                </div>
            </div>
            <div class="reports-chart-canvas-wrap">
                <canvas id="reportsUnitsChart" aria-label="Units by day chart"></canvas>
                <p id="reportsChartEmpty" class="reports-chart-empty hidden">No completed jobs for these filters.</p>
            </div>
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
                <button type="button" class="reports-btn reports-btn-export" id="reportsExportBtn" data-export-url="{{ route('reports.export') }}">
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
                            <th class="reports-th" data-sort="">
                                <span>Time Spent</span>
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
                                    <td class="reports-td">{{ $row->time_spent ?? '—' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="reports-empty-row">
                                <td colspan="5" class="reports-empty-cell">No data for this filter.</td>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        $(function() {
            $('.select2-single').select2({ width: '100%', allowClear: false });

            var rangeInput = document.getElementById('reportsDateRange');
            var fromHidden = document.getElementById('reportsDateFrom');
            var toHidden = document.getElementById('reportsDateTo');
            var rangeCalendar = document.getElementById('reportsDateRangeCalendar');

            function pad2(n) { return String(n).padStart(2, '0'); }
            function formatMmDdYyyy(iso) {
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
                } catch (e) {}
            }
            function bindDateRange(rangeEl, fromEl, toEl, calendarEl) {
                function syncRangeText() {
                    if (!rangeEl) return;
                    var fromVal = fromEl ? fromEl.value : '';
                    var toVal = toEl ? toEl.value : '';
                    var fromText = formatMmDdYyyy(fromVal);
                    var toText = formatMmDdYyyy(toVal);
                    rangeEl.value = (fromText && toText) ? (fromText + ' - ' + toText) : (fromText ? (fromText + ' - ') : '');
                }
                if (calendarEl) {
                    calendarEl.addEventListener('click', function (e) {
                        e.preventDefault();
                        showPickerSafe(fromEl);
                    });
                }
                if (rangeEl) {
                    rangeEl.addEventListener('click', function (e) {
                        e.preventDefault();
                        showPickerSafe(fromEl);
                    });
                }
                if (fromEl) {
                    fromEl.addEventListener('change', function () {
                        syncRangeText();
                        if (fromEl && toEl) {
                            try {
                                fromEl.style.pointerEvents = 'none';
                                fromEl.style.zIndex = '1';
                                toEl.style.pointerEvents = 'auto';
                                toEl.style.zIndex = '2';
                            } catch (e) {}
                        }
                        showPickerSafe(toEl);
                    });
                }
                if (toEl) {
                    toEl.addEventListener('change', function () {
                        syncRangeText();
                        if (fromEl) {
                            try {
                                fromEl.style.pointerEvents = 'auto';
                                fromEl.style.zIndex = '2';
                            } catch (e) {}
                        }
                        if (toEl) {
                            try {
                                toEl.style.pointerEvents = 'none';
                                toEl.style.zIndex = '1';
                            } catch (e) {}
                        }
                    });
                }
                syncRangeText();
                return syncRangeText;
            }

            bindDateRange(rangeInput, fromHidden, toHidden, rangeCalendar);

            var chartRangeInput = document.getElementById('chartDateRange');
            var chartFromHidden = document.getElementById('chartDateFrom');
            var chartToHidden = document.getElementById('chartDateTo');
            var chartRangeCalendar = document.getElementById('chartDateRangeCalendar');
            bindDateRange(chartRangeInput, chartFromHidden, chartToHidden, chartRangeCalendar);

            var applyBtn = document.querySelector('.reports-filter-card .reports-btn-apply');
            var clientSel = document.getElementById('reportsClient');
            var staffSel = document.getElementById('reportsStaff');
            var entriesSel = document.getElementById('reportsEntries');

            function buildReportParams() {
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
                return params;
            }

            if (applyBtn) {
                applyBtn.addEventListener('click', function () {
                    var qs = buildReportParams().toString();
                    window.location.href = window.location.pathname + (qs ? ('?' + qs) : '');
                });
            }

            var exportBtn = document.getElementById('reportsExportBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function () {
                    var exportUrl = exportBtn.getAttribute('data-export-url') || '';
                    if (!exportUrl) return;
                    var qs = buildReportParams().toString();
                    window.location.href = exportUrl + (qs ? ('?' + qs) : '');
                });
            }

            var initialChart = @json($chartPayload ?? ['labels' => [], 'units' => [], 'jobs' => []]);
            var chartUrl = @json(route('reports.chart'));
            var canvas = document.getElementById('reportsUnitsChart');
            var emptyEl = document.getElementById('reportsChartEmpty');
            var chartInstance = null;

            function isDarkTheme() {
                return document.documentElement.getAttribute('data-theme') === 'dark';
            }
            function chartColors() {
                var dark = isDarkTheme();
                return {
                    text: dark ? '#cbd5e1' : '#334155',
                    grid: dark ? 'rgba(148, 163, 184, 0.18)' : 'rgba(148, 163, 184, 0.28)',
                    units: dark ? 'rgba(52, 211, 153, 0.85)' : 'rgba(16, 185, 129, 0.85)',
                    jobs: dark ? 'rgba(96, 165, 250, 0.7)' : 'rgba(37, 99, 235, 0.7)',
                };
            }
            function hasChartData(payload) {
                var units = payload && payload.units ? payload.units : [];
                var jobs = payload && payload.jobs ? payload.jobs : [];
                return units.some(function (n) { return Number(n) > 0; }) || jobs.some(function (n) { return Number(n) > 0; });
            }
            function renderUnitsChart(payload) {
                if (!canvas || typeof Chart === 'undefined') return;
                var colors = chartColors();
                var show = hasChartData(payload);
                if (emptyEl) emptyEl.classList.toggle('hidden', show);
                canvas.style.opacity = show ? '1' : '0.15';
                if (chartInstance) {
                    chartInstance.destroy();
                    chartInstance = null;
                }
                chartInstance = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: payload.labels || [],
                        datasets: [
                            {
                                label: 'Units',
                                data: payload.units || [],
                                backgroundColor: colors.units,
                                borderRadius: 6,
                                maxBarThickness: 28,
                            },
                            {
                                label: 'Jobs',
                                data: payload.jobs || [],
                                backgroundColor: colors.jobs,
                                borderRadius: 6,
                                maxBarThickness: 28,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: colors.text, boxWidth: 12, font: { weight: '700' } }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: colors.text, maxRotation: 0, autoSkip: true, maxTicksLimit: 12 },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: colors.text, precision: 0 },
                                grid: { color: colors.grid }
                            }
                        }
                    }
                });
            }

            renderUnitsChart(initialChart);

            function chartSelectValue(id) {
                var el = document.getElementById(id);
                return el ? el.value : '';
            }
            function loadChart() {
                var params = new URLSearchParams();
                params.set('chart_checker', chartSelectValue('chartChecker') || 'all');
                params.set('chart_client', chartSelectValue('chartClient') || 'all');
                params.set('chart_staff', chartSelectValue('chartStaff') || 'all');
                params.set('chart_job_type', chartSelectValue('chartJobType') || 'all');
                if (chartFromHidden && chartFromHidden.value) params.set('chart_date_from', chartFromHidden.value);
                if (chartToHidden && chartToHidden.value) params.set('chart_date_to', chartToHidden.value);
                fetch(chartUrl + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                    .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
                    .then(renderUnitsChart)
                    .catch(function () {
                        renderUnitsChart({ labels: [], units: [], jobs: [] });
                    });
            }

            var chartApply = document.getElementById('chartApplyBtn');
            if (chartApply) {
                chartApply.addEventListener('click', function (e) {
                    e.preventDefault();
                    loadChart();
                });
            }

            document.addEventListener('themechange', function () {
                renderUnitsChart(chartInstance && chartInstance.data ? {
                    labels: chartInstance.data.labels || [],
                    units: (chartInstance.data.datasets[0] && chartInstance.data.datasets[0].data) || [],
                    jobs: (chartInstance.data.datasets[1] && chartInstance.data.datasets[1].data) || [],
                } : initialChart);
            });
        });
    </script>
@endpush
