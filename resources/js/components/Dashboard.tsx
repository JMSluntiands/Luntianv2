import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import Calendar, { type HolidaySource } from './Calendar';
import DashboardAnnouncementCard from './DashboardAnnouncementCard';
import JobStatusChart from './JobStatusChart';

declare global {
  interface Window {
    showSuccessToast?: (message: string) => void;
  }
}

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

type AttendanceStatus = {
  clocked_in: boolean;
  clocked_out: boolean;
  clocked_in_at: string | null;
  clocked_out_at: string | null;
  local_time: string;
  cutoff_label: string;
  timezone_label: string;
  can_clock_in: boolean;
  can_clock_out: boolean;
  overdue?: boolean;
  hours_open?: number | null;
};

function parseAttendanceFromDom(): AttendanceStatus | null {
  const el = document.getElementById('dashboard-attendance-json');
  const raw = el?.textContent?.trim();
  if (!raw) {
    return null;
  }
  try {
    const parsed = JSON.parse(raw) as Partial<AttendanceStatus>;
    return {
      ...defaultAttendanceStatus(),
      ...parsed,
      clocked_out: Boolean(parsed.clocked_out),
      clocked_out_at: parsed.clocked_out_at ?? null,
      can_clock_out: Boolean(parsed.can_clock_out),
      overdue: Boolean(parsed.overdue),
      hours_open: parsed.hours_open ?? null,
    };
  } catch {
    return null;
  }
}

function defaultAttendanceStatus(): AttendanceStatus {
  return {
    clocked_in: false,
    clocked_out: false,
    clocked_in_at: null,
    clocked_out_at: null,
    local_time: '',
    cutoff_label: '8:00 AM',
    timezone_label: 'Philippines (PHT)',
    can_clock_in: true,
    can_clock_out: false,
    overdue: false,
    hours_open: null,
  };
}

function csrfToken(): string {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta?.getAttribute('content')?.trim() || '';
}

function AttendanceBanner({
  attendance,
  clockInUrl,
  clockOutUrl,
  onAttendanceChange,
}: {
  attendance: AttendanceStatus;
  clockInUrl: string;
  clockOutUrl: string;
  onAttendanceChange: (next: AttendanceStatus) => void;
}) {
  const [busy, setBusy] = useState(false);
  const [localTime, setLocalTime] = useState(attendance.local_time);

  useEffect(() => {
    setLocalTime(attendance.local_time);
  }, [attendance.local_time]);

  useEffect(() => {
    const tick = () => {
      try {
        setLocalTime(
          new Intl.DateTimeFormat('en-US', {
            timeZone: 'Asia/Manila',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
          }).format(new Date())
        );
      } catch {
        // keep last known time
      }
    };
    tick();
    const id = window.setInterval(tick, 30000);
    return () => window.clearInterval(id);
  }, []);

  const postAttendance = async (url: string, failMessage: string, successFallback: string) => {
    if (!url || busy) {
      return;
    }
    setBusy(true);
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken(),
        },
        credentials: 'same-origin',
      });
      const body = (await res.json().catch(() => null)) as
        | { status?: string; message?: string; attendance?: AttendanceStatus }
        | null;
      if (!res.ok || body?.status !== 'success' || !body.attendance) {
        if (window.showSuccessToast) {
          window.showSuccessToast(body?.message || failMessage);
        }
        return;
      }
      onAttendanceChange(body.attendance);
      if (window.showSuccessToast) {
        window.showSuccessToast(body.message || successFallback);
      }
    } catch {
      if (window.showSuccessToast) {
        window.showSuccessToast(failMessage);
      }
    } finally {
      setBusy(false);
    }
  };

  const handleClockIn = () => {
    if (!attendance.can_clock_in) {
      return;
    }
    void postAttendance(clockInUrl, 'Could not clock in.', 'Clocked in successfully.');
  };

  const handleClockOut = () => {
    if (!attendance.can_clock_out) {
      return;
    }
    void postAttendance(clockOutUrl, 'Could not clock out.', 'Clocked out successfully.');
  };

  const actionEnabled =
    (attendance.can_clock_in || attendance.can_clock_out) && !busy;

  let buttonLabel = 'CLOCK IN';
  if (busy) {
    buttonLabel = 'Saving…';
  } else if (attendance.clocked_out) {
    buttonLabel = 'CLOCKED OUT';
  } else if (attendance.can_clock_out) {
    buttonLabel = 'CLOCK OUT';
  } else if (attendance.clocked_in) {
    buttonLabel = 'CLOCKED IN';
  }

  return (
    <section className="mb-5 flex flex-col gap-3 rounded-xl border border-slate-200/90 bg-white px-4 py-3.5 shadow-sm dark:border-slate-700/70 dark:bg-slate-800/90 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:px-5">
      <div className="min-w-0 flex-1">
        <div className="flex flex-wrap items-center gap-2">
          <h2 className="text-base font-semibold tracking-tight text-slate-900 dark:text-slate-100">Daily attendance</h2>
          <span className="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-700/80 dark:text-slate-300">
            <span aria-hidden className="text-[0.8125rem] leading-none">🇵🇭</span>
            {attendance.timezone_label}
          </span>
        </div>
        <p className="mt-1.5 text-sm leading-snug text-slate-500 dark:text-slate-400">
          {attendance.clocked_out ? (
            <>
              You clocked in at <span className="font-medium text-slate-700 dark:text-slate-200">{attendance.clocked_in_at}</span>
              {' '}and clocked out at <span className="font-medium text-slate-700 dark:text-slate-200">{attendance.clocked_out_at}</span>
              {' '}({attendance.timezone_label}). Local time now:{' '}
              <span className="font-medium text-slate-700 dark:text-slate-200">{localTime || '—'}</span>
            </>
          ) : attendance.clocked_in ? (
            <>
              You clocked in at <span className="font-medium text-slate-700 dark:text-slate-200">{attendance.clocked_in_at}</span>
              {' '}({attendance.timezone_label}). Local time now:{' '}
              <span className="font-medium text-slate-700 dark:text-slate-200">{localTime || '—'}</span>
              {attendance.overdue ? (
                <span className="mt-1 block text-red-600 dark:text-red-400">
                  Over 8 hours with no clock out — please clock out before midnight.
                </span>
              ) : null}
            </>
          ) : (
            <>
              Staff who have not clocked in by {attendance.cutoff_label} ({attendance.timezone_label}) will appear as absent.
              Local time now: <span className="font-medium text-slate-700 dark:text-slate-200">{localTime || '—'}</span>
              <span className="mt-1 block text-slate-400 dark:text-slate-500">
                After midnight, clock out is locked for the previous day (marked as no clock out) and clock in is available again.
              </span>
            </>
          )}
        </p>
      </div>
      <button
        type="button"
        onClick={attendance.can_clock_out ? handleClockOut : handleClockIn}
        disabled={!actionEnabled}
        className={`inline-flex shrink-0 items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold tracking-wide text-white transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:focus:ring-offset-slate-900 ${
          actionEnabled
            ? attendance.can_clock_out && attendance.overdue
              ? 'cursor-pointer bg-red-600 hover:bg-red-500'
              : 'cursor-pointer bg-slate-900 hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white'
            : 'cursor-not-allowed bg-slate-400 dark:bg-slate-600 dark:text-slate-300'
        }`}
      >
        {buttonLabel}
      </button>
    </section>
  );
}

/** Laravel may run in a subdirectory; root-relative `/dashboard/...` would 404. */
function resolveApiBase(raw: string | undefined, fallback: string): string {
  const fb = (fallback || '').trim() || '/dashboard/holidays';
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

function holidaysApiUrl(year: number): string {
  const el = document.getElementById('dashboard-root');
  const base = resolveApiBase(el?.dataset.holidaysApiBase, '/dashboard/holidays');
  return `${base}/${year}`;
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
  'GENERIC EA',
  'LUNTIAN',
  'BPH',
  'BluInq',
  'A&M',
  'FYRS ENERGY WISE',
  'CSP',
  'NH',
  'LC Home Builder',
  'Efficient Living',
  'Leading Energy',
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

const BRANCH_ROUTE_PREFIX: Record<string, string> = {
  LBS: 'lbs',
  'GENERIC EA': 'general-assembly',
  'GENERAL EA': 'general-assembly',
  'GENERAL ASSEMBLY': 'general-assembly',
  'GENERIC ASSESSMENT': 'general-assembly',
  LUNTIAN: 'luntian',
  BPH: 'bph',
  BluInq: 'bluinq',
  'A&M': 'amt',
  'FYRS ENERGY WISE': 'fyrs',
  CSP: 'csp',
  NH: 'nh',
  'LC Home Builder': 'lc-home-builder',
  'Efficient Living': 'efficient-living',
  'Leading Energy': 'leading-energy',
};

const CARD_BG = 'bg-[#F0C48A] dark:bg-[#A67C3A]';
const CARD_ICON = 'text-amber-900/55 dark:text-amber-50/50';
const CARD_PILL = 'bg-amber-950/10 text-amber-950 dark:bg-black/25 dark:text-amber-50';

const CARD_TEMPLATES: CardTemplate[] = [
  {
    key: 'total',
    title: 'Total Jobs',
    bgClass: CARD_BG,
    iconColor: CARD_ICON,
    pillClass: CARD_PILL,
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
    bgClass: CARD_BG,
    iconColor: CARD_ICON,
    pillClass: CARD_PILL,
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
    bgClass: CARD_BG,
    iconColor: CARD_ICON,
    pillClass: CARD_PILL,
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
    bgClass: CARD_BG,
    iconColor: CARD_ICON,
    pillClass: CARD_PILL,
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

function dashboardAsOfSubtitle(): string {
  const formatted = new Intl.DateTimeFormat('en-US', {
    timeZone: 'Asia/Manila',
    month: 'long',
    day: 'numeric',
    year: 'numeric',
  }).format(new Date());

  return `As of today, ${formatted} — overview of your jobs and calendar.`;
}

const DASHBOARD_ZONES = [
  { id: 'Asia/Manila', short: 'PHT', label: 'Philippines (PHT)' },
  { id: 'Australia/Sydney', short: 'AEST', label: 'Australia — Sydney' },
  { id: 'Australia/Perth', short: 'AWST', label: 'Australia — Perth' },
  { id: 'UTC', short: 'UTC', label: 'UTC' },
] as const;

type DashboardZoneId = (typeof DASHBOARD_ZONES)[number]['id'];

const TZ_STORAGE_KEY = 'dashboard_display_timezone';

function readStoredZone(): DashboardZoneId {
  try {
    const raw = localStorage.getItem(TZ_STORAGE_KEY)?.trim();
    if (raw && DASHBOARD_ZONES.some((z) => z.id === raw)) {
      return raw as DashboardZoneId;
    }
  } catch {
    // ignore
  }
  return 'Asia/Manila';
}

function formatInZone(date: Date, timeZone: string): { date: string; time: string } {
  const dateFmt = new Intl.DateTimeFormat('en-US', {
    timeZone,
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
  const timeFmt = new Intl.DateTimeFormat('en-US', {
    timeZone,
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  });
  return {
    date: dateFmt.format(date).toUpperCase(),
    time: timeFmt.format(date).toUpperCase(),
  };
}

function DashboardDateTimeZone() {
  const [zoneId, setZoneId] = useState<DashboardZoneId>(() =>
    typeof window !== 'undefined' ? readStoredZone() : 'Asia/Manila'
  );
  const [now, setNow] = useState(() => new Date());
  const [open, setOpen] = useState(false);
  const rootRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    const id = window.setInterval(() => setNow(new Date()), 1000);
    return () => window.clearInterval(id);
  }, []);

  useEffect(() => {
    if (!open) return;
    const onDoc = (e: MouseEvent) => {
      if (!rootRef.current?.contains(e.target as Node)) {
        setOpen(false);
      }
    };
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setOpen(false);
    };
    document.addEventListener('mousedown', onDoc);
    document.addEventListener('keydown', onKey);
    return () => {
      document.removeEventListener('mousedown', onDoc);
      document.removeEventListener('keydown', onKey);
    };
  }, [open]);

  const zone = DASHBOARD_ZONES.find((z) => z.id === zoneId) ?? DASHBOARD_ZONES[0];
  const { date, time } = formatInZone(now, zone.id);

  const pickZone = (id: DashboardZoneId) => {
    setZoneId(id);
    setOpen(false);
    try {
      localStorage.setItem(TZ_STORAGE_KEY, id);
    } catch {
      // ignore
    }
  };

  return (
    <div ref={rootRef} className="relative shrink-0 self-start sm:self-center">
      <div className="inline-flex items-center gap-0 overflow-hidden rounded-md bg-[#0b2a4a] text-[11px] font-semibold uppercase tracking-wide text-white shadow-sm sm:text-xs">
        <span className="px-3 py-2 tabular-nums">{date}</span>
        <span className="self-stretch w-px bg-white/35" aria-hidden />
        <span className="px-3 py-2 tabular-nums">{time}</span>
        <span className="self-stretch w-px bg-white/35" aria-hidden />
        <button
          type="button"
          aria-haspopup="listbox"
          aria-expanded={open}
          onClick={() => setOpen((v) => !v)}
          className="inline-flex cursor-pointer items-center gap-1.5 px-3 py-2 transition-colors hover:bg-white/10 focus:outline-none focus-visible:bg-white/15"
          title="Change timezone"
        >
          <span>{zone.short}</span>
          <svg className={`h-3 w-3 opacity-80 transition-transform ${open ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
          </svg>
        </button>
      </div>

      {open ? (
        <ul
          role="listbox"
          aria-label="Timezone"
          className="absolute right-0 z-50 mt-1.5 min-w-[12.5rem] overflow-hidden rounded-lg border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-600 dark:bg-slate-800"
        >
          {DASHBOARD_ZONES.map((z) => {
            const active = z.id === zone.id;
            return (
              <li key={z.id} role="option" aria-selected={active}>
                <button
                  type="button"
                  onClick={() => pickZone(z.id)}
                  className={`flex w-full cursor-pointer items-center justify-between gap-3 px-3 py-2 text-left text-sm transition-colors ${
                    active
                      ? 'bg-emerald-50 font-semibold text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300'
                      : 'text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/60'
                  }`}
                >
                  <span>{z.label}</span>
                  <span className="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    {z.short}
                  </span>
                </button>
              </li>
            );
          })}
        </ul>
      ) : null}
    </div>
  );
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

function dashboardBasePath(): string {
  const p = String(window.location.pathname || '');
  const i = p.toLowerCase().indexOf('/dashboard');
  return i >= 0 ? p.slice(0, i) : '';
}

function cardBucketToSegment(cardKey: CardVariant): string {
  if (cardKey === 'completed') return 'completed';
  if (cardKey === 'pending') return 'review';
  return 'list';
}

function resolveCardRowUrl(cardKey: CardVariant, branchLabel: string): string | null {
  const prefix = BRANCH_ROUTE_PREFIX[branchLabel];
  if (!prefix) return null;
  const segment = cardBucketToSegment(cardKey);
  return `${dashboardBasePath()}/dashboard/${prefix}/${segment}`;
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
  expanded,
  onToggle,
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
  expanded: boolean;
  onToggle: () => void;
}) {
  const delayClass = `dashboard-card-animate-delay-${index}`;
  const textClass = lightCard ? 'text-amber-950/80 dark:text-amber-50/85' : 'text-white/85';
  const borderClass = lightCard ? 'border-amber-950/15 dark:border-amber-50/20' : 'border-white/15';
  const panelId = `dashboard-card-panel-${cardKey}`;

  return (
    <div
      className={`animate-dashboard-card ${delayClass} relative flex min-w-0 cursor-pointer flex-col overflow-hidden rounded-xl transition-transform duration-300 ease-out hover:-translate-y-1 ${lightCard ? 'text-amber-950 dark:text-amber-50' : 'text-white'} ${bgClass}`}
      role="button"
      tabIndex={0}
      aria-expanded={expanded}
      aria-controls={panelId}
      onClick={onToggle}
      onKeyDown={(e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          onToggle();
        }
      }}
    >
      {/* Large icon as card background – no border, no bg */}
      <div className={`pointer-events-none absolute -right-2 -top-2 h-[100px] w-[100px] opacity-20 ${iconColor}`} aria-hidden>
        {icon}
      </div>
      <div className="relative z-10 flex flex-1 flex-col p-3 sm:p-4">
        <div className="min-w-0">
          <p className={`text-xs font-semibold uppercase tracking-wider ${textClass}`}>{title}</p>
          <p className={`mt-1.5 text-2xl font-bold tracking-tight tabular-nums sm:text-3xl ${lightCard ? 'text-amber-950 dark:text-amber-50' : ''}`}>
            {value}
          </p>
        </div>

        <div
          id={panelId}
          className={`grid transition-[grid-template-rows] duration-300 ease-out ${expanded ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'}`}
        >
          <div className="min-h-0 overflow-hidden">
            <div className={`mt-2.5 border-t ${borderClass}`} />
            <div className="mt-2 space-y-1">
              {items.map((item, i) => (
                <div
                  key={`${cardKey}-${item.label}-${i}`}
                  className={`flex items-center justify-between gap-2 rounded-lg px-2 py-1 text-sm transition-colors ${resolveCardRowUrl(cardKey, item.label) ? 'cursor-pointer' : ''} ${lightCard ? 'hover:bg-amber-950/5 dark:hover:bg-black/15' : 'hover:bg-white/5'}`}
                  onClick={(e) => {
                    e.stopPropagation();
                    const nextUrl = resolveCardRowUrl(cardKey, item.label);
                    if (nextUrl) {
                      window.location.href = nextUrl;
                    }
                  }}
                  role={resolveCardRowUrl(cardKey, item.label) ? 'button' : undefined}
                  tabIndex={resolveCardRowUrl(cardKey, item.label) ? 0 : undefined}
                  onKeyDown={(e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                      const nextUrl = resolveCardRowUrl(cardKey, item.label);
                      if (nextUrl) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.location.href = nextUrl;
                      }
                    }
                  }}
                  title={resolveCardRowUrl(cardKey, item.label) ? `Open ${item.label} ${CARD_BREAKDOWN_STATUS[cardKey]} table` : undefined}
                  aria-label={resolveCardRowUrl(cardKey, item.label) ? `Open ${item.label} ${CARD_BREAKDOWN_STATUS[cardKey]} table` : undefined}
                >
                  <div className="min-w-0 flex-1 space-y-1">
                    <p
                      className={`text-[13px] font-semibold leading-snug tracking-tight ${lightCard ? 'text-amber-950 dark:text-amber-50' : 'text-white'}`}
                    >
                      {CARD_BREAKDOWN_STATUS[cardKey]}
                    </p>
                    <p
                      className={`text-[11px] font-medium leading-tight ${lightCard ? 'text-amber-950/65 dark:text-amber-50/70' : 'text-white/65'}`}
                    >
                      Branch: <span className="font-semibold tabular-nums">{item.label}</span>
                    </p>
                  </div>
                  {resolveCardRowUrl(cardKey, item.label) ? (
                    <span className={`shrink-0 rounded-full px-2.5 py-0.5 text-sm font-semibold tabular-nums transition-opacity ${pillClass}`}>
                      {item.value}
                    </span>
                  ) : (
                    <span className={`shrink-0 rounded-full px-2.5 py-0.5 text-sm font-semibold tabular-nums ${pillClass}`}>
                      {item.value}
                    </span>
                  )}
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default function Dashboard() {
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
  const [dashboardStats, setDashboardStats] = useState<DashboardStatsPayload>(() =>
    normalizeStatsPayload(parseDashboardStats())
  );
  const [cardsExpanded, setCardsExpanded] = useState(false);
  const [attendance, setAttendance] = useState<AttendanceStatus>(
    () => parseAttendanceFromDom() ?? defaultAttendanceStatus()
  );

  const clockInUrl = useMemo(() => {
    const el = document.getElementById('dashboard-root');
    return (el?.dataset.attendanceClockInUrl ?? '').trim();
  }, []);

  const clockOutUrl = useMemo(() => {
    const el = document.getElementById('dashboard-root');
    return (el?.dataset.attendanceClockOutUrl ?? '').trim();
  }, []);

  const toggleAllCards = useCallback(() => {
    setCardsExpanded((v) => !v);
  }, []);

  const statCards = useMemo(() => {
    const cards = buildStatCards(dashboardStats);
    const el = document.getElementById('dashboard-root');
    const filter = el?.dataset.dashboardBranchFilter ?? '';
    return applyDashboardBranchFilter(cards, filter);
  }, [dashboardStats]);

  useEffect(() => {
    const onStatsUpdated = (event: Event) => {
      const detail = (event as CustomEvent<DashboardStatsPayload>).detail;
      if (detail) {
        setDashboardStats(normalizeStatsPayload(detail));
      }
    };

    document.addEventListener('dashboard:statsUpdated', onStatsUpdated);
    return () => document.removeEventListener('dashboard:statsUpdated', onStatsUpdated);
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
      <header className="dashboard-page__header mb-2 flex flex-col gap-3 pb-2 sm:flex-row sm:items-start sm:justify-between">
        <div className="min-w-0">
          <h1 className="dashboard-page__title">Dashboard</h1>
          <p className="dashboard-page__subtitle">{dashboardAsOfSubtitle()}</p>
        </div>
        <DashboardDateTimeZone />
      </header>

      <AttendanceBanner
        attendance={attendance}
        clockInUrl={clockInUrl}
        clockOutUrl={clockOutUrl}
        onAttendanceChange={setAttendance}
      />

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
            lightCard
            expanded={cardsExpanded}
            onToggle={toggleAllCards}
          />
        ))}
      </section>

      <section className="mb-6 mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-stretch">
        <div className="min-w-0">
          <JobStatusChart />
        </div>
        <div className="min-w-0 lg:min-h-[28rem]">
          <DashboardAnnouncementCard />
        </div>
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
