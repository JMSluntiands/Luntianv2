import { useCallback, useEffect, useMemo, useState } from 'react';

type ForumPostPreview = {
  id: number;
  author: string;
  avatar: string | null;
  excerpt: string;
  has_image: boolean;
  comments_count: number;
  created_at: string | null;
  created_at_iso?: string | null;
};

function resolveApiBase(raw: string | undefined, fallback: string): string {
  const value = (raw || '').trim();
  return value !== '' ? value : fallback;
}

function initials(name: string): string {
  const parts = name.trim().split(/\s+/).filter(Boolean).slice(0, 2);
  const letters = parts.map((p) => p.charAt(0).toUpperCase()).join('');
  return letters || 'U';
}

function parseInitialPosts(): ForumPostPreview[] {
  const el = document.getElementById('dashboard-forum-json');
  if (!el?.textContent) return [];
  try {
    const parsed = JSON.parse(el.textContent) as { posts?: ForumPostPreview[] } | ForumPostPreview[];
    if (Array.isArray(parsed)) return parsed;
    return Array.isArray(parsed.posts) ? parsed.posts : [];
  } catch {
    return [];
  }
}

export default function ForumThreadCard() {
  const feedUrl = useMemo(() => {
    const el = document.getElementById('dashboard-root');
    return resolveApiBase(el?.dataset.forumFeedUrl, '/dashboard/forum-thread');
  }, []);

  const recentApi = useMemo(() => {
    const el = document.getElementById('dashboard-root');
    return resolveApiBase(el?.dataset.forumRecentApi, '/dashboard/forum-thread/recent');
  }, []);

  const [posts, setPosts] = useState<ForumPostPreview[]>(() => parseInitialPosts());
  const [loading, setLoading] = useState(false);

  const loadPosts = useCallback(async () => {
    setLoading(true);
    try {
      const res = await fetch(recentApi, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('Failed');
      const body = (await res.json()) as { posts?: ForumPostPreview[] };
      setPosts(Array.isArray(body.posts) ? body.posts : []);
    } catch {
      /* keep existing */
    } finally {
      setLoading(false);
    }
  }, [recentApi]);

  useEffect(() => {
    if (posts.length === 0) {
      void loadPosts();
    }
  }, [loadPosts, posts.length]);

  return (
    <section className="animate-dashboard-panel dashboard-panel-animate-delay-2 flex h-full min-h-0 min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90">
      <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 bg-slate-50/80 px-4 py-3 dark:border-slate-700/60 dark:bg-slate-800/50 sm:px-5">
        <h2 className="flex items-center gap-2.5 text-sm font-semibold text-slate-800 dark:text-slate-100 sm:text-base">
          <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
            <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m1-4h8a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586"
              />
            </svg>
          </span>
          Bulletin
        </h2>
        <div className="flex items-center gap-3">
          <button
            type="button"
            onClick={() => void loadPosts()}
            disabled={loading}
            className="text-xs font-medium text-emerald-600 transition-colors hover:text-emerald-500 disabled:opacity-40 dark:text-emerald-400 dark:hover:text-emerald-300 sm:text-sm"
          >
            Refresh
          </button>
          <a
            href={feedUrl}
            className="text-xs font-semibold text-emerald-700 transition-colors hover:text-emerald-600 dark:text-emerald-300 dark:hover:text-emerald-200 sm:text-sm"
          >
            View all
          </a>
        </div>
      </div>

      <div className="min-h-0 flex-1 overflow-y-auto">
        {posts.length === 0 ? (
          <div className="flex h-full min-h-[14rem] flex-col items-center justify-center gap-2 px-4 py-8 text-center">
            <p className="text-sm text-slate-500 dark:text-slate-400">No posts yet.</p>
            <a
              href={feedUrl}
              className="text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400"
            >
              Start a conversation
            </a>
          </div>
        ) : (
          <ul className="divide-y divide-slate-100 dark:divide-slate-700/70">
            {posts.map((post) => (
              <li key={post.id}>
                <a
                  href={`${feedUrl}#post-${post.id}`}
                  className="flex gap-3 px-4 py-3 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/40 sm:px-5"
                >
                  <div className="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-emerald-500/20 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                    {post.avatar ? (
                      <img src={post.avatar} alt="" className="h-full w-full object-cover" />
                    ) : (
                      initials(post.author)
                    )}
                  </div>
                  <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                      <span className="text-sm font-semibold text-slate-900 dark:text-slate-100">{post.author}</span>
                      {post.created_at ? (
                        <span className="text-[11px] text-slate-500 dark:text-slate-400">{post.created_at}</span>
                      ) : null}
                    </div>
                    {post.excerpt ? (
                      <p className="mt-0.5 line-clamp-2 text-sm leading-snug text-slate-600 dark:text-slate-300">
                        {post.excerpt}
                      </p>
                    ) : post.has_image ? (
                      <p className="mt-0.5 text-sm italic text-slate-500 dark:text-slate-400">Shared a photo</p>
                    ) : null}
                    <p className="mt-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                      {post.comments_count} {post.comments_count === 1 ? 'comment' : 'comments'}
                      {post.has_image ? ' · Photo' : ''}
                    </p>
                  </div>
                </a>
              </li>
            ))}
          </ul>
        )}
      </div>
    </section>
  );
}
