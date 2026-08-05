import { useMemo, useState } from 'react';

type AnnouncementItem = {
  id: number;
  title: string;
  message: string;
  excerpt: string;
  author: string;
  status?: string;
  image_url?: string | null;
  date_label: string | null;
  time_label: string | null;
  meta_label: string;
  created_at_iso?: string | null;
  url?: string | null;
};

function resolveUrl(raw: string | undefined, fallback: string): string {
  const value = (raw || '').trim();
  return value !== '' ? value : fallback;
}

function parseInitialAnnouncements(): AnnouncementItem[] {
  const el = document.getElementById('dashboard-announcement-json');
  if (!el?.textContent) return [];
  try {
    const parsed = JSON.parse(el.textContent) as
      | { announcements?: AnnouncementItem[] }
      | AnnouncementItem[];
    const list = Array.isArray(parsed)
      ? parsed
      : Array.isArray(parsed.announcements)
        ? parsed.announcements
        : [];
    return list.map((item) => ({
      ...item,
      id: Number(item.id),
    }));
  } catch {
    return [];
  }
}

export default function DashboardAnnouncementCard() {
  const listUrl = useMemo(() => {
    const el = document.getElementById('dashboard-root');
    return resolveUrl(el?.dataset.announcementListUrl, '/dashboard/forum-thread');
  }, []);

  const items = useMemo(() => parseInitialAnnouncements(), []);
  const [activeId, setActiveId] = useState<number | null>(() =>
    items[0] != null ? Number(items[0].id) : null
  );

  const latest =
    items.find((a) => Number(a.id) === Number(activeId)) ?? items[0] ?? null;
  const seeMoreUrl = (latest?.url || '').trim() || listUrl;

  return (
    <section className="animate-dashboard-panel dashboard-panel-animate-delay-2 relative z-10 flex h-full min-h-0 min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90">
      <div className="flex items-center gap-2.5 border-b border-slate-200 px-4 py-3 dark:border-slate-700 sm:px-5">
        <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">
          <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
            />
          </svg>
        </span>
        <h2 className="text-base font-bold text-slate-900 dark:text-slate-100">Bulletin</h2>
      </div>

      {!latest ? (
        <div className="flex flex-1 flex-col items-center justify-center gap-2 px-4 py-10 text-center">
          <p className="text-sm text-slate-500 dark:text-slate-400">No bulletin announcements yet.</p>
          <a href={listUrl} className="text-sm font-semibold text-sky-600 hover:text-sky-500 dark:text-sky-400">
            Open Bulletin
          </a>
        </div>
      ) : (
        <div className="grid min-h-0 flex-1 grid-cols-1 lg:grid-cols-[minmax(0,1.6fr)_minmax(12rem,0.9fr)]">
          <div className="min-w-0 border-b border-slate-200 p-4 dark:border-slate-700 sm:p-5 lg:border-b-0 lg:border-r">
            <div className="relative mb-4 overflow-hidden rounded-xl bg-gradient-to-br from-sky-500 via-blue-600 to-indigo-700">
              {latest.image_url ? (
                <img src={latest.image_url} alt="" className="h-36 w-full object-cover sm:h-40" />
              ) : (
                <>
                  <div
                    className="pointer-events-none absolute inset-0 opacity-30"
                    style={{
                      backgroundImage: 'radial-gradient(circle, rgba(255,255,255,0.45) 1px, transparent 1px)',
                      backgroundSize: '14px 14px',
                    }}
                  />
                  <div className="relative flex h-36 items-center justify-center sm:h-40">
                    <svg className="h-16 w-16 text-white/25" fill="currentColor" viewBox="0 0 24 24" aria-hidden>
                      <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                  </div>
                </>
              )}
              <div className="absolute bottom-0 left-0 right-0 bg-slate-900/55 px-3 py-1.5 text-xs font-medium text-white backdrop-blur-[2px]">
                Latest announcement
              </div>
            </div>

            <p className="mb-1 text-[11px] font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-400">
              Latest update
            </p>
            <h3 className="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 sm:text-2xl">
              {latest.title}
            </h3>
            <p className="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{latest.meta_label}</p>
            <p className="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
              {latest.excerpt || latest.message}
            </p>
            <a
              href={seeMoreUrl}
              className="mt-4 inline-flex text-sm font-semibold text-sky-600 transition-colors hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300"
            >
              See more
            </a>
          </div>

          <aside className="relative z-20 min-h-0 overflow-y-auto p-3 sm:p-4">
            <h4 className="mb-2 px-1 text-sm font-semibold text-slate-800 dark:text-slate-100">Previous</h4>
            <ul className="space-y-1">
              {items.map((item) => {
                const active = Number(item.id) === Number(latest.id);
                return (
                  <li key={item.id}>
                    <button
                      type="button"
                      onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        setActiveId(Number(item.id));
                      }}
                      className={`relative z-20 w-full cursor-pointer rounded-lg px-3 py-2.5 text-left transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/50 ${
                        active
                          ? 'bg-sky-50 dark:bg-sky-500/15'
                          : 'hover:bg-slate-50 dark:hover:bg-slate-700/50'
                      }`}
                    >
                      <p
                        className={`truncate text-sm font-semibold ${
                          active
                            ? 'text-sky-800 dark:text-sky-200'
                            : 'text-slate-800 dark:text-slate-100'
                        }`}
                      >
                        {item.title}
                      </p>
                      <p className="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        {item.date_label || '—'}
                      </p>
                    </button>
                  </li>
                );
              })}
            </ul>
          </aside>
        </div>
      )}
    </section>
  );
}
