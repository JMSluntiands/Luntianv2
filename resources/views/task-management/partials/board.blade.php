@php
    use App\Models\Task;
@endphp
<div class="overflow-x-auto pb-2">
    <div class="flex min-w-max items-start gap-4">
        @forelse($boardColumns as $column)
            @php
                $user = $column['user'] ?? null;
                $columnTasks = collect($column['tasks'] ?? []);
                $assigneeId = ($column['key'] ?? '0') === '0' ? '' : ($column['key'] ?? '');
                $jobTasks = $columnTasks->filter(static fn ($task) => ($task->row_type ?? '') === 'job')->values();
                $personalTasks = $columnTasks->filter(static function ($task) {
                    return ($task->row_type ?? '') !== 'job'
                        && strtolower(trim((string) ($task->visibility ?? ''))) === Task::VISIBILITY_PERSONAL;
                })->values();
                $publicManualTasks = $columnTasks->filter(static function ($task) {
                    return ($task->row_type ?? '') !== 'job'
                        && strtolower(trim((string) ($task->visibility ?? ''))) !== Task::VISIBILITY_PERSONAL;
                })->values();
                $sections = [
                    [
                        'key' => 'jobs',
                        'label' => 'Jobs',
                        'hint' => 'Allocated jobs',
                        'tasks' => $jobTasks,
                        'tone' => 'border-violet-200 bg-violet-50/70 text-violet-800 dark:border-violet-800/60 dark:bg-violet-500/10 dark:text-violet-200',
                    ],
                    [
                        'key' => 'tasks',
                        'label' => 'Tasks',
                        'hint' => 'Shared tasks',
                        'tasks' => $publicManualTasks,
                        'tone' => 'border-sky-200 bg-sky-50/70 text-sky-800 dark:border-sky-800/60 dark:bg-sky-500/10 dark:text-sky-200',
                    ],
                    [
                        'key' => 'personal',
                        'label' => 'Personal',
                        'hint' => 'Personal tasks only',
                        'tasks' => $personalTasks,
                        'tone' => 'border-amber-200 bg-amber-50/70 text-amber-900 dark:border-amber-800/60 dark:bg-amber-500/10 dark:text-amber-100',
                    ],
                ];
            @endphp
            <section class="flex w-80 shrink-0 flex-col rounded-xl border border-slate-200 bg-slate-50/80 dark:border-slate-700 dark:bg-slate-900/40">
                <header class="flex items-center gap-2.5 border-b border-slate-200 px-3 py-3 dark:border-slate-700">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-200 text-xs font-semibold text-slate-700 dark:bg-slate-600 dark:text-slate-100">
                        @if($user && $avatarUrl($user))
                            <img src="{{ $avatarUrl($user) }}" alt="" class="h-full w-full object-cover">
                        @else
                            {{ $user ? $initials($user) : '?' }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $displayName($user) }}</p>
                        <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">
                            {{ $jobTasks->count() }} job{{ $jobTasks->count() === 1 ? '' : 's' }}
                            · {{ $personalTasks->count() }} personal
                        </p>
                    </div>
                    <span class="rounded-full bg-white px-2 py-0.5 text-xs font-semibold tabular-nums text-slate-500 shadow-sm dark:bg-slate-800 dark:text-slate-400">{{ $columnTasks->count() }}</span>
                </header>

                <div class="flex max-h-[70vh] flex-col gap-3 overflow-y-auto p-2.5">
                    @foreach($sections as $section)
                        @if($section['tasks']->isEmpty())
                            @continue
                        @endif
                        <div class="space-y-2">
                            <div class="sticky top-0 z-[1] flex items-center justify-between gap-2 rounded-lg border px-2.5 py-1.5 {{ $section['tone'] }}">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wide">{{ $section['label'] }}</p>
                                    <p class="truncate text-[10px] opacity-80">{{ $section['hint'] }}</p>
                                </div>
                                <span class="rounded-full bg-white/80 px-1.5 py-0.5 text-[10px] font-semibold tabular-nums text-slate-600 dark:bg-slate-900/50 dark:text-slate-200">{{ $section['tasks']->count() }}</span>
                            </div>

                            @foreach($section['tasks'] as $task)
                                @php
                                    $isJob = ($task->row_type ?? '') === 'job';
                                    if ($isJob) {
                                        $meta = [
                                            'label' => 'Allocated',
                                            'pill' => 'bg-violet-100 text-violet-800 dark:bg-violet-500/20 dark:text-violet-300',
                                            'dot' => 'bg-violet-500',
                                        ];
                                    } else {
                                        $meta = Task::statusMeta((string) $task->status);
                                    }
                                @endphp
                                <article class="group/card rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-shadow hover:shadow-md dark:border-slate-600 dark:bg-slate-800" @if(!$isJob) data-task-row data-task-id="{{ $task->id }}" @endif>
                                    <div class="mb-2 flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $task->client ?? '—' }}</p>
                                            <h3 class="text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">{{ $task->reference ?? $task->title ?? '—' }}</h3>
                                            @if(!$isJob && ($task->visibility ?? '') === Task::VISIBILITY_PERSONAL)
                                                <span class="mt-1 inline-flex items-center rounded-md bg-slate-800 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white dark:bg-slate-200 dark:text-slate-900">Personal</span>
                                            @endif
                                        </div>
                                        @php
                                            $isPersonalTask = !$isJob && strtolower(trim((string) ($task->visibility ?? ''))) === Task::VISIBILITY_PERSONAL;
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
                                        @if($canEditTask || ($canManage && !$isJob && !empty($task->id)))
                                            <div class="flex shrink-0 items-center gap-0.5 opacity-0 transition-opacity group-hover/card:opacity-100">
                                                @if($canEditTask)
                                                    <button
                                                        type="button"
                                                        class="task-edit-open cursor-pointer rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-700 dark:hover:text-slate-200"
                                                        title="Edit task"
                                                        data-task-id="{{ $task->id }}"
                                                        data-title="{{ $task->title ?? $task->reference ?? '' }}"
                                                        data-assignee="{{ $task->assignee_user_id ?? '' }}"
                                                        data-due="{{ $dueValue }}"
                                                        data-status="{{ $task->status ?? 'not_started' }}"
                                                        data-notes="{{ $task->notes ?? '' }}"
                                                        data-visibility="{{ $task->visibility ?? 'public' }}"
                                                    >
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </button>
                                                @endif
                                                @if($canManage && !$isJob && !empty($task->id))
                                                    <form method="POST" action="{{ route('task_management.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="view_redirect" value="board">
                                                        <button type="submit" class="cursor-pointer rounded p-1 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 dark:hover:text-red-400" title="Delete">
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <p class="mb-2 text-[11px] font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $task->module ?? '' }}</p>

                                    @if(!$isJob && !empty($task->due_date))
                                        <p class="mb-2 text-xs tabular-nums text-slate-500 dark:text-slate-400">
                                            {{ $task->due_date instanceof \Carbon\Carbon ? $task->due_date->format('F j, Y') : \Illuminate\Support\Carbon::parse($task->due_date)->format('F j, Y') }}
                                        </p>
                                    @endif

                                    <div class="mb-2">
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
                                    </div>

                                    @if(!$isJob && trim((string) ($task->notes ?? '')) !== '')
                                        <p class="mb-2 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $task->notes }}</p>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @endforeach

                    @if($columnTasks->isEmpty())
                        <p class="rounded-xl border border-dashed border-slate-300 px-3 py-6 text-center text-xs text-slate-500 dark:border-slate-600 dark:text-slate-400">No jobs or personal tasks</p>
                    @endif

                    @if($canManage)
                        <button
                            type="button"
                            class="task-add-open flex w-full cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-dashed border-slate-300 bg-white/60 px-3 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 dark:border-slate-600 dark:bg-slate-800/40 dark:text-slate-400 dark:hover:border-blue-500 dark:hover:bg-blue-500/10 dark:hover:text-blue-300"
                            data-assignee-id="{{ $assigneeId }}"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            New task
                        </button>
                    @endif
                </div>
            </section>
        @empty
            <div class="w-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-600 dark:bg-slate-800/50">
                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">No allocated jobs to show on the board</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Allocated jobs from Job Management will appear here.</p>
                @if($canManage)
                    <button type="button" class="task-add-open mt-4 cursor-pointer inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New task
                    </button>
                @endif
            </div>
        @endforelse
    </div>
</div>
