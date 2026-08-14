@php
    $announcements = array_slice($dashboardAnnouncements ?? \App\Services\DashboardAnnouncementService::recentPayload(5), 0, 5);
    $latest = $announcements[0] ?? null;
    $listUrl = \Illuminate\Support\Facades\Route::has('forum_thread')
        ? route('forum_thread', [], false)
        : '/dashboard/forum-thread';
    $bulletinCaption = static function (array $item): string {
        $kind = strtolower((string) ($item['status'] ?? '')) === 'announcement' ? 'announcement' : 'discussion';

        return ! empty($item['is_pinned']) ? 'Pinned '.$kind : 'Latest '.$kind;
    };
    $bulletinIsAnnouncement = static function (array $item): bool {
        return strtolower((string) ($item['status'] ?? '')) === 'announcement';
    };
@endphp
<section
    class="flex h-full min-h-[28rem] min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90"
    data-bulletin-fallback
    data-bulletin-list-url="{{ $listUrl }}"
>
    <div class="flex items-center gap-2.5 border-b border-slate-200 px-4 py-3 dark:border-slate-700 sm:px-5">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
        </span>
        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Bulletin</h2>
    </div>

    @if(!$latest)
        <div class="flex flex-1 flex-col items-center justify-center gap-2 px-4 py-10 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">No bulletin posts yet.</p>
            <a href="{{ $listUrl }}" class="text-sm font-semibold text-sky-600 hover:text-sky-500 dark:text-sky-400">Open Bulletin</a>
        </div>
    @else
        @php
            $latestIsAnnouncement = $bulletinIsAnnouncement($latest);
            $latestPinned = ! empty($latest['is_pinned']);
        @endphp
        <div class="grid min-h-0 flex-1 grid-cols-1 lg:grid-cols-[minmax(0,1.6fr)_minmax(12rem,0.9fr)]">
            <div class="min-w-0 border-b border-slate-200 p-4 dark:border-slate-700 sm:p-5 lg:border-b-0 lg:border-r" data-bulletin-main>
                <div class="relative mb-4 overflow-hidden rounded-xl bg-gradient-to-br from-sky-500 via-blue-600 to-indigo-700">
                    <img data-bulletin-image src="{{ $latest['image_url'] ?? '' }}" alt="" class="h-36 w-full object-cover sm:h-40 {{ empty($latest['image_url']) ? 'hidden' : '' }}">
                    <div data-bulletin-image-fallback class="relative flex h-36 items-center justify-center sm:h-40 {{ !empty($latest['image_url']) ? 'hidden' : '' }}">
                        <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image: radial-gradient(circle, rgba(255,255,255,0.45) 1px, transparent 1px); background-size: 14px 14px;"></div>
                        <svg class="relative h-16 w-16 text-white/25" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-slate-900/55 px-3 py-1.5 text-xs font-medium text-white" data-bulletin-caption>{{ $bulletinCaption($latest) }}</div>
                </div>
                <div class="mb-1 flex flex-wrap items-center gap-1.5" data-bulletin-badges>
                    <span data-bulletin-pin-badge class="inline-flex items-center gap-0.5 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 {{ $latestPinned ? '' : 'hidden' }}">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 9V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v5c0 1.66-1.34 3-3 3v2h5.97v7l1 1 1-1v-7H19v-2c-1.66 0-3-1.34-3-3z"/></svg>
                        Pinned
                    </span>
                    <span data-bulletin-type-badge class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $latestIsAnnouncement ? 'bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' }}">
                        {{ $latestIsAnnouncement ? 'Announcement' : 'Discussion' }}
                    </span>
                </div>
                <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 sm:text-2xl" data-bulletin-title>{{ $latest['title'] }}</h3>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400" data-bulletin-meta>{{ $latest['meta_label'] }}</p>
                <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300" data-bulletin-excerpt>{{ $latest['excerpt'] ?: $latest['message'] }}</p>
                <a href="{{ $latest['url'] ?? $listUrl }}" class="mt-4 inline-flex text-sm font-semibold text-sky-600 hover:text-sky-500 dark:text-sky-400" data-bulletin-more>See more</a>
            </div>
            <aside class="relative z-20 min-h-0 overflow-y-auto p-3 sm:p-4">
                <h4 class="mb-2 px-1 text-sm font-semibold text-slate-800 dark:text-slate-100">Previous</h4>
                <ul class="space-y-1" data-bulletin-list>
                    @foreach ($announcements as $item)
                        @php
                            $active = (int) ($item['id'] ?? 0) === (int) ($latest['id'] ?? 0);
                            $itemAnnouncement = $bulletinIsAnnouncement($item);
                            $itemPinned = ! empty($item['is_pinned']);
                        @endphp
                        <li>
                            <button
                                type="button"
                                class="bulletin-prev-item relative z-20 w-full cursor-pointer rounded-lg px-3 py-2.5 text-left transition-colors {{ $active ? 'bg-sky-50 dark:bg-sky-500/15' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50' }}"
                                data-id="{{ (int) $item['id'] }}"
                                data-title="{{ e($item['title'] ?? '') }}"
                                data-meta="{{ e($item['meta_label'] ?? '') }}"
                                data-excerpt="{{ e($item['excerpt'] ?: ($item['message'] ?? '')) }}"
                                data-image="{{ e($item['image_url'] ?? '') }}"
                                data-url="{{ e($item['url'] ?? $listUrl) }}"
                                data-date="{{ e($item['date_label'] ?? '—') }}"
                                data-caption="{{ e($bulletinCaption($item)) }}"
                                data-pinned="{{ $itemPinned ? '1' : '0' }}"
                                data-status="{{ $itemAnnouncement ? 'announcement' : 'discussion' }}"
                                aria-pressed="{{ $active ? 'true' : 'false' }}"
                            >
                                <div class="flex items-start gap-1.5">
                                    @if($itemPinned)
                                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 9V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v5c0 1.66-1.34 3-3 3v2h5.97v7l1 1 1-1v-7H19v-2c-1.66 0-3-1.34-3-3z"/></svg>
                                    @endif
                                    <p class="min-w-0 flex-1 truncate text-sm font-semibold {{ $active ? 'text-sky-800 dark:text-sky-200' : 'text-slate-800 dark:text-slate-100' }}" data-prev-title>{{ $item['title'] }}</p>
                                </div>
                                <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $itemAnnouncement ? 'Announcement' : 'Discussion' }}{{ !empty($item['date_label']) ? ' · '.$item['date_label'] : '' }}</p>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </aside>
        </div>
        <script>
        (function () {
            var root = document.currentScript && document.currentScript.closest('[data-bulletin-fallback]');
            if (!root || root.dataset.bound === '1') return;
            root.dataset.bound = '1';
            var listUrl = root.getAttribute('data-bulletin-list-url') || '';
            var titleEl = root.querySelector('[data-bulletin-title]');
            var metaEl = root.querySelector('[data-bulletin-meta]');
            var excerptEl = root.querySelector('[data-bulletin-excerpt]');
            var moreEl = root.querySelector('[data-bulletin-more]');
            var imgEl = root.querySelector('[data-bulletin-image]');
            var imgFallback = root.querySelector('[data-bulletin-image-fallback]');
            var captionEl = root.querySelector('[data-bulletin-caption]');
            var pinBadge = root.querySelector('[data-bulletin-pin-badge]');
            var typeBadge = root.querySelector('[data-bulletin-type-badge]');
            var buttons = root.querySelectorAll('.bulletin-prev-item');

            function setTypeBadge(isAnnouncement) {
                if (!typeBadge) return;
                typeBadge.textContent = isAnnouncement ? 'Announcement' : 'Discussion';
                typeBadge.className = 'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide ' +
                    (isAnnouncement
                        ? 'bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300'
                        : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300');
            }

            function setActive(btn) {
                buttons.forEach(function (b) {
                    var on = b === btn;
                    b.classList.toggle('bg-sky-50', on);
                    b.classList.toggle('dark:bg-sky-500/15', on);
                    b.classList.toggle('hover:bg-slate-50', !on);
                    b.classList.toggle('dark:hover:bg-slate-700/50', !on);
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                    var t = b.querySelector('[data-prev-title]');
                    if (t) {
                        t.classList.toggle('text-sky-800', on);
                        t.classList.toggle('dark:text-sky-200', on);
                        t.classList.toggle('text-slate-800', !on);
                        t.classList.toggle('dark:text-slate-100', !on);
                    }
                });
                if (titleEl) titleEl.textContent = btn.getAttribute('data-title') || '';
                if (metaEl) metaEl.textContent = btn.getAttribute('data-meta') || '';
                if (excerptEl) excerptEl.textContent = btn.getAttribute('data-excerpt') || '';
                if (moreEl) moreEl.setAttribute('href', btn.getAttribute('data-url') || listUrl);
                if (captionEl) captionEl.textContent = btn.getAttribute('data-caption') || '';
                if (pinBadge) pinBadge.classList.toggle('hidden', btn.getAttribute('data-pinned') !== '1');
                setTypeBadge(btn.getAttribute('data-status') === 'announcement');
                var image = (btn.getAttribute('data-image') || '').trim();
                if (imgEl && imgFallback) {
                    if (image) {
                        imgEl.src = image;
                        imgEl.classList.remove('hidden');
                        imgFallback.classList.add('hidden');
                    } else {
                        imgEl.removeAttribute('src');
                        imgEl.classList.add('hidden');
                        imgFallback.classList.remove('hidden');
                    }
                }
            }

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    setActive(btn);
                });
            });
        })();
        </script>
    @endif
</section>
