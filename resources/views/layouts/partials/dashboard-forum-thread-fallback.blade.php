@php
    $forumPosts = $dashboardForumPosts ?? \App\Http\Controllers\ForumThreadController::recentPostsPayload(8);
    $forumFeedUrl = route('forum_thread', [], false);
    $forumInitials = static function (string $name): string {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'U';
    };
@endphp
<section class="flex h-full min-h-[28rem] min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-lg dark:border-slate-700/60 dark:bg-slate-800/90">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 bg-slate-50/80 px-4 py-3 dark:border-slate-700/60 dark:bg-slate-800/50 sm:px-5">
        <h2 class="flex items-center gap-2.5 text-sm font-semibold text-slate-800 dark:text-slate-100 sm:text-base">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m1-4h8a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586"/>
                </svg>
            </span>
            Bulletin
        </h2>
        <a href="{{ $forumFeedUrl }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-600 dark:text-emerald-300 dark:hover:text-emerald-200 sm:text-sm">View all</a>
    </div>

    <div class="min-h-0 flex-1 overflow-y-auto">
        @forelse ($forumPosts as $post)
            <a href="{{ $forumFeedUrl }}#post-{{ $post['id'] }}" class="flex gap-3 border-b border-slate-100 px-4 py-3 transition-colors hover:bg-slate-50 dark:border-slate-700/70 dark:hover:bg-slate-700/40 sm:px-5 last:border-b-0">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-emerald-500/20 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                    @if(!empty($post['avatar']))
                        <img src="{{ $post['avatar'] }}" alt="" class="h-full w-full object-cover">
                    @else
                        {{ $forumInitials((string) ($post['author'] ?? 'User')) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $post['author'] ?? 'User' }}</span>
                        @if(!empty($post['created_at']))
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $post['created_at'] }}</span>
                        @endif
                    </div>
                    @if(!empty($post['excerpt']))
                        <p class="mt-0.5 line-clamp-2 text-sm leading-snug text-slate-600 dark:text-slate-300">{{ $post['excerpt'] }}</p>
                    @elseif(!empty($post['has_image']))
                        <p class="mt-0.5 text-sm italic text-slate-500 dark:text-slate-400">Shared a photo</p>
                    @endif
                    <p class="mt-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                        {{ (int) ($post['comments_count'] ?? 0) }} {{ (int) ($post['comments_count'] ?? 0) === 1 ? 'comment' : 'comments' }}
                        @if(!empty($post['has_image'])) · Photo @endif
                    </p>
                </div>
            </a>
        @empty
            <div class="flex min-h-[14rem] flex-col items-center justify-center gap-2 px-4 py-8 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">No posts yet.</p>
                <a href="{{ $forumFeedUrl }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">Start a conversation</a>
            </div>
        @endforelse
    </div>
</section>
