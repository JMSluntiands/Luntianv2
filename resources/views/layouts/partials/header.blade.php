<header class="header z-30 flex h-14 min-h-14 min-w-0 flex-shrink-0 items-center gap-2 overflow-visible border-b border-slate-200 bg-white px-3 dark:border-slate-700 dark:bg-slate-900 sm:px-4">
    <button type="button" id="sidebarToggle" class="icon-btn -ml-1 flex h-10 w-10 flex-shrink-0 cursor-pointer items-center justify-center rounded-lg border-0 bg-transparent text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 lg:hidden dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100" aria-expanded="false" aria-controls="sidebarNav" aria-label="Open menu">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    @php
        $activeAnnouncement = \App\Models\Announcement::query()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', now());
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();
    @endphp
    @if($activeAnnouncement)
        <div class="header-announcement flex min-w-0 flex-1 items-center">
            <div
                id="announcement-root"
                class="flex min-w-0 flex-1 items-center overflow-hidden"
                data-announcement-text="{{ $activeAnnouncement->message }}"
            >
                <div class="announcement-ticker" role="status" aria-live="polite">
                    <div class="announcement-ticker__track">
                        <div class="announcement-ticker__content" style="--ticker-duration: 9000ms;">
                            <span class="announcement-ticker__text">{{ $activeAnnouncement->message }}</span>
                        </div>
                    </div>
                </div>
                @yield('header_center')
            </div>
        </div>
    @else
        <div class="min-w-0 flex-1 lg:min-w-0" aria-hidden="true"></div>
    @endif
    <div class="header-actions relative z-10 flex flex-shrink-0 items-center gap-1 sm:gap-2">
        @yield('header_extra')
        @php
            $tagNotifications = collect();
            $replyNotifications = collect();
            if (\Illuminate\Support\Facades\Route::has('task_management.job_notes')) {
                try {
                    $noteUserId = (int) session('user_id', 0);
                    $noteCode = $noteUserId > 0
                        ? strtoupper(trim((string) (\App\Models\User::query()->where('id', $noteUserId)->value('unique_code') ?? '')))
                        : '';
                    if ($noteCode !== '') {
                        $tagNotifications = \App\Services\AllocatedJobsTaskFeed::unreadTaggedForUser($noteUserId, $noteCode);
                    }
                    if ($noteUserId > 0 && \Illuminate\Support\Facades\Schema::hasTable('discussion_reply_notifications')) {
                        $replyNotifications = \Illuminate\Support\Facades\DB::table('discussion_reply_notifications')
                            ->where('user_id', $noteUserId)
                            ->whereNull('read_at')
                            ->orderByDesc('id')
                            ->limit(30)
                            ->get();
                    }
                } catch (\Throwable) {
                    $tagNotifications = collect();
                    $replyNotifications = collect();
                }
            }
            $bellCount = $tagNotifications->count() + $replyNotifications->count();
        @endphp
        <div class="notification-wrap relative">
            <button type="button" class="icon-btn relative flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border-0 bg-transparent text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" id="notificationBtn" aria-label="Notifications" aria-expanded="false" aria-haspopup="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span id="notificationCount" @if($bellCount < 1) hidden @endif class="absolute right-0.5 top-0.5 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-semibold leading-none text-white">{{ $bellCount }}</span>
            </button>
            <div class="notification-dropdown absolute right-0 top-full z-[100] mt-2 max-h-[400px] min-w-[320px] max-w-[380px] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl opacity-0 invisible pointer-events-none transition-all duration-200 -translate-y-1 dark:border-slate-700 dark:bg-slate-800 dark:shadow-xl dark:shadow-black/20" id="notificationDropdown" role="menu" aria-label="Notifications menu">
                <div class="notification-dropdown-title border-b border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-600 dark:text-slate-200">Notifications</div>
                <div class="notification-dropdown-list max-h-[280px] overflow-y-auto" id="notificationList" data-notifications-url="{{ \Illuminate\Support\Facades\Route::has('notifications.live') ? route('notifications.live') : '' }}" data-job-notes-url="{{ \Illuminate\Support\Facades\Route::has('task_management.job_notes') ? route('task_management.job_notes') : '' }}" data-job-notes-read-url="{{ \Illuminate\Support\Facades\Route::has('task_management.job_notes.read') ? route('task_management.job_notes.read') : '' }}" data-notify-sound="{{ asset('sounds/tag-notify.mp3') }}">
                    @if($replyNotifications->isEmpty() && $tagNotifications->isEmpty())
                    <div class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400">No notifications</div>
                    @else
                    @foreach($replyNotifications as $replyNote)
                    <a href="{{ route('discussion.reply.open', $replyNote->id) }}" class="notification-item flex cursor-pointer items-start gap-2.5 border-b border-slate-100 px-4 py-3 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-700/50" role="menuitem">
                        <div class="notification-item-content min-w-0 flex-1">
                            <div class="notification-item-title font-medium text-slate-800 dark:text-slate-200">Replied in this thread</div>
                            @if(trim((string) ($replyNote->created_at ?? '')) !== '')
                            <p class="notification-item-time text-xs text-slate-500 dark:text-slate-400">{{ $replyNote->created_at }}</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                    @foreach($tagNotifications as $tagNote)
                    @php
                        $tagAuthor = trim((string) ($tagNote->author ?? ''));
                        $tagCode = strtoupper(trim((string) ($tagNote->author_code ?? '')));
                        $tagInitial = $tagCode !== '' ? mb_strtoupper(mb_substr($tagCode, 0, 1)) : ($tagAuthor !== '' ? mb_strtoupper(mb_substr($tagAuthor, 0, 1)) : 'L');
                        $tagWho = $tagCode !== '' ? $tagCode : $tagInitial;
                        $tagJobNumber = trim((string) ($tagNote->reference ?? ''));
                        if ($tagJobNumber === '—') {
                            $tagJobNumber = '';
                        }
                    @endphp
                    <a href="{{ !empty($tagNote->view_url) ? $tagNote->view_url : route('task_management.job_notes') }}" class="notification-item flex cursor-pointer items-start gap-2.5 border-b border-slate-100 px-4 py-3 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-700/50" role="menuitem">
                        <span class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-500 text-xs font-bold text-white">{{ $tagInitial }}</span>
                        <div class="notification-item-content min-w-0 flex-1">
                            <div class="notification-item-title font-medium text-slate-800 dark:text-slate-100">{{ $tagWho }} tagged you in {{ $tagJobNumber !== '' ? $tagJobNumber : 'this job' }}</div>
                        </div>
                    </a>
                    @endforeach
                    @endif
                </div>
                <div class="border-t border-slate-200 px-4 py-2.5 dark:border-slate-700">
                    <button type="button" id="enableNotificationsBtn" class="w-full cursor-pointer rounded-lg bg-slate-900 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Allow notifications</button>
                </div>
                @if(\Illuminate\Support\Facades\Route::has('task_management.job_notes'))
                <div class="notification-dropdown-footer border-t border-slate-200 bg-slate-50 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800/50">
                    <a href="{{ route('task_management.job_notes') }}" id="seeAllNotifications" class="block rounded-lg py-2 text-center text-sm font-medium text-emerald-600 no-underline transition-colors hover:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20">See all notifications</a>
                </div>
                @endif
            </div>
        </div>
        @include('layouts.partials.theme-toggle-button')
        <div class="relative">
            <button type="button" class="user-btn flex cursor-pointer items-center gap-2.5 rounded-xl border-0 bg-transparent px-3 py-2 text-slate-700 no-underline transition-all duration-200 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800" id="userMenuBtn" aria-expanded="false" aria-haspopup="true">
                @if(session('user_profile_image'))
                    <span class="user-avatar flex h-8 w-8 flex-shrink-0 overflow-hidden rounded-full ring-2 ring-slate-200 dark:ring-slate-600"><img src="{{ route('account.settings.image') }}" alt="" class="h-full w-full object-cover"></span>
                @else
                    <span class="user-avatar user-avatar-letter flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-700 dark:bg-slate-600 dark:text-slate-200 ring-2 ring-slate-200 dark:ring-slate-600" aria-hidden="true">{{ strtoupper(substr(session('user_name', 'User'), 0, 1)) }}</span>
                @endif
                <span class="hidden text-left text-sm font-medium md:inline">{{ session('user_name', 'User') }}</span>
                <svg class="h-4 w-4 flex-shrink-0 text-slate-500 transition-transform duration-200 dark:text-slate-400" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="user-dropdown absolute right-0 top-full z-[100] mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl opacity-0 invisible -translate-y-1 pointer-events-none transition-all duration-200 ease-out dark:border-slate-600 dark:bg-slate-800 dark:shadow-xl dark:shadow-black/20" id="userDropdown" role="menu" aria-label="User menu">
                <div class="border-b border-slate-100 px-3 py-2.5 dark:border-slate-600/80">
                    <p class="truncate text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Signed in as</p>
                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ session('user_name', 'User') }}</p>
                </div>
                <div class="p-1.5">
                    <a href="{{ route('account.settings.edit') }}" role="menuitem" class="flex items-center gap-2.5 rounded-md px-2.5 py-2.5 text-sm text-slate-700 no-underline transition-colors hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700/80">
                        <svg class="h-4 w-4 flex-shrink-0 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Account Settings</span>
                    </a>
                    <div class="my-1 border-t border-slate-100 dark:border-slate-600/80" role="separator"></div>
                    <a href="#" role="menuitem" id="logoutBtn" class="flex items-center gap-2.5 rounded-md px-2.5 py-2.5 text-sm text-slate-700 no-underline transition-colors hover:bg-red-50 hover:text-red-600 dark:text-slate-300 dark:hover:bg-red-950/40 dark:hover:text-red-400">
                        <svg class="h-4 w-4 flex-shrink-0 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Log out</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
