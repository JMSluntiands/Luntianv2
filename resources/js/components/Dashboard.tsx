import { useCallback, useEffect, useMemo, useState } from 'react';
import CountUp from 'react-countup';
import Calendar, { type HolidaySource } from './Calendar';

type CardVariant = 'total' | 'completed' | 'processing' | 'pending';

/** Primary line in each card’s breakdown — ties the row to what that card measures (not the branch name). */
const CARD_BREAKDOWN_STATUS: Record<CardVariant, string> = {
  total: 'Total jobs',
  completed: 'Completed',
  processing: 'Processing',
  pending: 'Pending',
};

type DashboardStatsPayload = {
  total: Record<string, number>;
  completed: Record<string, number>;
  processing: Record<string, number>;
  pending: Record<string, number>;
};

type HolidayItem = {
  date: string;
  localName: string;
  name: string;
  source: HolidaySource;
};

/** Laravel may run in a subdirectory; root-relative `/dashboard/...` would 404. */
function holidaysApiUrl(year: number): string {
  const el = document.getElementById('dashboard-root');
  const base = el?.dataset.holidaysApiBase?.trim().replace(/\/$/, '');
  if (base) {
    return `${base}/${year}`;
  }
  return `/dashboard/holidays/${year}`;
}

function parseInitialHolidaysFromDom(year: number): { ph: HolidayItem[]; au: HolidayItem[] } | null {
  const el = document.getElementById('dashboard-holidays-initial');
  const raw = el?.textContent?.trim();
  if (!raw) {
    return null;
  }
  const dataYear = parseInt(el.getAttribute('data-year') ?? '', 10);
  if (!Number.isFinite(dataYear) || dataYear !== year) {
    return null;
  }
  try {
    const body = JSON.parse(raw) as { ph?: unknown; au?: unknown };
    const phRows = Array.isArray(body.ph) ? body.ph : [];
    const auRows = Array.isArray(body.au) ? body.au : [];
    return {
      ph: phRows.map((h: { date: string; localName?: string; name?: string }) => ({
        date: h.date,
        localName: h.localName ?? '',
        name: h.name ?? '',
        source: 'PH' as const,
      })),
      au: auRows.map((h: { date: string; localName?: string; name?: string }) => ({
        date: h.date,
        localName: h.localName ?? '',
        name: h.name ?? '',
        source: 'AU' as const,
      })),
    };
  } catch {
    return null;
  }
}

const BRANCH_ORDER = [
  'LBS',
  'BPH',
  'BLUINQ',
  'CSP',
  'NH',
  'LC HOME BUILDER',
  'EFFICIENT LIVING',
  'LEADING ENERGY',
] as const;

function parseDashboardStats(): DashboardStatsPayload | null {
  const el = document.getElementById('dashboard-stats-json');
  const raw = el?.textContent?.trim();
  if (!raw) {
    return null;
  }
  try {
    return JSON.parse(raw) as DashboardStatsPayload;
  } catch {
    return null;
  }
}

function emptyBucket(): Record<string, number> {
  const o: Record<string, number> = {};
  for (const k of BRANCH_ORDER) {
    o[k] = 0;
  }
  return o;
}

function normalizeStatsPayload(raw: DashboardStatsPayload | null): DashboardStatsPayload {
  const z = emptyBucket();
  const merge = (b: Record<string, number> | undefined) => {
    const o = { ...z };
    if (b) {
      for (const k of BRANCH_ORDER) {
        o[k] = typeof b[k] === 'number' && !Number.isNaN(b[k]) ? b[k] : 0;
      }
    }
    return o;
  };
  return {
    total: merge(raw?.total),
    completed: merge(raw?.completed),
    processing: merge(raw?.processing),
    pending: merge(raw?.pending),
  };
}

type CardTemplate = {
  key: CardVariant;
  title: string;
  bgClass: string;
  iconColor: string;
  pillClass: string;
  icon: React.ReactNode;
};

type StatCardData = CardTemplate & { value: number; items: { label: string; value: number }[] };

/* Continuous line animation – segment travels along path in a loop (visible) */
function LineGraphBg({ light = false, variant = 'total' }: { light?: boolean; variant?: CardVariant }) {
  const stroke = light ? 'rgba(71,85,105,0.5)' : 'rgba(255,255,255,0.65)';
  const trackStroke = light ? 'rgba(71,85,105,0.18)' : 'rgba(255,255,255,0.22)';
  const fill = light ? 'rgba(71,85,105,0.06)' : 'rgba(255,255,255,0.06)';
  const lineClass = (n: number) => `dashboard-graph-continuous dashboard-graph-continuous-${variant} dashboard-graph-continuous-${n}`;
  return (
    <div className="pointer-events-none absolute inset-0 overflow-hidden rounded-xl" aria-hidden>
      <svg className="absolute bottom-0 left-0 h-[55%] w-full" viewBox="0 0 200 80" preserveAspectRatio="none">
        <path d="M0 72 Q50 58 100 45 T200 28 L200 80 L0 80 Z" fill={fill} className="dashboard-graph-fill" />
        {/* Line 1: track + moving segment (continuous) */}
        <path d="M0 65 Q30 55 60 42 T120 30 T180 20 L200 18" fill="none" stroke={trackStroke} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
        <path d="M0 65 Q30 55 60 42 T120 30 T180 20 L200 18" fill="none" stroke={stroke} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" pathLength={1} strokeDasharray="0.08 0.26" className={lineClass(1)} />
        {/* Line 2 */}
        <path d="M0 58 Q40 48 80 38 T160 22 L200 15" fill="none" stroke={trackStroke} strokeWidth="1" strokeLinecap="round" strokeLinejoin="round" />
        <path d="M0 58 Q40 48 80 38 T160 22 L200 15" fill="none" stroke={stroke} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" pathLength={1} strokeDasharray="0.08 0.26" className={lineClass(2)} />
        {/* Line 3 */}
        <path d="M0 70 Q25 60 50 48 T100 38 T150 28 T200 20" fill="none" stroke={trackStroke} strokeWidth="1" strokeLinecap="round" strokeLinejoin="round" />
        <path d="M0 70 Q25 60 50 48 T100 38 T150 28 T200 20" fill="none" stroke={stroke} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" pathLength={1} strokeDasharray="0.08 0.26" className={lineClass(3)} />
      </svg>
    </div>
  );
}

const CARD_TEMPLATES: CardTemplate[] = [
  {
    key: 'total',
    title: 'Total Jobs',
    bgClass: 'bg-[#FFA500] dark:bg-[#FFA500]',
    iconColor: 'text-white',
    pillClass: 'bg-black/20 text-white',
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} className="h-full w-full">
        <path d="M12 12h.01" />
        <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
        <path d="M22 13a18.15 18.15 0 0 1-20 0" />
        <rect width={20} height={14} x={2} y={6} rx={2} />
      </svg>
    ),
  },
  {
    key: 'completed',
    title: 'Completed Jobs',
    bgClass: 'bg-[#8B4513] dark:bg-[#8B4513]',
    iconColor: 'text-white',
    pillClass: 'bg-black/25 text-white',
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} className="h-full w-full">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <path d="M22 4L12 14.01l-3-3" />
      </svg>
    ),
  },
  {
    key: 'processing',
    title: 'Processing',
    bgClass: 'bg-[#FFC107] dark:bg-[#FFC107]',
    iconColor: 'text-white',
    pillClass: 'bg-black/20 text-white',
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} className="h-full w-full">
        <circle cx={12} cy={12} r={10} />
        <path d="M12 6v6l4 2" />
      </svg>
    ),
  },
  {
    key: 'pending',
    title: 'Pending',
    bgClass: 'bg-[#F5DEB3] dark:bg-[#F5DEB3]',
    iconColor: 'text-slate-700',
    pillClass: 'bg-slate-600/25 text-slate-800',
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} className="h-full w-full">
        <rect x={3} y={4} width={18} height={18} rx={2} ry={2} />
        <path d="M16 2v4" />
        <path d="M8 2v4" />
        <path d="M3 10h18" />
      </svg>
    ),
  },
];

function buildStatCards(stats: DashboardStatsPayload | null): StatCardData[] {
  const p = normalizeStatsPayload(stats);
  return CARD_TEMPLATES.map((tpl) => {
    const bucket = p[tpl.key];
    const items = BRANCH_ORDER.map((label) => ({ label, value: bucket[label] ?? 0 }));
    const value = items.reduce((sum, it) => sum + it.value, 0);
    return { ...tpl, value, items };
  });
}

function normalizeBranchKey(s: string): string {
  return s.trim().toLowerCase().replace(/\s+/g, ' ');
}

function branchKeyMatchesStatLabel(cardLabel: string, filterRaw: string): boolean {
  const a = normalizeBranchKey(cardLabel);
  const b = normalizeBranchKey(filterRaw);
  if (a === b) {
    return true;
  }
  const aCompact = a.replace(/\s+/g, '');
  const bCompact = b.replace(/\s+/g, '');
  if (aCompact === bCompact) {
    return true;
  }
  return a.includes(b) || b.includes(a);
}

/** When user has a branch, show only that row; main total = that row's count. Empty filter = all branches. */
function applyDashboardBranchFilter<T extends { value: number; items: { label: string; value: number }[]; key: CardVariant }>(
  cards: T[],
  branchFilterRaw: string
): T[] {
  const branchFilter = branchFilterRaw.trim();
  if (!branchFilter) {
    return cards;
  }
  return cards.map((card) => {
    const hit = card.items.find((it) => branchKeyMatchesStatLabel(it.label, branchFilter));
    const displayLabel = hit ? hit.label : branchFilter;
    const value = hit ? hit.value : 0;
    return {
      ...card,
      value,
      items: [{ label: displayLabel, value }],
    };
  });
}

/* Wrapper: show Count Up Animation (react-countup) only after page loader is hidden */
function CountUpDisplay({ value, duration, start }: { value: number; duration: number; start: boolean }) {
  if (!start) {
    return <span className="count-up-animation tabular-nums">0</span>;
  }
  return (
    <span className="count-up-animation tabular-nums" aria-live="polite">
      <CountUp start={0} end={value} duration={duration} preserveValue={false} />
    </span>
  );
}

function formatHolidayDate(isoDate: string): string {
  const d = new Date(`${isoDate}T00:00:00`);
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    weekday: 'short',
  }).format(d);
}

function sortHolidayByDate(a: HolidayItem, b: HolidayItem): number {
  return a.date.localeCompare(b.date);
}

function holidayOverlapForDate(
  isoDate: string,
  holidaysByDate: Record<string, HolidayItem[]>
): { both: boolean; hasPh: boolean; hasAu: boolean } {
  const list = holidaysByDate[isoDate] ?? [];
  const hasPh = list.some((x) => x.source === 'PH');
  const hasAu = list.some((x) => x.source === 'AU');
  return { both: hasPh && hasAu, hasPh, hasAu };
}

function holidayListRowClass(
  isoDate: string,
  holidaysByDate: Record<string, HolidayItem[]>,
  section: 'ph' | 'au'
): string {
  const { both } = holidayOverlapForDate(isoDate, holidaysByDate);
  if (both) {
    return 'dashboard-holiday-item dashboard-holiday-row dashboard-holiday-row--both';
  }
  return section === 'ph'
    ? 'dashboard-holiday-item dashboard-holiday-row dashboard-holiday-row--ph'
    : 'dashboard-holiday-item dashboard-holiday-row dashboard-holiday-row--au';
}

function StatCard({
  index,
  cardKey,
  title,
  value,
  items,
  icon,
  bgClass,
  iconColor,
  pillClass,
  lightCard = false,
  startCount = false,
}: {
  index: number;
  cardKey: CardVariant;
  title: string;
  value: number;
  items: { label: string; value: number }[];
  icon: React.ReactNode;
  bgClass: string;
  iconColor: string;
  pillClass: string;
  lightCard?: boolean;
  startCount?: boolean;
}) {
  const delayClass = `dashboard-card-animate-delay-${index}`;
  const textClass = lightCard ? 'text-slate-800' : 'text-white/85';
  const borderClass = lightCard ? 'border-slate-300/40' : 'border-white/15';
  return (
    <div
      className={`animate-dashboard-card ${delayClass} relative flex min-w-0 flex-col overflow-hidden rounded-xl transition-transform duration-300 ease-out hover:-translate-y-1 ${lightCard ? 'text-slate-800' : 'text-white'} ${bgClass}`}
    >
      {/* Line graph background – different animation per card */}
      <LineGraphBg light={lightCard} variant={cardKey} />
      {/* Large icon as card background – no border, no bg */}
      <div className={`pointer-events-none absolute -right-2 -top-2 h-[100px] w-[100px] opacity-20 ${iconColor}`} aria-hidden>
        {icon}
      </div>
      <div className="relative z-10 flex flex-1 flex-col p-3 sm:p-4">
        <p className={`text-xs font-semibold uppercase tracking-wider ${textClass}`}>{title}</p>
        <p className={`mt-1.5 text-2xl font-bold tracking-tight sm:text-3xl ${lightCard ? 'text-slate-900' : ''}`}>
          <CountUpDisplay value={value} duration={1.2} start={startCount} />
        </p>
        <div className={`mt-2.5 border-t ${borderClass}`} />
        <div className="mt-2 space-y-1">
          {items.map((item, i) => (
            <div
              key={`${cardKey}-${item.label}-${i}`}
              className={`flex items-center justify-between gap-2 rounded-lg px-2 py-1 text-sm transition-colors ${lightCard ? 'hover:bg-slate-400/10' : 'hover:bg-white/5'}`}
              style={{ animationDelay: `${0.35 + i * 0.05}s` }}
            >
              <div className="min-w-0 flex-1 space-y-1">
                <p
                  className={`text-[13px] font-semibold leading-snug tracking-tight ${lightCard ? 'text-slate-900' : 'text-white'}`}
                >
                  {CARD_BREAKDOWN_STATUS[cardKey]}
                </p>
                <p
                  className={`text-[11px] font-medium leading-tight ${lightCard ? 'text-slate-600' : 'text-white/65'}`}
                >
                  Branch: <span className="font-semibold tabular-nums">{item.label}</span>
                </p>
              </div>
              <span className={`shrink-0 rounded-full px-2.5 py-0.5 text-sm font-semibold ${pillClass}`}>
                <CountUpDisplay value={item.value} duration={0.8} start={startCount} />
              </span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

export default function Dashboard() {
  const [loadingDone, setLoadingDone] = useState(false);
  /** Plain { year, month } avoids controlled Date edge cases with React state updates. */
  const [viewYearMonth, setViewYearMonth] = useState(() => {
    const d = new Date();
    return { year: d.getFullYear(), month: d.getMonth() };
  });
  const calendarViewDate = useMemo(
    () => new Date(viewYearMonth.year, viewYearMonth.month, 1),
    [viewYearMonth.year, viewYearMonth.month]
  );
  const onCalendarMonthChange = useCallback((d: Date) => {
    setViewYearMonth({ year: d.getFullYear(), month: d.getMonth() });
  }, []);
  const [phHolidays, setPhHolidays] = useState<HolidayItem[]>([]);
  const [auHolidays, setAuHolidays] = useState<HolidayItem[]>([]);
  const [holidayLoading, setHolidayLoading] = useState(true);

  const statCards = useMemo(() => {
    const stats = parseDashboardStats();
    const cards = buildStatCards(stats);
    const el = document.getElementById('dashboard-root');
    const filter = el?.dataset.dashboardBranchFilter ?? '';
    return applyDashboardBranchFilter(cards, filter);
  }, []);

  useEffect(() => {
    let done = false;
    const finish = () => {
      if (done) {
        return;
      }
      done = true;
      setLoadingDone(true);
    };
    const loader = document.getElementById('pageLoader');
    if (!loader || loader.classList.contains('hide')) {
      finish();
    } else {
      document.addEventListener('pageLoaderHidden', finish);
    }
    const fallbackMs = 2000;
    const t = window.setTimeout(finish, fallbackMs);
    return () => {
      document.removeEventListener('pageLoaderHidden', finish);
      window.clearTimeout(t);
    };
  }, []);

  useEffect(() => {
    let ignore = false;
    const year = viewYearMonth.year;
    setHolidayLoading(true);

    const applyRows = (phData: unknown[], auData: unknown[]) => {
      if (ignore) {
        return;
      }
      setPhHolidays(
        phData.map((h: { date: string; localName?: string; name?: string }) => ({
          date: h.date,
          localName: h.localName ?? '',
          name: h.name ?? '',
          source: 'PH' as const,
        }))
      );
      setAuHolidays(
        auData.map((h: { date: string; localName?: string; name?: string }) => ({
          date: h.date,
          localName: h.localName ?? '',
          name: h.name ?? '',
          source: 'AU' as const,
        }))
      );
    };

    const initial = parseInitialHolidaysFromDom(year);
    const hadServerSeed =
      initial !== null && (initial.ph.length > 0 || initial.au.length > 0);
    if (initial) {
      applyRows(initial.ph, initial.au);
    } else {
      setPhHolidays([]);
      setAuHolidays([]);
    }

    const loadHolidays = async () => {
      try {
        const res = await fetch(holidaysApiUrl(year), {
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        });

        const ct = res.headers.get('content-type') ?? '';
        if (!res.ok || !ct.includes('application/json')) {
          throw new Error('Failed holiday fetch');
        }

        const body = (await res.json()) as { ph?: unknown; au?: unknown };
        const phData = Array.isArray(body.ph) ? body.ph : [];
        const auData = Array.isArray(body.au) ? body.au : [];
        if (!ignore) {
          applyRows(phData, auData);
        }
      } catch {
        if (!ignore && !hadServerSeed) {
          setPhHolidays([]);
          setAuHolidays([]);
        }
      } finally {
        if (!ignore) {
          setHolidayLoading(false);
        }
      }
    };

    void loadHolidays();
    return () => {
      ignore = true;
    };
  }, [viewYearMonth.year]);

  const monthKey = `${viewYearMonth.year}-${String(viewYearMonth.month + 1).padStart(2, '0')}`;

  const monthPhHolidays = useMemo(
    () => phHolidays.filter((h) => h.date.startsWith(monthKey)).sort(sortHolidayByDate),
    [phHolidays, monthKey]
  );
  const monthAuHolidays = useMemo(
    () => auHolidays.filter((h) => h.date.startsWith(monthKey)).sort(sortHolidayByDate),
    [auHolidays, monthKey]
  );

  const holidaysByDate = useMemo(() => {
    const map: Record<string, HolidayItem[]> = {};
    for (const h of [...phHolidays, ...auHolidays]) {
      if (!map[h.date]) {
        map[h.date] = [];
      }
      map[h.date].push(h);
    }
    return map;
  }, [phHolidays, auHolidays]);

  return (
    <div className="dashboard-page min-h-0 w-full">
      <header className="dashboard-page__header">
        <h1 className="dashboard-page__title">Dashboard</h1>
        <p className="dashboard-page__subtitle">
          Welcome back! Here&apos;s an overview of your jobs and calendar.
        </p>
      </header>

      <section className="dashboard-cards">
        {statCards.map((card, index) => (
          <StatCard
            key={card.key}
            index={index}
            cardKey={card.key as CardVariant}
            title={card.title}
            value={card.value}
            items={card.items}
            icon={card.icon}
            bgClass={card.bgClass}
            iconColor={card.iconColor}
            pillClass={card.pillClass}
            lightCard={index === 3}
            startCount={loadingDone}
          />
        ))}
      </section>

      <section className="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
        <div className="animate-dashboard-panel dashboard-panel-animate-delay-0 min-w-0 overflow-visible rounded-xl border border-slate-200/80 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90 lg:col-span-2">
          <h2 className="flex items-center gap-2.5 border-b border-slate-200/80 bg-slate-50/80 px-4 py-3 font-semibold text-slate-800 dark:border-slate-700/60 dark:bg-slate-800/50 dark:text-slate-100 sm:px-5 sm:py-4">
            <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
              <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </span>
            Calendar
          </h2>
          <div className="p-4 transition-colors sm:p-5">
            <div className="dashboard-calendar-wrapper">
              <Calendar
                viewDate={calendarViewDate}
                onMonthChange={onCalendarMonthChange}
                holidaysByDate={holidaysByDate}
              />
              <p className="mt-4 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span className="inline-flex items-center gap-1.5">
                  <span className="h-2.5 w-2.5 shrink-0 rounded-full bg-blue-500" aria-hidden />
                  Philippines (PH)
                </span>
                <span className="inline-flex items-center gap-1.5">
                  <span className="h-2.5 w-2.5 shrink-0 rounded-full bg-amber-500" aria-hidden />
                  Australia (AU)
                </span>
                <span className="inline-flex items-center gap-1.5">
                  <span
                    className="h-2.5 w-7 shrink-0 rounded-sm bg-gradient-to-r from-blue-500 to-amber-500"
                    aria-hidden
                  />
                  Both countries
                </span>
              </p>
            </div>
          </div>
        </div>

        <div className="animate-dashboard-panel dashboard-panel-animate-delay-1 min-w-0 overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90">
          <h2 className="flex items-center gap-2.5 border-b border-slate-200/80 bg-slate-50/80 px-4 py-3 font-semibold text-slate-800 dark:border-slate-700/60 dark:bg-slate-800/50 dark:text-slate-100 sm:px-5 sm:py-4">
            <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
              <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
              </svg>
            </span>
            Holidays
          </h2>
          <div className="space-y-3 p-4 sm:p-5">
            <div className="overflow-hidden rounded-lg border border-slate-200/80 bg-gradient-to-br from-slate-50 to-slate-100/80 dark:border-slate-600/60 dark:from-slate-800/80 dark:to-slate-900/60">
              <div className="border-b border-slate-200/80 px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-slate-600/60 dark:text-slate-400">
                Philippine Holidays
              </div>
              <div className="px-3 py-3 text-sm text-slate-600 dark:text-slate-400">
                {holidayLoading ? (
                  'Loading holidays...'
                ) : monthPhHolidays.length ? (
                  <ul className="dashboard-holiday-list">
                    {monthPhHolidays.map((h) => {
                      const { both } = holidayOverlapForDate(h.date, holidaysByDate);
                      return (
                        <li key={`ph-${h.date}-${h.name}`} className={holidayListRowClass(h.date, holidaysByDate, 'ph')}>
                          <span className="flex shrink-0 items-center gap-0.5" aria-hidden>
                            {both ? (
                              <>
                                <span className="dashboard-holiday-dot dashboard-holiday-dot--ph" />
                                <span className="dashboard-holiday-dot dashboard-holiday-dot--au" />
                              </>
                            ) : (
                              <span className="dashboard-holiday-dot dashboard-holiday-dot--ph" />
                            )}
                          </span>
                          <span className="dashboard-holiday-date">{formatHolidayDate(h.date)}</span>
                          <span className="dashboard-holiday-name">{h.localName || h.name}</span>
                        </li>
                      );
                    })}
                  </ul>
                ) : (
                  'No holidays this month'
                )}
              </div>
            </div>
            <div className="overflow-hidden rounded-lg border border-slate-200/80 bg-gradient-to-br from-slate-50 to-slate-100/80 dark:border-slate-600/60 dark:from-slate-800/80 dark:to-slate-900/60">
              <div className="border-b border-slate-200/80 px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-slate-600/60 dark:text-slate-400">
                Australian Holidays
              </div>
              <div className="px-3 py-3 text-sm text-slate-600 dark:text-slate-400">
                {holidayLoading ? (
                  'Loading holidays...'
                ) : monthAuHolidays.length ? (
                  <ul className="dashboard-holiday-list">
                    {monthAuHolidays.map((h) => {
                      const { both } = holidayOverlapForDate(h.date, holidaysByDate);
                      return (
                        <li key={`au-${h.date}-${h.name}`} className={holidayListRowClass(h.date, holidaysByDate, 'au')}>
                          <span className="flex shrink-0 items-center gap-0.5" aria-hidden>
                            {both ? (
                              <>
                                <span className="dashboard-holiday-dot dashboard-holiday-dot--ph" />
                                <span className="dashboard-holiday-dot dashboard-holiday-dot--au" />
                              </>
                            ) : (
                              <span className="dashboard-holiday-dot dashboard-holiday-dot--au" />
                            )}
                          </span>
                          <span className="dashboard-holiday-date">{formatHolidayDate(h.date)}</span>
                          <span className="dashboard-holiday-name">{h.localName || h.name}</span>
                        </li>
                      );
                    })}
                  </ul>
                ) : (
                  'No holidays this month'
                )}
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
