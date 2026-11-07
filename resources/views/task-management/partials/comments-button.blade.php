@php $commentCount = (int) ($task->comment_count ?? 0); @endphp
<button type="button"
        class="task-comments-open inline-flex cursor-pointer items-center gap-1 rounded-md px-1.5 py-1 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-100"
        data-task-id="{{ $task->id }}"
        data-task-title="{{ $task->title ?? $task->reference ?? '' }}"
        title="Comments">
    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
    <span data-comment-count>{{ $commentCount > 0 ? $commentCount.' '.\Illuminate\Support\Str::plural('comment', $commentCount) : 'Comment' }}</span>
</button>
