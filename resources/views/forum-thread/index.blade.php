@extends('layouts.dashboard')

@section('title', 'Bulletin')

@section('body_class', 'page-forum-thread')

@section('content')
@php
    $displayName = static function ($user): string {
        if (! $user) {
            return 'Unknown';
        }
        $name = trim((string) ($user->fullname ?? ''));
        if ($name !== '') {
            return $name;
        }
        $username = trim((string) ($user->username ?? ''));

        return $username !== '' ? $username : 'User';
    };
    $initials = static function ($user) use ($displayName): string {
        $name = $displayName($user);
        $parts = preg_split('/\s+/', $name) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'U';
    };
    $avatarUrl = static function ($user): ?string {
        $img = trim((string) ($user->profile_image ?? ''));
        if ($img === '') {
            return null;
        }
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '/')) {
            return $img;
        }

        return asset('storage/'.$img);
    };
@endphp

    <div class="w-full max-w-full px-0">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="mb-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Bulletin</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Share updates and join the conversation.</p>
            </div>
            @if($canPost)
                <button
                    type="button"
                    id="bulletinNewPostBtn"
                    class="cursor-pointer inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New post
                </button>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 items-stretch gap-5 lg:grid-cols-12 lg:gap-6">
            {{-- LEFT: previous posts list (fixed height + inner scroll) --}}
            <aside class="flex lg:col-span-4">
                <div class="flex w-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/70 h-[min(70vh,40rem)] lg:h-[calc(100vh-11.5rem)] lg:min-h-[32rem]">
                    <div class="shrink-0 border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Previous posts</h2>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Select a post to view details</p>
                    </div>

                    <div class="min-h-0 flex-1 space-y-2 overflow-y-auto overscroll-contain p-3">
                        @forelse($posts as $post)
                            @php
                                $author = $post->user;
                                $isAnnouncement = $post->isAnnouncement();
                                $isPinned = $post->isPinned();
                                $postTitle = trim((string) ($post->title ?? ''));
                                $excerpt = \Illuminate\Support\Str::limit(trim(strip_tags((string) $post->body)), 80);
                                $listLabel = $postTitle !== '' ? $postTitle : ($excerpt !== '' ? $excerpt : 'Untitled post');
                                $authorInitials = $initials($author);
                                $authorAvatar = $avatarUrl($author);
                            @endphp
                            <button
                                type="button"
                                class="bulletin-list-item group flex w-full cursor-pointer items-start gap-3 rounded-xl border bg-slate-50/80 px-3 py-3 text-left shadow-sm transition-all hover:bg-white dark:bg-slate-900/50 dark:hover:bg-slate-900/80 {{ $isPinned ? 'border-amber-300/90 hover:border-amber-400 dark:border-amber-500/40 dark:hover:border-amber-400/60' : 'border-slate-200/90 hover:border-slate-300 dark:border-slate-600/80 dark:hover:border-slate-500' }}"
                                data-post-id="{{ $post->id }}"
                                aria-controls="bulletinDetail-{{ $post->id }}"
                            >
                                <div class="relative mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-300 text-xs font-semibold text-slate-700 dark:bg-slate-600 dark:text-slate-100">
                                    <span class="bulletin-avatar-fallback absolute inset-0 flex items-center justify-center">{{ $authorInitials }}</span>
                                    @if($authorAvatar)
                                        <img
                                            src="{{ $authorAvatar }}"
                                            alt=""
                                            class="relative z-[1] h-full w-full object-cover"
                                            onerror="this.classList.add('hidden'); this.previousElementSibling?.classList.remove('hidden');"
                                        >
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="truncate text-xs font-semibold text-slate-900 dark:text-slate-100">{{ $displayName($author) }}</span>
                                        @if($isPinned)
                                            <span class="inline-flex items-center gap-0.5 rounded-md bg-amber-500/15 px-1.5 py-0.5 text-[9px] font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/20 dark:text-amber-300" title="Pinned">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 9V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v5c0 1.66-1.34 3-3 3v2h5.97v7l1 1 1-1v-7H19v-2c-1.66 0-3-1.34-3-3z"/></svg>
                                                Pinned
                                            </span>
                                        @endif
                                        @if($isAnnouncement)
                                            <span class="inline-flex items-center rounded-md bg-sky-500/15 px-1.5 py-0.5 text-[9px] font-semibold uppercase tracking-wide text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">Announcement</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-emerald-500/15 px-1.5 py-0.5 text-[9px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Discussion</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 line-clamp-2 text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">{{ $listLabel }}</p>
                                    <p class="mt-1 text-[0.7rem] text-slate-500 dark:text-slate-400" title="{{ $post->created_at?->timezone('Asia/Manila')->format('M j, Y g:i A') }}">
                                        {{ $post->created_at?->timezone('Asia/Manila')->format('M j, Y · g:i A') }}
                                    </p>
                                </div>
                            </button>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 px-3 py-10 text-center dark:border-slate-600">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">No posts yet</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Be the first to share something.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($posts->hasPages())
                        <div class="shrink-0 border-t border-slate-100 px-3 py-3 dark:border-slate-700">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>
            </aside>

            {{-- RIGHT: composer (default) or post detail --}}
            <div class="lg:col-span-8">
                {{-- Composer panel --}}
                @if($canPost)
                    @php
                        $oldType = old('post_type', 'announcement');
                        $editingId = (int) old('editing_post_id', 0);
                        $composerLabel = $editingId > 0
                            ? ($oldType === 'discussion' ? 'Edit Discussion' : 'Edit Announcement')
                            : ($oldType === 'discussion' ? 'Post Discussion' : 'Post Announcement');
                    @endphp
                    <div id="bulletinComposerPanel" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/70 sm:p-6">
                        <form method="POST" action="{{ $editingId > 0 ? route('forum_thread.update', $editingId) : route('forum_thread.store') }}" enctype="multipart/form-data" class="space-y-5" id="forumComposerForm"
                            data-store-url="{{ route('forum_thread.store') }}">
                            @csrf
                            <input type="hidden" name="_method" id="forumComposerMethod" value="PUT" @disabled($editingId <= 0)>
                            <input type="hidden" name="editing_post_id" id="forumEditingPostId" value="{{ $editingId > 0 ? $editingId : '' }}" @disabled($editingId <= 0)>
                            <input type="hidden" name="remove_image" id="forumRemoveImage" value="{{ old('remove_image') ? '1' : '0' }}">

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h2 id="forumComposerHeading" class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                    {{ $composerLabel }}
                                </h2>
                                <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-slate-50 p-0.5 dark:border-slate-600 dark:bg-slate-900/40" role="group" aria-label="Post type">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="post_type" value="announcement" class="peer sr-only forum-type-radio" @checked($oldType === 'announcement')>
                                        <span class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-semibold text-slate-600 transition-colors peer-checked:bg-white peer-checked:text-sky-700 peer-checked:shadow-sm dark:text-slate-300 dark:peer-checked:bg-slate-700 dark:peer-checked:text-sky-300">Announcement</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="post_type" value="discussion" class="peer sr-only forum-type-radio" @checked($oldType === 'discussion')>
                                        <span class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-semibold text-slate-600 transition-colors peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm dark:text-slate-300 dark:peer-checked:bg-slate-700 dark:peer-checked:text-emerald-300">Discussion</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="forumPostTitle" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-200">Title</label>
                                <input
                                    type="text"
                                    id="forumPostTitle"
                                    name="title"
                                    value="{{ old('title') }}"
                                    maxlength="200"
                                    required
                                    placeholder="Enter a title"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-400/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-100 dark:placeholder-slate-500"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-200">Cover image</label>
                                <input type="file" id="forumPostImage" name="image" accept="image/jpeg,image/png,image/gif,image/webp,.jpg,.jpeg,.png,.gif,.webp" class="hidden">
                                <label
                                    for="forumPostImage"
                                    id="forumCoverUploadBtn"
                                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:border-slate-400 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800"
                                >
                                    <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Upload image
                                </label>
                                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Optional. JPG, PNG, or GIF up to 4 MB.</p>
                                <div id="forumImagePreviewWrap" class="mt-3 hidden">
                                    <div class="relative inline-block max-w-full overflow-hidden rounded-xl border border-slate-200 dark:border-slate-600">
                                        <img id="forumImagePreview" src="" alt="Cover preview" class="max-h-56 max-w-full object-contain">
                                        <button type="button" id="forumImageClear" class="absolute right-2 top-2 cursor-pointer rounded-full bg-black/60 px-2 py-1 text-xs font-medium text-white hover:bg-black/80">Remove</button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="forumPostEditor" class="mb-1.5 block text-sm font-semibold text-slate-800 dark:text-slate-200">Description</label>
                                <div class="overflow-hidden rounded-lg border border-slate-300 dark:border-slate-600">
                                    <div class="flex flex-wrap items-center gap-0.5 border-b border-slate-200 bg-slate-50 px-2 py-1.5 dark:border-slate-600 dark:bg-slate-800/80">
                                        <button type="button" class="forum-rt-btn rounded px-2.5 py-1.5 text-sm font-bold text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700" data-cmd="bold" title="Bold">B</button>
                                        <button type="button" class="forum-rt-btn rounded px-2.5 py-1.5 text-sm italic text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700" data-cmd="italic" title="Italic">I</button>
                                        <button type="button" class="forum-rt-btn rounded px-2.5 py-1.5 text-sm underline text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700" data-cmd="underline" title="Underline">U</button>
                                        <span class="mx-1 h-5 w-px bg-slate-300 dark:bg-slate-600"></span>
                                        <button type="button" class="forum-rt-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700" data-cmd="insertUnorderedList" title="Bullets">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
                                        </button>
                                        <button type="button" class="forum-rt-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700" data-cmd="insertOrderedList" title="Numbered list">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h12M9 12h12M9 19h12M3 5h.01M3 12h.01M3 19h.01"/></svg>
                                        </button>
                                        <span class="mx-1 h-5 w-px bg-slate-300 dark:bg-slate-600"></span>
                                        <button type="button" class="forum-rt-btn rounded px-2.5 py-1.5 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700" data-cmd="createLink" title="Link">Link</button>
                                        <button type="button" class="rounded px-2.5 py-1.5 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700" id="forumToolbarImageBtn" title="Insert pictures into the text (you can also paste or drop them)">Pictures</button>
                                        <input type="file" id="forumInlineImageInput" accept="image/jpeg,image/png,image/gif,image/webp,.jpg,.jpeg,.png,.gif,.webp" multiple class="hidden">
                                    </div>
                                    <input type="hidden" name="body" id="forumPostBody" value="">
                                    <div
                                        id="forumPostEditor"
                                        contenteditable="true"
                                        data-placeholder="Write the announcement details..."
                                        data-upload-url="{{ route('forum_thread.inline_image') }}"
                                        class="min-h-[140px] bg-white px-3.5 py-3 text-sm text-slate-800 focus:outline-none dark:bg-slate-900/50 dark:text-slate-100 [&_img]:my-2 [&_img]:block [&_img]:max-h-72 [&_img]:max-w-full [&_img]:rounded-lg [&_img.is-uploading]:opacity-50 [&:empty::before]:pointer-events-none [&:empty::before]:text-slate-400 [&:empty::before]:content-[attr(data-placeholder)] dark:[&:empty::before]:text-slate-500"
                                    >{!! old('body') !!}</div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <button
                                    type="submit"
                                    id="forumSubmitBtn"
                                    class="cursor-pointer inline-flex items-center justify-center rounded-md bg-slate-900 px-5 py-2.5 text-xs font-bold uppercase tracking-wide text-white transition-colors hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500/40 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                                >
                                    {{ $editingId > 0 ? 'Save changes' : $composerLabel }}
                                </button>
                                <button
                                    type="button"
                                    id="forumCancelBtn"
                                    class="cursor-pointer inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold uppercase tracking-wide text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400/30 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div id="bulletinEmptyPanel" class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Select a post</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Choose a previous post on the left to view its details.</p>
                    </div>
                @endif

                {{-- Detail panels (one per post, hidden by default) --}}
                @foreach($posts as $post)
                    @php
                        $author = $post->user;
                        $canRemovePost = $isAdmin || ($canDeletePost && (int) $post->user_id === (int) $currentUserId);
                        $canEditPost = $canPost && ($isAdmin || (int) $post->user_id === (int) $currentUserId);
                        $imageUrl = $post->imageUrl();
                        $allowsComments = $post->allowsComments();
                        $isAnnouncement = $post->isAnnouncement();
                        $isPinned = $post->isPinned();
                        $mayPin = $canPin ?? false;
                    @endphp
                    <article
                        id="bulletinDetail-{{ $post->id }}"
                        class="bulletin-detail-panel hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/70"
                        data-post-id="{{ $post->id }}"
                    >
                        <div class="flex items-start gap-3 px-4 pt-4 sm:px-5">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-200 text-sm font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                @if($avatarUrl($author))
                                    <img src="{{ $avatarUrl($author) }}" alt="" class="h-full w-full object-cover">
                                @else
                                    {{ $initials($author) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $displayName($author) }}</p>
                                            @if($isPinned)
                                                <span class="inline-flex items-center gap-0.5 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 9V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v5c0 1.66-1.34 3-3 3v2h5.97v7l1 1 1-1v-7H19v-2c-1.66 0-3-1.34-3-3z"/></svg>
                                                    Pinned
                                                </span>
                                            @endif
                                            @if($isAnnouncement)
                                                <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-sky-700 dark:bg-sky-500/20 dark:text-sky-300">Announcement</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Discussion</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400" title="{{ $post->created_at?->timezone('Asia/Manila')->format('M j, Y g:i A') }}">
                                            {{ $post->created_at?->timezone('Asia/Manila')->format('M j, Y · g:i A') }}
                                        </p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        @if($mayPin)
                                            <form method="POST" action="{{ route('forum_thread.pin', $post->id) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex cursor-pointer items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium transition-colors {{ $isPinned ? 'bg-amber-500/10 text-amber-800 hover:bg-amber-500/20 dark:text-amber-300' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200' }}"
                                                    title="{{ $isPinned ? 'Unpin this post' : 'Pin this post to the top' }}"
                                                >
                                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 9V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v5c0 1.66-1.34 3-3 3v2h5.97v7l1 1 1-1v-7H19v-2c-1.66 0-3-1.34-3-3z"/></svg>
                                                    {{ $isPinned ? 'Unpin' : 'Pin' }}
                                                </button>
                                            </form>
                                        @endif
                                        @if($canEditPost)
                                            <button
                                                type="button"
                                                class="bulletin-edit-btn cursor-pointer rounded-lg px-2 py-1 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200"
                                                data-post-id="{{ $post->id }}"
                                                data-update-url="{{ route('forum_thread.update', $post->id) }}"
                                                data-title="{{ $post->title }}"
                                                data-post-type="{{ $post->normalizeType() }}"
                                                data-image-url="{{ $imageUrl }}"
                                            >
                                                Edit
                                            </button>
                                            <template id="bulletinEditBody-{{ $post->id }}">{!! preg_match('/<(p|br|div|ul|ol|li|b|strong|i|em|u|a|img)\b/i', (string) $post->body) ? $post->body : nl2br(e((string) $post->body)) !!}</template>
                                        @endif
                                        @if($canRemovePost)
                                            <form method="POST" action="{{ route('forum_thread.destroy', $post->id) }}" onsubmit="return confirm('Delete this post?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="cursor-pointer rounded-lg px-2 py-1 text-xs font-medium text-slate-500 transition-colors hover:bg-red-500/10 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                @php $postTitle = trim((string) ($post->title ?? '')); @endphp
                                @if($postTitle !== '')
                                    <h3 class="mt-3 text-base font-bold tracking-tight text-slate-900 dark:text-slate-100">{{ $postTitle }}</h3>
                                @endif
                                @if(trim(strip_tags((string) $post->body)) !== '' || stripos((string) $post->body, '<img') !== false)
                                    @php
                                        $rawBody = (string) $post->body;
                                        $looksRich = (bool) preg_match('/<(p|br|div|ul|ol|li|b|strong|i|em|u|a|img)\b/i', $rawBody);
                                    @endphp
                                    <div class="forum-post-body mt-2 break-words text-sm leading-relaxed text-slate-800 dark:text-slate-200 [&_a]:text-sky-600 [&_a]:underline dark:[&_a]:text-sky-400 [&_ol]:my-2 [&_ol]:list-decimal [&_ol]:pl-5 [&_ul]:my-2 [&_ul]:list-disc [&_ul]:pl-5 [&_p]:my-1 [&_img]:my-2 [&_img]:block [&_img]:max-h-[28rem] [&_img]:max-w-full [&_img]:cursor-zoom-in [&_img]:rounded-lg [&_img]:border [&_img]:border-slate-200 dark:[&_img]:border-slate-700">
                                        {!! $looksRich ? $rawBody : nl2br(e($rawBody)) !!}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($imageUrl)
                            <div class="mt-3 overflow-hidden border-y border-slate-100 dark:border-slate-700">
                                <a href="{{ $imageUrl }}" target="_blank" rel="noopener" class="block bg-slate-50 dark:bg-slate-900/40">
                                    <img src="{{ $imageUrl }}" alt="Post image" class="mx-auto max-h-[32rem] w-full object-contain">
                                </a>
                            </div>
                        @endif

                        <div class="mt-0 flex items-center gap-4 border-t border-slate-100 px-4 py-2 text-xs font-medium text-slate-500 dark:border-slate-700 dark:text-slate-400 sm:px-5 {{ $imageUrl ? '' : 'mt-3' }}">
                            @if($allowsComments)
                                <span>{{ (int) $post->comments_count }} {{ (int) $post->comments_count === 1 ? 'comment' : 'comments' }}</span>
                            @else
                                <span>Comments disabled</span>
                            @endif
                        </div>

                        {{-- Comments (discussion only) --}}
                        @if($allowsComments)
                        <div class="space-y-3 border-t border-slate-100 px-4 py-3 dark:border-slate-700 sm:px-5">
                            @foreach($post->comments as $comment)
                                @php
                                    $cAuthor = $comment->user;
                                    $canRemoveComment = $isAdmin || ($canDeleteComment && (int) $comment->user_id === (int) $currentUserId);
                                @endphp
                                <div class="flex gap-2.5">
                                    <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-200 text-[0.7rem] font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                        @if($avatarUrl($cAuthor))
                                            <img src="{{ $avatarUrl($cAuthor) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            {{ $initials($cAuthor) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="rounded-2xl bg-slate-100 px-3 py-2 dark:bg-slate-900/60">
                                            <div class="flex items-start justify-between gap-2">
                                                <p class="text-xs font-semibold text-slate-900 dark:text-slate-100">{{ $displayName($cAuthor) }}</p>
                                                @if($canRemoveComment)
                                                    <form method="POST" action="{{ route('forum_thread.comment.destroy', $comment->id) }}" onsubmit="return confirm('Delete this comment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="cursor-pointer text-[0.65rem] font-medium text-slate-400 hover:text-red-500">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                            <p class="mt-0.5 whitespace-pre-wrap break-words text-sm text-slate-700 dark:text-slate-300">{{ $comment->body }}</p>
                                        </div>
                                        <p class="mt-1 px-1 text-[0.7rem] text-slate-400" title="{{ $comment->created_at?->timezone('Asia/Manila')->format('M j, Y g:i A') }}">
                                            {{ $comment->created_at?->timezone('Asia/Manila')->format('M j, Y · g:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            @if($canComment)
                                <form method="POST" action="{{ route('forum_thread.comment.store', $post->id) }}" class="flex items-start gap-2.5 pt-1">
                                    @csrf
                                    <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-emerald-500/20 text-[0.7rem] font-semibold text-emerald-700 dark:text-emerald-300">
                                        @if($avatarUrl($currentUser))
                                            <img src="{{ $avatarUrl($currentUser) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            {{ $initials($currentUser) }}
                                        @endif
                                    </div>
                                    <div class="flex min-w-0 flex-1 gap-2">
                                        <input
                                            type="text"
                                            name="body"
                                            required
                                            maxlength="2000"
                                            placeholder="Write a comment…"
                                            class="w-full rounded-full border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-100 dark:placeholder-slate-500"
                                        >
                                        <button type="submit" class="cursor-pointer shrink-0 rounded-full bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white transition-colors hover:bg-emerald-500">
                                            Reply
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                        @endif
                    </article>
                @endforeach

                @if($posts->isEmpty() && ! $canPost)
                    <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m0-4V6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H9l-4 4V8z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">No posts yet</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Be the first to share something with the team.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('forumPostImage');
    var preview = document.getElementById('forumImagePreview');
    var wrap = document.getElementById('forumImagePreviewWrap');
    var clearBtn = document.getElementById('forumImageClear');
    var form = document.getElementById('forumComposerForm');
    var body = document.getElementById('forumPostBody');
    var editor = document.getElementById('forumPostEditor');
    var titleInput = document.getElementById('forumPostTitle');
    var heading = document.getElementById('forumComposerHeading');
    var submitBtn = document.getElementById('forumSubmitBtn');
    var cancelBtn = document.getElementById('forumCancelBtn');
    var toolbarImageBtn = document.getElementById('forumToolbarImageBtn');
    var typeRadios = document.querySelectorAll('.forum-type-radio');
    var rtBtns = document.querySelectorAll('.forum-rt-btn');
    var methodInput = document.getElementById('forumComposerMethod');
    var editingIdInput = document.getElementById('forumEditingPostId');
    var removeImageInput = document.getElementById('forumRemoveImage');

    function editingPostId() {
        return editingIdInput && !editingIdInput.disabled ? editingIdInput.value : '';
    }

    function isEmptyHtml(html) {
        var tmp = document.createElement('div');
        tmp.innerHTML = html || '';
        if (tmp.querySelector('img')) return false;
        var text = (tmp.textContent || tmp.innerText || '').replace(/\u00a0/g, ' ').trim();
        return text === '';
    }

    function syncBody() {
        if (body && editor) {
            body.value = isEmptyHtml(editor.innerHTML) ? '' : editor.innerHTML;
        }
    }

    function selectedType() {
        var checked = document.querySelector('.forum-type-radio:checked');
        return checked ? checked.value : 'announcement';
    }

    function syncTypeLabels() {
        var isDiscussion = selectedType() === 'discussion';
        var isEditing = editingPostId() !== '';
        var label = (isEditing ? 'Edit ' : 'Post ') + (isDiscussion ? 'Discussion' : 'Announcement');
        if (heading) heading.textContent = label;
        if (submitBtn) submitBtn.textContent = isEditing ? 'Save changes' : label;
        if (editor) {
            editor.setAttribute('data-placeholder', isDiscussion
                ? 'Write the discussion details...'
                : 'Write the announcement details...');
        }
    }

    typeRadios.forEach(function (radio) {
        radio.addEventListener('change', syncTypeLabels);
    });
    syncTypeLabels();

    function updateRtActiveState() {
        rtBtns.forEach(function (btn) {
            var cmd = btn.getAttribute('data-cmd');
            if (cmd === 'createLink') return;
            var active = false;
            try { active = document.queryCommandState(cmd); } catch (e) {}
            btn.classList.toggle('bg-slate-200', active);
            btn.classList.toggle('text-slate-900', active);
            btn.classList.toggle('dark:bg-slate-700', active);
            btn.classList.toggle('dark:text-white', active);
        });
    }

    rtBtns.forEach(function (btn) {
        btn.addEventListener('mousedown', function (e) {
            e.preventDefault();
            var cmd = this.getAttribute('data-cmd');
            if (!cmd || !editor) return;
            editor.focus();
            if (cmd === 'createLink') {
                var url = window.prompt('Enter link URL', 'https://');
                if (url && url.trim() !== '') {
                    document.execCommand('createLink', false, url.trim());
                }
            } else {
                document.execCommand(cmd, false, null);
            }
            syncBody();
            updateRtActiveState();
        });
    });

    var inlineInput = document.getElementById('forumInlineImageInput');
    var savedRange = null;
    var pendingUploads = 0;

    function rememberCaret() {
        var sel = window.getSelection();
        if (editor && sel && sel.rangeCount > 0 && editor.contains(sel.getRangeAt(0).commonAncestorContainer)) {
            savedRange = sel.getRangeAt(0).cloneRange();
        }
    }

    function insertNodeAtCaret(node) {
        if (!editor) return;
        var range = savedRange;
        if (!range || !editor.contains(range.commonAncestorContainer)) {
            range = document.createRange();
            range.selectNodeContents(editor);
            range.collapse(false);
        }
        range.deleteContents();
        range.insertNode(node);
        range.setStartAfter(node);
        range.collapse(true);
        var sel = window.getSelection();
        if (sel) {
            sel.removeAllRanges();
            sel.addRange(range);
        }
        savedRange = range.cloneRange();
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function uploadInlineImage(file) {
        if (!editor || !file) return;
        var placeholder = document.createElement('img');
        placeholder.className = 'is-uploading';
        placeholder.alt = '';
        placeholder.src = URL.createObjectURL(file);
        insertNodeAtCaret(placeholder);
        pendingUploads++;
        syncBody();

        compressImage(file).then(function (ready) {
            var fd = new FormData();
            fd.append('image', ready);
            return fetch(editor.getAttribute('data-upload-url'), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body: fd
            }).then(function (res) {
                return res.json().catch(function () { return {}; }).then(function (data) {
                    if (!res.ok || !data.url) {
                        var msg = data.message || (data.errors && data.errors.image && data.errors.image[0]) || 'Could not upload the picture.';
                        throw new Error(msg);
                    }
                    return data.url;
                });
            });
        }).then(function (url) {
            URL.revokeObjectURL(placeholder.src);
            placeholder.src = url;
            placeholder.classList.remove('is-uploading');
            placeholder.removeAttribute('class');
        }).catch(function (err) {
            URL.revokeObjectURL(placeholder.src);
            placeholder.remove();
            toast((err && err.message) || 'Could not upload the picture.');
        }).then(function () {
            pendingUploads = Math.max(0, pendingUploads - 1);
            syncBody();
        });
    }

    function imageFilesFrom(list) {
        return Array.prototype.filter.call(list || [], function (f) {
            return f && /^image\//i.test(f.type || '');
        });
    }

    if (toolbarImageBtn && inlineInput) {
        toolbarImageBtn.addEventListener('mousedown', function (e) {
            e.preventDefault();
            rememberCaret();
        });
        toolbarImageBtn.addEventListener('click', function (e) {
            e.preventDefault();
            inlineInput.click();
        });
        inlineInput.addEventListener('change', function () {
            imageFilesFrom(inlineInput.files).forEach(uploadInlineImage);
            inlineInput.value = '';
        });
    }

    if (editor) {
        ['focus', 'keyup', 'mouseup', 'input'].forEach(function (evt) {
            editor.addEventListener(evt, function () {
                rememberCaret();
                syncBody();
                updateRtActiveState();
            });
        });

        editor.addEventListener('paste', function (e) {
            var cd = e.clipboardData;
            var files = imageFilesFrom(cd && cd.files);
            if (!files.length && cd && cd.items) {
                Array.prototype.forEach.call(cd.items, function (item) {
                    if (item.kind === 'file' && /^image\//i.test(item.type)) {
                        var f = item.getAsFile();
                        if (f) files.push(f);
                    }
                });
            }
            if (files.length) {
                e.preventDefault();
                rememberCaret();
                files.forEach(uploadInlineImage);
                return;
            }
            window.setTimeout(adoptPastedImages, 0);
        });

        editor.addEventListener('dragover', function (e) {
            if (e.dataTransfer && Array.prototype.some.call(e.dataTransfer.types || [], function (t) { return t === 'Files'; })) {
                e.preventDefault();
            }
        });
        editor.addEventListener('drop', function (e) {
            var files = imageFilesFrom(e.dataTransfer && e.dataTransfer.files);
            if (!files.length) return;
            e.preventDefault();
            var range = null;
            if (document.caretRangeFromPoint) {
                range = document.caretRangeFromPoint(e.clientX, e.clientY);
            } else if (document.caretPositionFromPoint) {
                var pos = document.caretPositionFromPoint(e.clientX, e.clientY);
                if (pos) {
                    range = document.createRange();
                    range.setStart(pos.offsetNode, pos.offset);
                }
            }
            if (range && editor.contains(range.commonAncestorContainer)) savedRange = range;
            files.forEach(uploadInlineImage);
        });

        syncBody();
    }

    // Pictures pasted as HTML (e.g. copied from a web page) point elsewhere; re-upload embedded ones and drop the rest.
    function adoptPastedImages() {
        if (!editor) return;
        var dropped = 0;
        editor.querySelectorAll('img').forEach(function (img) {
            var src = img.getAttribute('src') || '';
            if (img.classList.contains('is-uploading') || src.indexOf('/dashboard/forum-thread/image/inline_') !== -1) return;
            if (/^data:image\//i.test(src)) {
                fetch(src).then(function (r) { return r.blob(); }).then(function (blob) {
                    var marker = document.createElement('span');
                    img.replaceWith(marker);
                    var range = document.createRange();
                    range.selectNode(marker);
                    savedRange = range;
                    uploadInlineImage(new File([blob], 'pasted.' + ((blob.type.split('/')[1] || 'png').replace('jpeg', 'jpg')), { type: blob.type || 'image/png' }));
                }).catch(function () { img.remove(); });
                return;
            }
            img.remove();
            dropped++;
        });
        if (dropped) {
            toast('Some pasted pictures could not be copied. Use "Copy image" and paste again, or add them with the Pictures button.');
        }
        syncBody();
    }

    function clearImage() {
        if (input) input.value = '';
        if (preview) preview.src = '';
        if (wrap) wrap.classList.add('hidden');
        if (removeImageInput && editingPostId() !== '') removeImageInput.value = '1';
    }

    function showImagePreview(url) {
        if (!preview || !wrap || !url) return;
        preview.src = url;
        wrap.classList.remove('hidden');
    }

    function setEditing(btn) {
        var id = btn ? btn.getAttribute('data-post-id') : '';
        if (form) form.action = btn ? btn.getAttribute('data-update-url') : form.getAttribute('data-store-url');
        if (methodInput) methodInput.disabled = !btn;
        if (editingIdInput) {
            editingIdInput.value = id || '';
            editingIdInput.disabled = !btn;
        }
        if (removeImageInput) removeImageInput.value = '0';
    }

    function resetComposer() {
        setEditing(null);
        if (titleInput) titleInput.value = '';
        if (editor) editor.innerHTML = '';
        syncBody();
        clearImage();
        var announcementRadio = document.querySelector('.forum-type-radio[value="announcement"]');
        if (announcementRadio) {
            announcementRadio.checked = true;
            syncTypeLabels();
        }
    }

    function toast(msg) {
        if (window.showSuccessToast) window.showSuccessToast(msg);
        else alert(msg);
    }

    function setInputFile(file) {
        if (!input || !file) return;
        try {
            var dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        } catch (err) {
            /* keep original selection if DataTransfer unsupported */
        }
    }

    function compressImage(file) {
        return new Promise(function (resolve, reject) {
            var type = (file.type || '').toLowerCase();
            var allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (allowed.indexOf(type) === -1) {
                reject(new Error('Please choose a JPG, PNG, GIF, or WebP image.'));
                return;
            }
            if (type === 'image/gif') {
                if (file.size > 4 * 1024 * 1024) {
                    reject(new Error('GIF is too large. Please use a file under 4MB.'));
                } else {
                    resolve(file);
                }
                return;
            }
            if (file.size <= 1.5 * 1024 * 1024) {
                resolve(file);
                return;
            }

            var img = new Image();
            var url = URL.createObjectURL(file);
            img.onload = function () {
                URL.revokeObjectURL(url);
                var maxSide = 1920;
                var w = img.naturalWidth || img.width;
                var h = img.naturalHeight || img.height;
                var scale = Math.min(1, maxSide / Math.max(w, h));
                var cw = Math.max(1, Math.round(w * scale));
                var ch = Math.max(1, Math.round(h * scale));
                var canvas = document.createElement('canvas');
                canvas.width = cw;
                canvas.height = ch;
                var ctx = canvas.getContext('2d');
                if (!ctx) {
                    resolve(file);
                    return;
                }
                ctx.drawImage(img, 0, 0, cw, ch);

                var quality = 0.82;
                var tryBlob = function () {
                    canvas.toBlob(function (blob) {
                        if (!blob) {
                            resolve(file);
                            return;
                        }
                        if (blob.size > 3.8 * 1024 * 1024 && quality > 0.5) {
                            quality -= 0.12;
                            tryBlob();
                            return;
                        }
                        if (blob.size > 4 * 1024 * 1024) {
                            reject(new Error('Image is still too large after compression. Try a smaller photo.'));
                            return;
                        }
                        var base = (file.name || 'photo').replace(/\.[^.]+$/, '') || 'photo';
                        var outType = type === 'image/png' ? 'image/png' : 'image/jpeg';
                        var outName = base + (outType === 'image/png' ? '.png' : '.jpg');
                        resolve(new File([blob], outName, { type: outType, lastModified: Date.now() }));
                    }, type === 'image/png' ? 'image/png' : 'image/jpeg', quality);
                };
                tryBlob();
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Could not read that image. Try another file.'));
            };
            img.src = url;
        });
    }

    if (input && preview && wrap) {
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) {
                clearImage();
                return;
            }
            compressImage(file).then(function (ready) {
                setInputFile(ready);
                var url = URL.createObjectURL(ready);
                preview.src = url;
                wrap.classList.remove('hidden');
            }).catch(function (err) {
                clearImage();
                toast((err && err.message) || 'Could not prepare the image.');
            });
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function (e) {
            e.preventDefault();
            clearImage();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var editedId = editingPostId();
            resetComposer();
            if (editedId !== '') showDetail(editedId);
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            syncBody();
            var hasTitle = titleInput && titleInput.value.trim() !== '';
            var hasText = body && !isEmptyHtml(body.value);
            var hasImage = (input && input.files && input.files.length > 0)
                || (editingPostId() !== '' && wrap && !wrap.classList.contains('hidden'));
            if (pendingUploads > 0) {
                e.preventDefault();
                toast('Please wait for the pictures to finish uploading.');
                return;
            }
            if (!hasTitle) {
                e.preventDefault();
                toast('Please enter a title.');
                if (titleInput) titleInput.focus();
                return;
            }
            if (!hasText && !hasImage) {
                e.preventDefault();
                toast('Write a description or attach a cover image to post.');
            }
        });
    }

    document.querySelectorAll('.forum-post-body').forEach(function (bodyEl) {
        bodyEl.addEventListener('click', function (e) {
            var img = e.target && e.target.closest ? e.target.closest('img') : null;
            if (img && img.src) window.open(img.src, '_blank', 'noopener');
        });
    });

    /* ---- Bulletin layout: list ↔ composer / detail ---- */
    var composerPanel = document.getElementById('bulletinComposerPanel') || document.getElementById('bulletinEmptyPanel');
    var detailPanels = document.querySelectorAll('.bulletin-detail-panel');
    var listItems = document.querySelectorAll('.bulletin-list-item');
    var newPostBtn = document.getElementById('bulletinNewPostBtn');
    var selectedClasses = [
        'border-sky-400',
        'bg-sky-50',
        'ring-1',
        'ring-sky-400/40',
        'dark:border-sky-500/60',
        'dark:bg-sky-500/10',
        'dark:ring-sky-500/30',
    ];
    var baseBorderClasses = ['border-slate-200/90', 'dark:border-slate-600/80'];

    function clearListSelection() {
        listItems.forEach(function (item) {
            selectedClasses.forEach(function (cls) { item.classList.remove(cls); });
            baseBorderClasses.forEach(function (cls) { item.classList.add(cls); });
            item.setAttribute('aria-current', 'false');
        });
    }

    function hideAllDetails() {
        detailPanels.forEach(function (panel) {
            panel.classList.add('hidden');
        });
    }

    function showComposer() {
        hideAllDetails();
        clearListSelection();
        if (composerPanel) composerPanel.classList.remove('hidden');
        if (history.replaceState) {
            var url = window.location.pathname + window.location.search;
            history.replaceState(null, '', url);
        }
    }

    function showDetail(postId) {
        if (!postId) return;
        var panel = document.getElementById('bulletinDetail-' + postId);
        if (!panel) return;

        if (composerPanel) composerPanel.classList.add('hidden');
        hideAllDetails();
        panel.classList.remove('hidden');

        clearListSelection();
        listItems.forEach(function (item) {
            if (String(item.getAttribute('data-post-id')) === String(postId)) {
                baseBorderClasses.forEach(function (cls) { item.classList.remove(cls); });
                selectedClasses.forEach(function (cls) { item.classList.add(cls); });
                item.setAttribute('aria-current', 'true');
            }
        });

        if (history.replaceState) {
            history.replaceState(null, '', '#post-' + postId);
        }
    }

    listItems.forEach(function (item) {
        item.addEventListener('click', function () {
            showDetail(this.getAttribute('data-post-id'));
        });
    });

    if (newPostBtn) {
        newPostBtn.addEventListener('click', function () {
            if (editingPostId() !== '') resetComposer();
            showComposer();
        });
    }

    function startEdit(btn) {
        if (!form || !btn) return;
        resetComposer();
        setEditing(btn);
        if (titleInput) titleInput.value = btn.getAttribute('data-title') || '';
        var typeRadio = document.querySelector('.forum-type-radio[value="' + (btn.getAttribute('data-post-type') === 'discussion' ? 'discussion' : 'announcement') + '"]');
        if (typeRadio) typeRadio.checked = true;
        var bodyTpl = document.getElementById('bulletinEditBody-' + btn.getAttribute('data-post-id'));
        if (editor) editor.innerHTML = bodyTpl ? bodyTpl.innerHTML.trim() : '';
        syncBody();
        showImagePreview(btn.getAttribute('data-image-url'));
        syncTypeLabels();
        showComposer();
        if (composerPanel && composerPanel.scrollIntoView) composerPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        if (titleInput) titleInput.focus();
    }

    document.querySelectorAll('.bulletin-edit-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            startEdit(this);
        });
    });

    // After a failed save, the form comes back in edit mode; restore the saved cover preview.
    var restoredEditId = editingPostId();
    if (restoredEditId !== '' && removeImageInput && removeImageInput.value !== '1') {
        var restoredBtn = document.querySelector('.bulletin-edit-btn[data-post-id="' + restoredEditId + '"]');
        if (restoredBtn) showImagePreview(restoredBtn.getAttribute('data-image-url'));
    }

    // Open detail from hash (#post-N) or ?post=N on load
    var initialId = null;
    var hashMatch = (window.location.hash || '').match(/^#post-(\d+)$/);
    if (hashMatch) {
        initialId = hashMatch[1];
    } else {
        try {
            var params = new URLSearchParams(window.location.search);
            var q = params.get('post');
            if (q && /^\d+$/.test(q)) initialId = q;
        } catch (err) {}
    }
    if (initialId && document.getElementById('bulletinDetail-' + initialId)) {
        showDetail(initialId);
    }
});
</script>
@endpush
