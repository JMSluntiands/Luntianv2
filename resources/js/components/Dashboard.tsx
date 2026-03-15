import { useEffect, useState } from 'react';
import CountUp from 'react-countup';
import Calendar from './Calendar';

type CardVariant = 'total' | 'completed' | 'processing' | 'pending';

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

const STAT_CARDS = [
  {
    key: 'total',
    title: 'Total Jobs',
    value: 143,
    items: [
      { label: 'LBS', value: 45 },
      { label: 'BPH', value: 10 },
      { label: 'BLUINQ', value: 56 },
      { label: 'CSP', value: 8 },
      { label: 'NH', value: 5 },
      { label: 'LC HOME BUILDER', value: 7 },
      { label: 'EFFICIENT LIVING', value: 6 },
      { label: 'LEADING ENERGY', value: 6 },
    ],
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
    value: 87,
    items: [
      { label: 'LBS', value: 32 },
      { label: 'BPH', value: 8 },
      { label: 'BLUINQ', value: 22 },
      { label: 'CSP', value: 6 },
      { label: 'NH', value: 4 },
      { label: 'LC HOME BUILDER', value: 5 },
      { label: 'EFFICIENT LIVING', value: 5 },
      { label: 'LEADING ENERGY', value: 5 },
    ],
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
    value: 28,
    items: [
      { label: 'LBS', value: 10 },
      { label: 'BPH', value: 2 },
      { label: 'BLUINQ', value: 9 },
      { label: 'CSP', value: 2 },
      { label: 'NH', value: 1 },
      { label: 'LC HOME BUILDER', value: 2 },
      { label: 'EFFICIENT LIVING', value: 1 },
      { label: 'LEADING ENERGY', value: 1 },
    ],
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
    value: 28,
    items: [
      { label: 'LBS', value: 3 },
      { label: 'BPH', value: 0 },
      { label: 'BLUINQ', value: 25 },
      { label: 'CSP', value: 0 },
      { label: 'NH', value: 0 },
      { label: 'LC HOME BUILDER', value: 0 },
      { label: 'EFFICIENT LIVING', value: 0 },
      { label: 'LEADING ENERGY', value: 0 },
    ],
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

/* Wrapper: show Count Up Animation (react-countup) only after page loader is hidden */
function CountUpDisplay({ value, duration, start }: { value: number; duration: number; start: boolean }) {
  if (!start) {
    return <span className="count-up-animation tabular-nums">0</span>;
  }
  return (
    <span className="count-up-animation tabular-nums" aria-live="polite">
      <CountUp start={0} end={value} duration={duration} />
    </span>
  );
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
              key={item.label}
              className={`flex items-center justify-between rounded-lg px-2 py-1 text-sm transition-colors ${lightCard ? 'hover:bg-slate-400/10' : 'hover:bg-white/5'}`}
              style={{ animationDelay: `${0.35 + i * 0.05}s` }}
            >
              <span className={`font-medium ${textClass}`}>{item.label}</span>
              <span className={`rounded-full px-2.5 py-0.5 text-sm font-semibold ${pillClass}`}>
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

  useEffect(() => {
    const onLoaderHidden = () => setLoadingDone(true);
    const loader = document.getElementById('pageLoader');
    if (!loader || loader.classList.contains('hide')) {
      setLoadingDone(true);
    } else {
      document.addEventListener('pageLoaderHidden', onLoaderHidden);
      return () => document.removeEventListener('pageLoaderHidden', onLoaderHidden);
    }
  }, []);

  return (
    <div className="dashboard-page min-h-0 w-full">
      <header className="dashboard-page__header">
        <h1 className="dashboard-page__title">Dashboard</h1>
        <p className="dashboard-page__subtitle">
          Welcome back! Here&apos;s an overview of your jobs and calendar.
        </p>
      </header>

      <section className="dashboard-cards">
        {STAT_CARDS.map((card, index) => (
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
        <div className="animate-dashboard-panel dashboard-panel-animate-delay-0 min-w-0 overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90 lg:col-span-2">
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
              <Calendar />
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
              <div className="px-3 py-3 text-sm text-slate-600 dark:text-slate-400">No holidays this month</div>
            </div>
            <div className="overflow-hidden rounded-lg border border-slate-200/80 bg-gradient-to-br from-slate-50 to-slate-100/80 dark:border-slate-600/60 dark:from-slate-800/80 dark:to-slate-900/60">
              <div className="border-b border-slate-200/80 px-3 py-2.5 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-slate-600/60 dark:text-slate-400">
                Australian Holidays
              </div>
              <div className="px-3 py-3 text-sm text-slate-600 dark:text-slate-400">No holidays this month</div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
