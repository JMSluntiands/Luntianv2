@php
    $chartPayload = $dashboardStatusChart ?? ['date' => now('Asia/Manila')->toDateString(), 'branches' => []];
    $chartBranches = collect($chartPayload['branches'] ?? [])->filter(fn ($b) => (int) ($b['total'] ?? 0) > 0)->values();
    $filterOptions = $chartPayload['filterOptions'] ?? ['clients' => [], 'statuses' => [], 'staff' => []];
    $chartBranchFilterLocked = trim((string) ($dashboardBranchFilter ?? ''));
    $legend = [];
    foreach ($chartBranches as $branch) {
        foreach ($branch['statuses'] ?? [] as $s) {
            $legend[$s['label'] ?? ''] = $s;
        }
    }
    $shortBranch = static function (string $label): string {
        return match (strtoupper(trim($label))) {
            'GENERAL ASSEMBLY' => 'GA',
            'GENERIC ASSESSMENT' => 'GA',
            'EFFICIENT LIVING' => 'EL',
            'FYRS ENERGY WISE' => 'FYRS',
            default => $label,
        };
    };
@endphp
<section
    id="dashboard-status-chart-fallback"
    class="dashboard-status-chart mb-6 mt-6 min-w-0 overflow-hidden rounded-xl border border-slate-700/60 bg-[#0f172a] shadow-lg"
    data-chart-api="{{ route('dashboard.chart', [], false) }}"
    data-branch-filter="{{ $chartBranchFilterLocked }}"
>
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-700/60 px-4 py-3 sm:px-5">
        <h2 class="flex items-center gap-2.5 text-sm font-semibold text-slate-100 sm:text-base">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </span>
            Job Status Chart
        </h2>
    </div>

    <div class="flex flex-col lg:flex-row">
        <aside class="dashboard-chart-filters border-b border-slate-700/60 bg-slate-900/40 px-4 py-4 lg:w-56 lg:shrink-0 lg:border-b-0 lg:border-r xl:w-60">
            <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                Filters
            </div>
            <div class="space-y-3">
                <label class="dashboard-chart-filter-field block">
                    <span class="dashboard-chart-filter-field__label">Client</span>
                    <select id="dashboardChartClientFilter" aria-label="Filter by client" @disabled($chartBranchFilterLocked !== '') class="dashboard-chart-filter-select w-full rounded-lg border border-slate-600 bg-slate-900/80 px-2.5 py-2 text-xs font-medium text-slate-100">
                        <option value="" @selected($chartBranchFilterLocked === '')>All</option>
                        @foreach (($filterOptions['clients'] ?? []) as $opt)
                            <option value="{{ $opt['value'] ?? '' }}" @selected($chartBranchFilterLocked !== '' && strcasecmp($chartBranchFilterLocked, (string) ($opt['value'] ?? '')) === 0)>
                                {{ $shortBranch((string) ($opt['label'] ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="dashboard-chart-filter-field block">
                    <span class="dashboard-chart-filter-field__label">Status</span>
                    <select id="dashboardChartStatusFilter" aria-label="Filter by status" class="dashboard-chart-filter-select w-full rounded-lg border border-slate-600 bg-slate-900/80 px-2.5 py-2 text-xs font-medium text-slate-100">
                        <option value="">All</option>
                        @foreach (($filterOptions['statuses'] ?? []) as $opt)
                            <option value="{{ $opt['value'] ?? '' }}">{{ $opt['label'] ?? '' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="dashboard-chart-filter-field block">
                    <span class="dashboard-chart-filter-field__label">Staff</span>
                    <select id="dashboardChartStaffFilter" aria-label="Filter by staff" class="dashboard-chart-filter-select w-full rounded-lg border border-slate-600 bg-slate-900/80 px-2.5 py-2 text-xs font-medium text-slate-100">
                        <option value="">All</option>
                        @foreach (($filterOptions['staff'] ?? []) as $opt)
                            <option value="{{ $opt['value'] ?? '' }}">{{ $opt['label'] ?? '' }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <button type="button" id="dashboardChartClearFilters" class="mt-4 hidden w-full rounded-lg border border-slate-600 px-3 py-2 text-xs font-medium text-slate-300 hover:border-slate-500 hover:bg-slate-800 hover:text-slate-100">
                Clear filters
            </button>
        </aside>

        <div class="min-w-0 flex-1">
            <p class="px-4 py-2 text-xs text-slate-400 sm:px-5">Active jobs per module — matches Total Jobs card (status breakdown)</p>
            <div id="dashboardChartRows" class="dashboard-chart-body-enter space-y-3 px-4 pb-6 pt-2 sm:px-5">
                @forelse ($chartBranches as $branch)
                    @php
                        $total = (int) ($branch['total'] ?? 0);
                        $rowIndex = $loop->index;
                    @endphp
                    <div class="dashboard-chart-row-enter flex items-center gap-3 dashboard-chart-branch-row" data-chart-branch="{{ $branch['label'] ?? '' }}" style="animation-delay: {{ $rowIndex * 0.07 }}s">
                        <div class="w-[4.5rem] shrink-0 text-right text-[10px] font-semibold text-slate-300 sm:w-24 sm:text-xs" title="{{ $branch['label'] ?? '' }}">
                            {{ $shortBranch((string) ($branch['label'] ?? '')) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex h-7 w-full overflow-hidden rounded-md bg-slate-800/50 sm:h-8">
                                @foreach ($branch['statuses'] ?? [] as $status)
                                    @php
                                        $count = (int) ($status['count'] ?? 0);
                                        $segmentPct = $total > 0 && $count > 0 ? ($count / $total) * 100 : 0;
                                        $fontSize = $segmentPct < 10 ? '8px' : ($segmentPct < 16 ? '9px' : '10px');
                                        $bg = ! empty($status['color']) ? trim((string) $status['color']) : '#3b82f6';
                                        if ($bg !== '' && $bg[0] !== '#') { $bg = '#'.$bg; }
                                        $fg = \App\Models\Status::resolveFontColor($status['fontColor'] ?? null);
                                        $statusIndex = $loop->index;
                                        $barDelay = 0.1 + ($rowIndex * 0.07) + ($statusIndex * 0.05);
                                    @endphp
                                    <div class="relative flex h-full min-w-0 items-center justify-center" style="flex: {{ $count }} 1 0; min-width: {{ $count > 0 ? '1.125rem' : '0' }};" title="{{ $status['label'] ?? '' }}: {{ $count }}">
                                        <div class="dashboard-chart-bar-segment absolute inset-0" style="background-color: {{ $bg }}; animation-delay: {{ $barDelay }}s;"></div>
                                        @if ($count > 0)
                                            <span class="pointer-events-none relative z-10 font-bold tabular-nums" style="color: {{ $fg }}; font-size: {{ $fontSize }}; text-shadow: 0 1px 1px rgba(0,0,0,0.6);">{{ $count }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <span class="w-8 shrink-0 text-right text-xs font-semibold tabular-nums text-slate-300 sm:w-10">{{ $total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No active jobs in Job Management.</p>
                @endforelse
                @if (count($legend) > 0)
                    <div id="dashboardChartLegend" class="dashboard-chart-legend-enter mt-4 flex flex-wrap gap-2 border-t border-slate-700/60 pb-1 pt-4" style="animation-delay: {{ max(0, $chartBranches->count() - 1) * 0.07 + 0.12 }}s">
                        @foreach ($legend as $item)
                            @php
                                $bg = ! empty($item['color']) ? trim((string) $item['color']) : '#3b82f6';
                                if ($bg !== '' && $bg[0] !== '#') { $bg = '#'.$bg; }
                                $fg = \App\Models\Status::resolveFontColor($item['fontColor'] ?? null);
                            @endphp
                            <span class="rounded-md px-2 py-1 text-[10px] font-medium sm:text-xs" style="background-color: {{ $bg }}; color: {{ $fg }}">{{ $item['label'] ?? '' }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<script>
(function () {
    var section = document.getElementById('dashboard-status-chart-fallback');
    if (!section || section.dataset.chartLiveBound === '1') return;
    section.dataset.chartLiveBound = '1';

    var api = section.getAttribute('data-chart-api') || '';
    var locked = (section.getAttribute('data-branch-filter') || '').trim();
    var clientSel = document.getElementById('dashboardChartClientFilter');
    var statusSel = document.getElementById('dashboardChartStatusFilter');
    var staffSel = document.getElementById('dashboardChartStaffFilter');
    var rowsEl = document.getElementById('dashboardChartRows');
    var clearBtn = document.getElementById('dashboardChartClearFilters');
    if (!rowsEl || !api) return;

    var fetchGen = 0;
    var shortBranch = function (label) {
        var map = { 'GENERAL ASSEMBLY': 'GA', 'GENERIC ASSESSMENT': 'GA', 'EFFICIENT LIVING': 'EL', 'FYRS ENERGY WISE': 'FYRS' };
        return map[label] || label;
    };

    function resolveFontColor(raw) {
        var c = String(raw || '').trim();
        if (/^#[0-9A-Fa-f]{6}$/.test(c)) return c;
        if (/^[0-9A-Fa-f]{6}$/.test(c)) return '#' + c;
        return '#1e293b';
    }

    function resolveBarColor(raw) {
        var c = String(raw || '').trim();
        if (!c) return '#3b82f6';
        if (c[0] !== '#') return '#' + c;
        return c;
    }

    function pulseSelect(el) {
        if (!el) return;
        el.classList.remove('dashboard-chart-filter-select--pulse');
        void el.offsetWidth;
        el.classList.add('dashboard-chart-filter-select--pulse');
    }

    function currentFilters() {
        return {
            client: locked || (clientSel ? clientSel.value : '') || '',
            status: statusSel ? statusSel.value : '',
            staff: staffSel ? staffSel.value : ''
        };
    }

    function hasActiveFilters() {
        var f = currentFilters();
        return !!((!locked && f.client) || f.status || f.staff);
    }

    function toggleClear() {
        if (!clearBtn) return;
        clearBtn.classList.toggle('hidden', !hasActiveFilters());
    }

    function buildRowHtml(branch, rowIndex) {
        var total = Number(branch.total) || 0;
        var statuses = Array.isArray(branch.statuses) ? branch.statuses : [];
        var bars = statuses.map(function (status, statusIndex) {
            var count = Number(status.count) || 0;
            var segmentPct = total > 0 && count > 0 ? (count / total) * 100 : 0;
            var fontSize = segmentPct < 10 ? '8px' : (segmentPct < 16 ? '9px' : '10px');
            var bg = resolveBarColor(status.color);
            var fg = resolveFontColor(status.fontColor);
            var barDelay = 0.1 + (rowIndex * 0.07) + (statusIndex * 0.05);
            var countHtml = count > 0
                ? '<span class="pointer-events-none relative z-10 font-bold tabular-nums" style="color:' + fg + ';font-size:' + fontSize + ';text-shadow:0 1px 1px rgba(0,0,0,0.6)">' + count + '</span>'
                : '';
            return '<div class="relative flex h-full min-w-0 items-center justify-center" style="flex:' + count + ' 1 0;min-width:' + (count > 0 ? '1.125rem' : '0') + '">' +
                '<div class="dashboard-chart-bar-segment absolute inset-0" style="background-color:' + bg + ';animation-delay:' + barDelay + 's"></div>' +
                countHtml + '</div>';
        }).join('');

        return '<div class="dashboard-chart-row-enter flex items-center gap-3" style="animation-delay:' + (rowIndex * 0.07) + 's">' +
            '<div class="w-[4.5rem] shrink-0 text-right text-[10px] font-semibold text-slate-300 sm:w-24 sm:text-xs" title="' + (branch.label || '') + '">' + shortBranch(branch.label || '') + '</div>' +
            '<div class="min-w-0 flex-1"><div class="flex h-7 w-full overflow-hidden rounded-md bg-slate-800/50 sm:h-8">' + bars + '</div></div>' +
            '<span class="w-8 shrink-0 text-right text-xs font-semibold tabular-nums text-slate-300 sm:w-10">' + total + '</span></div>';
    }

    function buildLegendHtml(branches) {
        var legend = {};
        branches.forEach(function (branch) {
            (branch.statuses || []).forEach(function (s) {
                if (s && s.label) legend[s.label] = s;
            });
        });
        var items = Object.keys(legend).map(function (key) {
            var item = legend[key];
            var bg = resolveBarColor(item.color);
            var fg = resolveFontColor(item.fontColor);
            return '<span class="rounded-md px-2 py-1 text-[10px] font-medium sm:text-xs" style="background-color:' + bg + ';color:' + fg + '">' + item.label + '</span>';
        }).join('');
        if (!items) return '';
        return '<div class="dashboard-chart-legend-enter mt-4 flex flex-wrap gap-2 border-t border-slate-700/60 pb-1 pt-4" style="animation-delay:' + (Math.max(0, branches.length - 1) * 0.07 + 0.12) + 's">' + items + '</div>';
    }

    function renderChart(branches) {
        rowsEl.classList.remove('dashboard-chart-body-enter');
        rowsEl.classList.add('dashboard-chart-body-exit');
        window.setTimeout(function () {
            if (!branches.length) {
                rowsEl.innerHTML = '<p class="text-sm text-slate-500">No active jobs in Job Management.</p>';
            } else {
                rowsEl.innerHTML = branches.map(buildRowHtml).join('') + buildLegendHtml(branches);
            }
            rowsEl.classList.remove('dashboard-chart-body-exit', 'dashboard-chart-updating');
            void rowsEl.offsetWidth;
            rowsEl.classList.add('dashboard-chart-body-enter');
        }, 180);
    }

    function fetchChart() {
        var filters = currentFilters();
        var params = new URLSearchParams();
        if (filters.client) params.set('client', filters.client);
        if (filters.status) params.set('status', filters.status);
        if (filters.staff) params.set('staff', filters.staff);
        var url = params.toString() ? api + '?' + params.toString() : api;
        var gen = ++fetchGen;

        rowsEl.classList.add('dashboard-chart-updating');
        toggleClear();

        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
            .then(function (body) {
                if (gen !== fetchGen) return;
                var branches = Array.isArray(body.branches)
                    ? body.branches.filter(function (b) { return Number(b.total) > 0; })
                    : [];
                renderChart(branches);
            })
            .catch(function () {
                if (gen !== fetchGen) return;
                renderChart([]);
            });
    }

    function onFilterChange(el) {
        pulseSelect(el);
        fetchChart();
    }

    if (clientSel && !locked) clientSel.addEventListener('change', function () { onFilterChange(clientSel); });
    if (statusSel) statusSel.addEventListener('change', function () { onFilterChange(statusSel); });
    if (staffSel) staffSel.addEventListener('change', function () { onFilterChange(staffSel); });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (!locked && clientSel) clientSel.value = '';
            if (statusSel) statusSel.value = '';
            if (staffSel) staffSel.value = '';
            pulseSelect(statusSel);
            pulseSelect(staffSel);
            fetchChart();
        });
    }

    toggleClear();
})();
</script>
