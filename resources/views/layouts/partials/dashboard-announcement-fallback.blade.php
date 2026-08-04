@php
    $announcements = $dashboardAnnouncements ?? \App\Services\DashboardAnnouncementService::recentPayload(8);
    $latest = $announcements[0] ?? null;
    $listUrl = route('forum_thread', [], false);
@endphp
<section class="flex h-full min-h-[28rem] min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90">
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
            <div class="min-w-0 border-b border-slate-200 p-4 dark:border-slate-700 sm:p-5 lg:border-b-0 lg:border-r">
                <div class="relative mb-4 overflow-hidden rounded-xl bg-gradient-to-br from-sky-500 via-blue-600 to-indigo-700">
                    @if(!empty($latest['image_url']))
                        <img src="{{ $latest['image_url'] }}" alt="" class="h-36 w-full object-cover sm:h-40">
                    @else
                        <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image: radial-gradient(circle, rgba(255,255,255,0.45) 1px, transparent 1px); background-size: 14px 14px;"></div>
                        <div class="relative flex h-36 items-center justify-center sm:h-40">
                            <svg class="h-16 w-16 text-white/25" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 bg-slate-900/55 px-3 py-1.5 text-xs font-medium text-white">Latest announcement</div>
                </div>
                <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-400">Latest update</p>
                <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 sm:text-2xl">{{ $latest['title'] }}</h3>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ $latest['meta_label'] }}</p>
                <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $latest['excerpt'] ?: $latest['message'] }}</p>
                <a href="{{ $latest['url'] ?? $listUrl }}" class="mt-4 inline-flex text-sm font-semibold text-sky-600 hover:text-sky-500 dark:text-sky-400">See more</a>
            </div>
            <aside class="min-h-0 overflow-y-auto p-3 sm:p-4">
                <h4 class="mb-2 px-1 text-sm font-semibold text-slate-800 dark:text-slate-100">Previous</h4>
                <ul class="space-y-1">
                    @foreach ($announcements as $item)
                        @php $active = (int) ($item['id'] ?? 0) === (int) ($latest['id'] ?? 0); @endphp
                        <li>
                            <div class="rounded-lg px-3 py-2.5 {{ $active ? 'bg-sky-50 dark:bg-sky-500/15' : '' }}">
                                <p class="truncate text-sm font-semibold {{ $active ? 'text-sky-800 dark:text-sky-200' : 'text-slate-800 dark:text-slate-100' }}">{{ $item['title'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $item['date_label'] ?? '—' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </aside>
        </div>
    @endif
</section>
