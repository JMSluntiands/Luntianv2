@php
    use App\Models\Task;
@endphp
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-3 py-2.5 dark:border-slate-700 sm:px-4">
        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-700/70 dark:text-slate-200">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Allocated jobs + tasks
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/80 text-left text-xs font-medium text-slate-500 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-400">
                    <th class="w-10 px-3 py-2.5 sm:px-4"><span class="sr-only">Select</span></th>
                    <th class="min-w-[12rem] px-2 py-2.5 font-medium">Client</th>
                    <th class="min-w-[12rem] px-2 py-2.5 font-medium">Reference</th>
                    <th class="min-w-[9rem] px-2 py-2.5 font-medium">Module</th>
                    <th class="min-w-[11rem] px-2 py-2.5 font-medium">Assignee</th>
                    <th class="min-w-[8.5rem] px-2 py-2.5 font-medium">Due</th>
                    <th class="min-w-[10rem] px-2 py-2.5 font-medium">Status</th>
                    @if($canManage || !empty($canViewSelf))
                        <th class="w-16 px-2 py-2.5 text-right font-medium sm:px-4"><span class="sr-only">Actions</span></th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                @forelse($tasks as $task)
                    @php
                        $isJob = ($task->row_type ?? '') === 'job';
                        $assignee = $task->assignee ?? null;
                        if ($isJob) {
                            $meta = [
                                'label' => 'Allocated',
                                'pill' => 'bg-violet-100 text-violet-800 dark:bg-violet-500/20 dark:text-violet-300',
                                'dot' => 'bg-violet-500',
                            ];
                        } else {
                            $meta = Task::statusMeta((string) $task->status);
                        }
                        $isOverdue = false;
                        if (!$isJob && !empty($task->due_date) && ($task->status ?? '') !== \App\Models\Task::STATUS_DONE) {
                            $dueDate = $task->due_date instanceof \Carbon\Carbon
                                ? $task->due_date->copy()->startOfDay()
                                : \Illuminate\Support\Carbon::parse($task->due_date)->startOfDay();
                            $isOverdue = $dueDate->lt(\Illuminate\Support\Carbon::today());
                        }
                        $rowClass = $isOverdue
                            ? 'group/row bg-red-50 transition-colors hover:bg-red-100/80 dark:bg-red-500/10 dark:hover:bg-red-500/15'
                            : 'group/row transition-colors hover:bg-slate-50/80 dark:hover:bg-slate-700/30';
                        $jobViewUrl = $isJob ? trim((string) ($task->view_url ?? '')) : '';
                        if ($jobViewUrl !== '') {
                            $rowClass .= ' cursor-pointer';
                        }
                    @endphp
                    <tr class="{{ $rowClass }}" @if(!$isJob) data-task-row data-task-id="{{ $task->id }}" @endif @if($jobViewUrl !== '') data-job-url="{{ $jobViewUrl }}" title="Open job details" @endif>
                        <td class="px-3 py-2.5 align-middle sm:px-4">
                            <input type="checkbox" class="h-4 w-4 cursor-pointer rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/30 dark:border-slate-600 dark:bg-slate-800" aria-label="Select row" onclick="event.stopPropagation()">
                        </td>
                        <td class="px-2 py-2.5 align-middle">
                            <span class="font-medium text-slate-800 dark:text-slate-100">{{ $task->client ?? '—' }}</span>
                        </td>
                        <td class="px-2 py-2.5 align-middle">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @if($jobViewUrl !== '')
                                    <a href="{{ $jobViewUrl }}" class="font-medium text-slate-800 no-underline hover:text-emerald-700 dark:text-slate-100 dark:hover:text-emerald-300">{{ $task->reference ?? $task->title ?? '—' }}</a>
                                @else
                                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ $task->reference ?? $task->title ?? '—' }}</span>
                                @endif
                                @if(!$isJob && ($task->visibility ?? '') === \App\Models\Task::VISIBILITY_PERSONAL)
                                    <span class="inline-flex items-center rounded-md bg-slate-800 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white dark:bg-slate-200 dark:text-slate-900">Personal</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-2 py-2.5 align-middle">
                            <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-700/70 dark:text-slate-300">{{ $task->module ?? '—' }}</span>
                        </td>
                        <td class="px-2 py-2.5 align-middle">
                            @if($assignee)
                                <div class="flex items-center gap-2">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-200 text-[11px] font-semibold text-slate-700 dark:bg-slate-600 dark:text-slate-100">
                                        @if($avatarUrl($assignee))
                                            <img src="{{ $avatarUrl($assignee) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            {{ $initials($assignee) }}
                                        @endif
                                    </div>
                                    <span class="truncate text-slate-700 dark:text-slate-200">{{ $displayName($assignee) }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 dark:text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-2 py-2.5 align-middle tabular-nums {{ $isOverdue ? 'font-semibold text-red-600 dark:text-red-400' : 'text-slate-700 dark:text-slate-200' }}">
                            @if(!$isJob && !empty($task->due_date))
                                {{ $task->due_date instanceof \Carbon\Carbon ? $task->due_date->format('F j, Y') : \Illuminate\Support\Carbon::parse($task->due_date)->format('F j, Y') }}
                                @if($isOverdue)
                                    <span class="ml-1 text-[10px] font-bold uppercase tracking-wide">Overdue</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-2 py-2.5 align-middle">
                            @if($isJob)
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $meta['pill'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $meta['dot'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                            @else
                                @php
                                    $taskModel = (object) [
                                        'id' => $task->id,
                                        'status' => $task->status,
                                    ];
                                @endphp
                                @include('task-management.partials.status-dropdown', ['task' => $taskModel, 'meta' => $meta, 'statusOptions' => $statusOptions, 'canManage' => $canManage])
                            @endif
                        </td>
                        @if($canManage || !empty($canViewSelf))
                            <td class="px-2 py-2.5 text-right align-middle sm:px-4">
                                @php
                                    $isPersonalTask = !$isJob && strtolower(trim((string) ($task->visibility ?? ''))) === \App\Models\Task::VISIBILITY_PERSONAL;
                                    $canEditTask = !$isJob && !empty($task->id) && (
                                        $isPersonalTask
                                            ? ((int) ($task->created_by ?? 0) === (int) ($currentUserId ?? 0))
                                            : (bool) $canManage
                                    );
                                    $dueValue = '';
                                    if (!$isJob && !empty($task->due_date)) {
                                        $dueValue = $task->due_date instanceof \Carbon\Carbon
                                            ? $task->due_date->format('Y-m-d')
                                            : \Illuminate\Support\Carbon::parse($task->due_date)->format('Y-m-d');
                                    }
                                @endphp
                                @if(!$isJob && !empty($task->id) && ($canEditTask || $canManage))
                                    <div class="inline-flex items-center gap-1 opacity-0 transition-opacity group-hover/row:opacity-100">
                                        @if($canEditTask)
                                            <button
                                                type="button"
                                                class="task-edit-open cursor-pointer rounded-md px-2 py-1 text-xs font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-100"
                                                data-task-id="{{ $task->id }}"
                                                data-title="{{ $task->title ?? $task->reference ?? '' }}"
                                                data-assignee="{{ $task->assignee_user_id ?? '' }}"
                                                data-due="{{ $dueValue }}"
                                                data-status="{{ $task->status ?? 'not_started' }}"
                                                data-notes="{{ $task->notes ?? '' }}"
                                                data-visibility="{{ $task->visibility ?? 'public' }}"
                                            >Edit</button>
                                        @endif
                                        @if($canManage)
                                            <form method="POST" action="{{ route('task_management.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="view_redirect" value="table">
                                                <button type="submit" class="cursor-pointer rounded-md px-2 py-1 text-xs font-medium text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 dark:hover:text-red-400">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ($canManage || !empty($canViewSelf)) ? 8 : 7 }}" class="px-4 py-12 text-center">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">No allocated jobs yet</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Allocated jobs from Job Management will appear here with client and reference.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($canManage)
        <div class="border-t border-slate-200 dark:border-slate-700">
            <button type="button" class="task-add-open flex w-full cursor-pointer items-center gap-2 px-4 py-3 text-left text-sm font-medium text-slate-500 transition-colors hover:bg-slate-50 hover:text-emerald-700 dark:text-slate-400 dark:hover:bg-slate-700/40 dark:hover:text-emerald-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New
            </button>
        </div>
    @endif

    @if($tasks && method_exists($tasks, 'hasPages') && $tasks->hasPages())
        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-700">
            {{ $tasks->links() }}
        </div>
    @endif
</div>
