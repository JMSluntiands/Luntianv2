import { useCallback, useEffect, useMemo, useState } from 'react';

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

export type StatusChartPayload = {
  date: string;
  scope?: string;
  branches?: BranchChartRow[];
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
    'GENERAL ASSEMBLY': 'GA',
    'EFFICIENT LIVING': 'EL',
    'FYRS ENERGY WISE': 'FYRS',
    'LC HOME BUILDER': 'LC HB',
    'LEADING ENERGY': 'LE',
  };
  return map[label] ?? label;
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

  const [branches, setBranches] = useState<BranchChartRow[]>(
    () => parseInitialChart()?.branches ?? []
  );
  const [loading, setLoading] = useState(false);
  const [hovered, setHovered] = useState<string | null>(null);

  const loadChart = useCallback(async () => {
    setLoading(true);
    try {
      const res = await fetch(chartBase, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('chart fetch failed');
      const body = (await res.json()) as StatusChartPayload;
      setBranches(Array.isArray(body.branches) ? body.branches : []);
    } catch {
      setBranches([]);
    } finally {
      setLoading(false);
    }
  }, [chartBase]);

  useEffect(() => {
    if (!parseInitialChart()?.branches?.length) {
      void loadChart();
    }

    const onChartUpdated = (event: Event) => {
      const detail = (event as CustomEvent<StatusChartPayload>).detail;
      if (detail && Array.isArray(detail.branches)) {
        setBranches(detail.branches);
      }
    };
    document.addEventListener('dashboard:chartUpdated', onChartUpdated);

    return () => {
      document.removeEventListener('dashboard:chartUpdated', onChartUpdated);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps -- initial load only
  }, []);

  const maxTotal = useMemo(
    () => branches.reduce((max, b) => Math.max(max, Number(b.total) || 0), 0),
    [branches]
  );

  const legendStatuses = useMemo(() => {
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
        <div className="flex items-center gap-3 text-xs sm:text-sm">
          <button
            type="button"
            onClick={() => void loadChart()}
            disabled={loading}
            className="font-medium text-blue-400 transition-colors hover:text-blue-300 disabled:opacity-40"
          >
            Refresh
          </button>
        </div>
      </div>

      <div className="px-4 py-2 text-xs text-slate-400 sm:px-5">
        Active jobs per module — matches Total Jobs card (status breakdown)
        {loading ? <span className="ml-2 text-slate-500">Loading…</span> : null}
      </div>

      {branches.length === 0 ? (
        <p className="px-4 pb-6 text-sm text-slate-500 sm:px-5">No active jobs in Job Management.</p>
      ) : (
        <div className="space-y-3 px-4 pb-6 pt-2 sm:px-5">
          {branches.map((branch) => {
            const total = Number(branch.total) || 0;
            const statuses = branch.statuses ?? [];
            return (
              <div key={branch.label} className="flex items-center gap-3">
                <div
                  className="w-[4.5rem] shrink-0 text-right text-[10px] font-semibold leading-tight text-slate-300 sm:w-24 sm:text-xs"
                  title={branch.label}
                >
                  {shortBranchLabel(branch.label)}
                </div>
                <div className="relative min-w-0 flex-1">
                  <div className="relative flex h-7 w-full overflow-hidden rounded-md bg-slate-800/50 sm:h-8">
                    {statuses.map((status) => {
                      const count = Number(status.count) || 0;
                      const barColor = resolveHexColor(status.color);
                      const labelColor = resolveHexColor(status.fontColor, '#1e293b');
                      const hoverKey = `${branch.label}::${status.label}`;
                      const isHover = hovered === hoverKey;
                      const widthPct =
                        maxTotal > 0 && count > 0 ? (count / maxTotal) * 100 : 0;
                      const showLabel = widthPct >= 6;
                      return (
                        <div
                          key={status.label}
                          className="relative h-full shrink-0"
                          style={{
                            width: `${widthPct}%`,
                            minWidth: count > 0 ? '2px' : 0,
                          }}
                          onMouseEnter={() => setHovered(hoverKey)}
                          onMouseLeave={() => setHovered(null)}
                        >
                          <div
                            className="h-full w-full transition-opacity"
                            style={{
                              backgroundColor: barColor,
                              opacity: isHover ? 0.92 : 1,
                            }}
                            role="img"
                            aria-label={`${branch.label} ${status.label}: ${count}`}
                          />
                          {count > 0 && showLabel ? (
                            <span
                              className="absolute left-1/2 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap text-[9px] font-bold tabular-nums sm:text-[10px]"
                              style={{ color: labelColor }}
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

          {legendStatuses.length > 0 ? (
            <div className="mt-4 flex flex-wrap gap-2 border-t border-slate-700/60 pb-1 pt-4">
              {legendStatuses.map((s) => (
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
      )}
    </section>
  );
}
