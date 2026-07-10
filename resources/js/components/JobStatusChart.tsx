import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

export type StatusChartRow = {
  label: string;
  count: number;
  color?: string | null;
  fontColor?: string | null;
};

export type BranchChartRow = {
  label: string;
  total: number;
  statuses: StatusChartRow[];
};

export type ChartFilterOption = {
  value: string;
  label: string;
};

export type ChartFilterOptions = {
  clients: ChartFilterOption[];
  statuses: ChartFilterOption[];
  staff: ChartFilterOption[];
};

export type StatusChartPayload = {
  date: string;
  scope?: string;
  branches?: BranchChartRow[];
  filterOptions?: ChartFilterOptions;
  filters?: {
    client: string;
    status: string;
    staff: string;
  };
};

const DEFAULT_BAR_COLOR = '#3b82f6';

function resolveHexColor(raw?: string | null, fallback = DEFAULT_BAR_COLOR): string {
  const c = (raw ?? '').trim();
  if (/^#[0-9A-Fa-f]{6}$/.test(c)) {
    return c;
  }
  if (/^[0-9A-Fa-f]{6}$/.test(c)) {
    return `#${c}`;
  }

  return fallback;
}

function resolveApiBase(raw: string | undefined, fallback: string): string {
  const fb = (fallback || '').trim() || '/dashboard/chart';
  const value = (raw || fb).trim() || fb;
  try {
    if (value.startsWith('/')) {
      return value.replace(/\/$/, '') || fb;
    }
    const parsed = new URL(value, window.location.origin);
    return (parsed.pathname + parsed.search).replace(/\/$/, '') || fb;
  } catch {
    return fb;
  }
}

function parseInitialChart(): StatusChartPayload | null {
  const el = document.getElementById('dashboard-chart-json');
  const raw = el?.textContent?.trim();
  if (!raw) return null;
  try {
    return JSON.parse(raw) as StatusChartPayload;
  } catch {
    return null;
  }
}

function shortBranchLabel(label: string): string {
  const map: Record<string, string> = {
    'GENERIC ASSESSMENT': 'GA',
    'EFFICIENT LIVING': 'EL',
    'FYRS ENERGY WISE': 'FYRS',
    'LC HOME BUILDER': 'LC HB',
    'LEADING ENERGY': 'LE',
  };
  return map[label] ?? label;
}

function lockedBranchFilter(): string {
  const el = document.getElementById('dashboard-root');
  return (el?.dataset.dashboardBranchFilter ?? '').trim();
}

function normalizeFilterOptions(raw?: ChartFilterOptions): ChartFilterOptions {
  const pick = (rows?: ChartFilterOption[]) =>
    Array.isArray(rows)
      ? rows.filter((r) => typeof r?.value === 'string' && r.value.trim() !== '')
      : [];

  return {
    clients: pick(raw?.clients),
    statuses: pick(raw?.statuses),
    staff: pick(raw?.staff),
  };
}

type ChartFilters = {
  client: string;
  status: string;
  staff: string;
};

function FilterField({
  label,
  value,
  options,
  disabled,
  pulseKey,
  onChange,
}: {
  label: string;
  value: string;
  options: ChartFilterOption[];
  disabled?: boolean;
  pulseKey?: number;
  onChange: (value: string) => void;
}) {
  const selectRef = useRef<HTMLSelectElement>(null);

  useEffect(() => {
    const el = selectRef.current;
    if (!el || !pulseKey) {
      return;
    }
    el.classList.remove('dashboard-chart-filter-select--pulse');
    void el.offsetWidth;
    el.classList.add('dashboard-chart-filter-select--pulse');
  }, [pulseKey]);

  return (
    <label className="dashboard-chart-filter-field block">
      <span className="dashboard-chart-filter-field__label">{label}</span>
      <select
        ref={selectRef}
        value={value}
        disabled={disabled}
        onChange={(e) => onChange(e.target.value)}
        aria-label={`Filter by ${label}`}
        className={`dashboard-chart-filter-select w-full rounded-lg border border-slate-600 bg-slate-900/80 px-2.5 py-2 text-xs font-medium text-slate-100 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60 sm:text-sm${pulseKey ? ' dashboard-chart-filter-select--pulse' : ''}`}
      >
        <option value="">All</option>
        {options.map((opt) => (
          <option key={`${label}-${opt.value}`} value={opt.value}>
            {label === 'Client' ? shortBranchLabel(opt.label) : opt.label}
          </option>
        ))}
      </select>
    </label>
  );
}

export default function JobStatusChart() {
  const chartBase = useMemo(() => {
    const el = document.getElementById('dashboard-root');
    if (el?.dataset.chartApiBase) {
      return resolveApiBase(el.dataset.chartApiBase, '/dashboard/chart');
    }
    const statsBase = resolveApiBase(el?.dataset.statsApiBase, '/dashboard/stats');
    return statsBase.replace(/\/stats\/?$/, '/chart');
  }, []);

  const initialChart = useMemo(() => parseInitialChart(), []);
  const branchFilterLocked = useMemo(() => lockedBranchFilter(), []);

  const [branches, setBranches] = useState<BranchChartRow[]>(() => initialChart?.branches ?? []);
  const [filterOptions, setFilterOptions] = useState<ChartFilterOptions>(() =>
    normalizeFilterOptions(initialChart?.filterOptions)
  );
  const [clientFilter, setClientFilter] = useState(() => branchFilterLocked);
  const [statusFilter, setStatusFilter] = useState('');
  const [staffFilter, setStaffFilter] = useState('');
  const [loading, setLoading] = useState(false);
  const [updating, setUpdating] = useState(false);
  const [chartRenderKey, setChartRenderKey] = useState(0);
  const [chartPhase, setChartPhase] = useState<'idle' | 'exit' | 'enter'>('enter');
  const [filterPulse, setFilterPulse] = useState({ client: 0, status: 0, staff: 0 });
  const [hovered, setHovered] = useState<string | null>(null);
  const skipInitialFilterFetch = useRef(Boolean(initialChart?.branches?.length));
  const fetchAbortRef = useRef<AbortController | null>(null);
  const fetchGenRef = useRef(0);

  const chartFilters = useMemo<ChartFilters>(
    () => ({
      client: branchFilterLocked || clientFilter,
      status: statusFilter,
      staff: staffFilter,
    }),
    [branchFilterLocked, clientFilter, statusFilter, staffFilter]
  );

  const chartAnimKey = chartRenderKey;
  const hasActiveFilters = !!(
    (!branchFilterLocked && clientFilter) ||
    statusFilter ||
    staffFilter
  );

  const loadChart = useCallback(
    async (filters: ChartFilters, options?: { bumpAnimation?: boolean }) => {
      const bumpAnimation = options?.bumpAnimation ?? true;
      fetchAbortRef.current?.abort();
      const controller = new AbortController();
      fetchAbortRef.current = controller;
      const fetchGen = ++fetchGenRef.current;

      setLoading(true);
      setUpdating(true);
      if (bumpAnimation) {
        setChartPhase('exit');
      }

      try {
        const params = new URLSearchParams();
        if (filters.client) params.set('client', filters.client);
        if (filters.status) params.set('status', filters.status);
        if (filters.staff) params.set('staff', filters.staff);
        const url = params.toString() ? `${chartBase}?${params.toString()}` : chartBase;

        const res = await fetch(url, {
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
          signal: controller.signal,
        });
        if (!res.ok) throw new Error('chart fetch failed');
        const body = (await res.json()) as StatusChartPayload;

        if (fetchGen !== fetchGenRef.current) {
          return;
        }

        setBranches(Array.isArray(body.branches) ? body.branches : []);
        const opts = normalizeFilterOptions(body.filterOptions);
        setFilterOptions(opts);
        setStaffFilter((current) => {
          if (!current) {
            return current;
          }
          const allowed = new Set(opts.staff.map((s) => s.value.toUpperCase()));
          return allowed.has(current.toUpperCase()) ? current : '';
        });
        if (bumpAnimation) {
          setChartRenderKey((k) => k + 1);
          setChartPhase('enter');
        }
      } catch (error) {
        if (error instanceof DOMException && error.name === 'AbortError') {
          return;
        }
        if (fetchGen !== fetchGenRef.current) {
          return;
        }
        setBranches([]);
        if (bumpAnimation) {
          setChartRenderKey((k) => k + 1);
          setChartPhase('enter');
        }
      } finally {
        if (fetchGen === fetchGenRef.current) {
          setLoading(false);
          setUpdating(false);
        }
      }
    },
    [chartBase]
  );

  const pulseFilter = (field: 'client' | 'status' | 'staff') => {
    setFilterPulse((prev) => ({ ...prev, [field]: prev[field] + 1 }));
  };

  const handleClientChange = (value: string) => {
    pulseFilter('client');
    setClientFilter(value);
    setStaffFilter('');
  };

  const handleStatusChange = (value: string) => {
    pulseFilter('status');
    setStatusFilter(value);
  };

  const handleStaffChange = (value: string) => {
    pulseFilter('staff');
    setStaffFilter(value);
  };

  useEffect(() => {
    if (!initialChart?.branches?.length) {
      void loadChart(chartFilters);
    }

    const onChartUpdated = (event: Event) => {
      const detail = (event as CustomEvent<StatusChartPayload>).detail;
      if (detail && Array.isArray(detail.branches)) {
        setBranches(detail.branches);
        setFilterOptions(normalizeFilterOptions(detail.filterOptions));
        setChartRenderKey((k) => k + 1);
        setChartPhase('enter');
      }
    };
    document.addEventListener('dashboard:chartUpdated', onChartUpdated);

    return () => {
      document.removeEventListener('dashboard:chartUpdated', onChartUpdated);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps -- initial mount only
  }, []);

  useEffect(() => {
    if (chartPhase !== 'enter') {
      return;
    }
    const timer = window.setTimeout(() => setChartPhase('idle'), 420);
    return () => window.clearTimeout(timer);
  }, [chartRenderKey, chartPhase]);

  useEffect(() => {
    return () => {
      fetchAbortRef.current?.abort();
    };
  }, []);

  useEffect(() => {
    if (skipInitialFilterFetch.current) {
      skipInitialFilterFetch.current = false;
      return;
    }
    void loadChart(chartFilters, { bumpAnimation: true });
  }, [chartFilters, loadChart]);

  const filteredLegendStatuses = useMemo(() => {
    const seen = new Map<string, StatusChartRow>();
    for (const branch of branches) {
      for (const s of branch.statuses ?? []) {
        if (!seen.has(s.label)) {
          seen.set(s.label, s);
        }
      }
    }
    return Array.from(seen.values());
  }, [branches]);

  const clearFilters = () => {
    if (!branchFilterLocked) {
      pulseFilter('client');
      setClientFilter('');
    }
    pulseFilter('status');
    pulseFilter('staff');
    setStatusFilter('');
    setStaffFilter('');
  };

  const chartBodyClass =
    chartPhase === 'exit'
      ? 'dashboard-chart-body-exit'
      : chartPhase === 'enter'
        ? 'dashboard-chart-body-enter'
        : '';

  return (
    <section className="animate-dashboard-panel dashboard-panel-animate-delay-2 mb-6 mt-6 min-w-0 overflow-hidden rounded-xl border border-slate-700/60 bg-[#0f172a] shadow-lg">
      <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-700/60 px-4 py-3 sm:px-5">
        <h2 className="flex items-center gap-2.5 text-sm font-semibold text-slate-100 sm:text-base">
          <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400">
            <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </span>
          Job Status Chart
        </h2>
        <button
          type="button"
          onClick={() => void loadChart(chartFilters, { bumpAnimation: true })}
          disabled={loading}
          className="text-xs font-medium text-blue-400 transition-colors hover:text-blue-300 disabled:opacity-40 sm:text-sm"
        >
          Refresh
        </button>
      </div>

      <div className="flex flex-col lg:flex-row">
        <aside className="dashboard-chart-filters border-b border-slate-700/60 bg-slate-900/40 px-4 py-4 lg:w-56 lg:shrink-0 lg:border-b-0 lg:border-r xl:w-60">
          <div className="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            <svg className="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            Filters
          </div>
          <div className="space-y-3">
            <FilterField
              label="Client"
              value={branchFilterLocked || clientFilter}
              options={filterOptions.clients}
              disabled={!!branchFilterLocked}
              pulseKey={filterPulse.client}
              onChange={handleClientChange}
            />
            <FilterField
              label="Status"
              value={statusFilter}
              options={filterOptions.statuses}
              pulseKey={filterPulse.status}
              onChange={handleStatusChange}
            />
            <FilterField
              label="Staff"
              value={staffFilter}
              options={filterOptions.staff}
              pulseKey={filterPulse.staff}
              onChange={handleStaffChange}
            />
          </div>
          {hasActiveFilters ? (
            <button
              type="button"
              onClick={clearFilters}
              className="mt-4 w-full rounded-lg border border-slate-600 px-3 py-2 text-xs font-medium text-slate-300 transition-colors hover:border-slate-500 hover:bg-slate-800 hover:text-slate-100"
            >
              Clear filters
            </button>
          ) : null}
        </aside>

        <div className="min-w-0 flex-1">
          <div className="px-4 py-2 text-xs text-slate-400 sm:px-5">
            Active jobs per module — matches Total Jobs card (status breakdown)
            {loading ? <span className="ml-2 text-slate-500">Loading…</span> : null}
          </div>

          {branches.length === 0 && !updating ? (
            <p className="px-4 pb-6 text-sm text-slate-500 sm:px-5">No active jobs in Job Management.</p>
          ) : branches.length > 0 ? (
            <div
              key={chartAnimKey}
              className={`space-y-3 px-4 pb-6 pt-2 sm:px-5 ${chartBodyClass} ${updating ? 'dashboard-chart-updating' : ''}`}
            >
              {branches.map((branch, rowIndex) => {
                const total = Number(branch.total) || 0;
                const statuses = branch.statuses ?? [];
                return (
                  <div
                    key={branch.label}
                    className="dashboard-chart-row-enter flex items-center gap-3"
                    style={{ animationDelay: `${rowIndex * 0.07}s` }}
                  >
                    <div
                      className="w-[4.5rem] shrink-0 text-right text-[10px] font-semibold leading-tight text-slate-300 sm:w-24 sm:text-xs"
                      title={branch.label}
                    >
                      {shortBranchLabel(branch.label)}
                    </div>
                    <div className="relative min-w-0 flex-1">
                      <div className="relative flex h-7 w-full overflow-hidden rounded-md bg-slate-800/50 sm:h-8">
                        {statuses.map((status, statusIndex) => {
                          const count = Number(status.count) || 0;
                          const barColor = resolveHexColor(status.color);
                          const labelColor = resolveHexColor(status.fontColor, '#1e293b');
                          const hoverKey = `${branch.label}::${status.label}`;
                          const isHover = hovered === hoverKey;
                          const segmentPct =
                            total > 0 && count > 0 ? (count / total) * 100 : 0;
                          return (
                            <div
                              key={status.label}
                              className="relative flex h-full min-w-0 items-center justify-center"
                              style={{
                                flexGrow: count,
                                flexShrink: 1,
                                flexBasis: 0,
                                minWidth: count > 0 ? '1.125rem' : 0,
                              }}
                              onMouseEnter={() => setHovered(hoverKey)}
                              onMouseLeave={() => setHovered(null)}
                            >
                              <div
                                className="dashboard-chart-bar-segment absolute inset-0 transition-opacity"
                                style={{
                                  backgroundColor: barColor,
                                  opacity: isHover ? 0.92 : 1,
                                  animationDelay: `${0.1 + rowIndex * 0.07 + statusIndex * 0.05}s`,
                                }}
                                role="img"
                                aria-label={`${branch.label} ${status.label}: ${count}`}
                              />
                              {count > 0 ? (
                                <span
                                  className="pointer-events-none relative z-10 whitespace-nowrap font-bold tabular-nums drop-shadow-[0_1px_1px_rgba(0,0,0,0.6)]"
                                  style={{
                                    color: labelColor,
                                    fontSize: segmentPct < 10 ? '8px' : segmentPct < 16 ? '9px' : '10px',
                                  }}
                                >
                                  {count}
                                </span>
                              ) : null}
                              {isHover ? (
                                <div className="absolute -top-9 left-1/2 z-20 -translate-x-1/2 whitespace-nowrap rounded-md border border-slate-600 bg-slate-800 px-2 py-1 text-[10px] text-slate-100 shadow-lg">
                                  {status.label}: <strong>{count}</strong>
                                </div>
                              ) : null}
                            </div>
                          );
                        })}
                      </div>
                    </div>
                    <span className="w-8 shrink-0 text-right text-xs font-semibold tabular-nums text-slate-300 sm:w-10">
                      {total}
                    </span>
                  </div>
                );
              })}

              {filteredLegendStatuses.length > 0 ? (
                <div
                  key={`legend-${chartAnimKey}`}
                  className="dashboard-chart-legend-enter mt-4 flex flex-wrap gap-2 border-t border-slate-700/60 pb-1 pt-4"
                  style={{ animationDelay: `${branches.length * 0.07 + 0.12}s` }}
                >
                  {filteredLegendStatuses.map((s) => (
                    <span
                      key={s.label}
                      className="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-[10px] font-medium sm:text-xs"
                      style={{
                        backgroundColor: resolveHexColor(s.color),
                        color: resolveHexColor(s.fontColor, '#1e293b'),
                      }}
                    >
                      {s.label}
                    </span>
                  ))}
                </div>
              ) : null}
            </div>
          ) : updating ? (
            <p className="px-4 pb-6 text-sm text-slate-500 sm:px-5">Updating chart…</p>
          ) : null}
        </div>
      </div>
    </section>
  );
}
