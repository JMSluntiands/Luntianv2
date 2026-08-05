@php
    $announcements = $dashboardAnnouncements ?? \App\Services\DashboardAnnouncementService::recentPayload(8);
    $latest = $announcements[0] ?? null;
    $listUrl = \Illuminate\Support\Facades\Route::has('forum_thread')
        ? route('forum_thread', [], false)
        : '/dashboard/forum-thread';
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
            <p class="text-sm text-slate-500 dark:text-slate-400">No bulletin announcements yet.</p>
            <a href="{{ $listUrl }}" class="text-sm font-semibold text-sky-600 hover:text-sky-500 dark:text-sky-400">Open Bulletin</a>
        </div>
    @else
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
                    <div class="absolute bottom-0 left-0 right-0 bg-slate-900/55 px-3 py-1.5 text-xs font-medium text-white">Latest announcement</div>
                </div>
                <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-400">Latest update</p>
                <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 sm:text-2xl" data-bulletin-title>{{ $latest['title'] }}</h3>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400" data-bulletin-meta>{{ $latest['meta_label'] }}</p>
                <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300" data-bulletin-excerpt>{{ $latest['excerpt'] ?: $latest['message'] }}</p>
                <a href="{{ $latest['url'] ?? $listUrl }}" class="mt-4 inline-flex text-sm font-semibold text-sky-600 hover:text-sky-500 dark:text-sky-400" data-bulletin-more>See more</a>
            </div>
            <aside class="relative z-20 min-h-0 overflow-y-auto p-3 sm:p-4">
                <h4 class="mb-2 px-1 text-sm font-semibold text-slate-800 dark:text-slate-100">Previous</h4>
                <ul class="space-y-1" data-bulletin-list>
                    @foreach ($announcements as $item)
                        @php $active = (int) ($item['id'] ?? 0) === (int) ($latest['id'] ?? 0); @endphp
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
                                aria-pressed="{{ $active ? 'true' : 'false' }}"
                            >
                                <p class="truncate text-sm font-semibold {{ $active ? 'text-sky-800 dark:text-sky-200' : 'text-slate-800 dark:text-slate-100' }}" data-prev-title>{{ $item['title'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $item['date_label'] ?? '—' }}</p>
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
            var buttons = root.querySelectorAll('.bulletin-prev-item');

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
