@php
    $cAuthor = $comment->user;
    $canRemoveComment = $isAdmin || ($canDeleteComment && (int) $comment->user_id === (int) $currentUserId);
    $canEditComment = $isAdmin || ($canComment && (int) $comment->user_id === (int) $currentUserId);
    $avatarSize = $isReply ? 'h-7 w-7 text-[0.6rem]' : 'h-8 w-8 text-[0.7rem]';
@endphp
<div class="flex gap-2.5">
    <div class="mt-0.5 flex {{ $avatarSize }} flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-200 font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">
        @if($avatarUrl($cAuthor))
            <img src="{{ $avatarUrl($cAuthor) }}" alt="" class="h-full w-full object-cover">
        @else
            {{ $initials($cAuthor) }}
        @endif
    </div>
    <div class="min-w-0 flex-1">
        <div class="forum-comment rounded-2xl bg-slate-100 px-3 py-2 dark:bg-slate-900/60">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs font-semibold text-slate-900 dark:text-slate-100">{{ $displayName($cAuthor) }}</p>
                <div class="flex items-center gap-2">
                    @if($canEditComment)
                        <button type="button" class="forum-comment-edit-btn cursor-pointer text-[0.65rem] font-medium text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Edit</button>
                    @endif
                    @if($canRemoveComment)
                        <form method="POST" action="{{ route('forum_thread.comment.destroy', $comment->id) }}" onsubmit="return confirm('{{ $isReply ? 'Delete this reply?' : 'Delete this comment and its replies?' }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cursor-pointer text-[0.65rem] font-medium text-slate-400 hover:text-red-500">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
            <p class="forum-comment-text mt-0.5 whitespace-pre-wrap break-words text-sm text-slate-700 dark:text-slate-300">{{ $comment->body }}</p>
            @if($canEditComment)
                <form method="POST" action="{{ route('forum_thread.comment.update', $comment->id) }}" class="forum-comment-edit-form mt-1.5 hidden space-y-2">
                    @csrf
                    @method('PUT')
                    <textarea name="body" required maxlength="2000" rows="2" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-100">{{ $comment->body }}</textarea>
                    <div class="flex gap-2">
                        <button type="submit" class="cursor-pointer rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-emerald-500">Save</button>
                        <button type="button" class="forum-comment-cancel-btn cursor-pointer rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-200 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
                    </div>
                </form>
            @endif
        </div>
        <div class="mt-1 flex items-center gap-3 px-1 text-[0.7rem] text-slate-400">
            <span title="{{ $comment->created_at?->timezone('Asia/Manila')->format('M j, Y g:i A') }}">
                {{ $comment->created_at?->timezone('Asia/Manila')->format('M j, Y · g:i A') }}
            </span>
            @if($canComment)
                <button
                    type="button"
                    class="forum-reply-btn cursor-pointer font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400"
                    data-reply-form="forumReplyForm-{{ $threadId }}"
                    data-reply-name="{{ $isReply ? $displayName($cAuthor) : '' }}"
                >Reply</button>
            @endif
        </div>
    </div>
</div>
