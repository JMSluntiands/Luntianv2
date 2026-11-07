@extends('layouts.dashboard')

@section('title', 'Task Management')

@section('body_class', 'page-task-management')

@section('content')
@php
    use App\Models\Task;

    $displayName = static function ($user): string {
        if (! $user) {
            return 'Unassigned';
        }
        $code = strtoupper(trim((string) ($user->unique_code ?? '')));
        if ($code !== '') {
            return $code;
        }
        $name = trim((string) ($user->fullname ?? ''));
        if ($name !== '') {
            return $name;
        }
        $username = trim((string) ($user->username ?? ''));
        if ($username !== '') {
            return $username;
        }
        $email = trim((string) ($user->email ?? ''));

        return $email !== '' ? $email : 'User';
    };
    $initials = static function ($user) use ($displayName): string {
        if (! $user) {
            return '?';
        }
        $code = strtoupper(trim((string) ($user->unique_code ?? '')));
        if ($code !== '') {
            return $code;
        }
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
    $staffUsers = collect($assigneeUsers ?? [])
        ->filter(static function ($user) {
            return strtoupper(trim((string) ($user->unique_code ?? ''))) !== ''
                && (int) ($user->id ?? 0) > 0;
        })
        ->groupBy(static function ($user) {
            return strtoupper(trim((string) ($user->unique_code ?? '')));
        })
        ->map(function ($group) use ($assigneeFilter) {
            if ($assigneeFilter) {
                $selected = $group->firstWhere('id', (int) $assigneeFilter);
                if ($selected) {
                    return $selected;
                }
            }

            return $group->sortBy('id')->first();
        })
        ->sortBy(static function ($user) {
            return strtoupper(trim((string) ($user->unique_code ?? '')));
        })
        ->values();
    $statusOptions = [
        Task::STATUS_NOT_STARTED => Task::statusMeta(Task::STATUS_NOT_STARTED),
        Task::STATUS_IN_PROGRESS => Task::statusMeta(Task::STATUS_IN_PROGRESS),
        Task::STATUS_ON_HOLD => Task::statusMeta(Task::STATUS_ON_HOLD),
        Task::STATUS_DONE => Task::statusMeta(Task::STATUS_DONE),
    ];
    $canManage = ! empty($canManage);
    $currentUserId = (int) ($currentUserId ?? session('user_id', 0));
    $statusFilter = $statusFilter ?? '';
    $assigneeFilter = $assigneeFilter ?? null;
    $visibilityFilter = $visibilityFilter ?? '';
    $viewMode = in_array(($viewMode ?? 'board'), ['table', 'board'], true) ? $viewMode : 'board';
    $queryBase = array_filter([
        'status' => $statusFilter !== '' ? $statusFilter : null,
        'assignee' => $assigneeFilter !== null ? $assigneeFilter : null,
        'visibility' => $visibilityFilter !== '' ? $visibilityFilter : null,
    ], static fn ($v) => $v !== null && $v !== '');
@endphp

    <div class="w-full max-w-full px-0" id="taskDatabaseRoot">
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="mb-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
                    {{ $viewMode === 'board' ? 'My Tasks' : 'Task Database' }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $viewMode === 'board' ? 'Board view grouped by assignee.' : 'Allocated jobs from all Job Management modules (client + reference).' }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white p-0.5 dark:border-slate-600 dark:bg-slate-800">
                    <a href="{{ route('task_management', array_merge($queryBase, ['view' => 'table'])) }}"
                       class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold transition-colors {{ $viewMode === 'table' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Table
                    </a>
                    <a href="{{ route('task_management', array_merge($queryBase, ['view' => 'board'])) }}"
                       class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold transition-colors {{ $viewMode === 'board' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4h6a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2V6a2 2 0 012-2zm-5 4h2v10H4a1 1 0 01-1-1V9a1 1 0 011-1zm14 0h2a1 1 0 011 1v8a1 1 0 01-1 1h-2V8z"/></svg>
                        Board
                    </a>
                </div>
                @if($canManage)
                    <button type="button" id="taskAddToggle" class="cursor-pointer inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New task
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">{{ $errors->first() }}</div>
        @endif

        @if($canManage || $canViewSelf)
            <form id="taskAddForm" method="POST" action="{{ route('task_management.store') }}" class="mb-4 hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800/70 sm:p-5">
                @csrf
                <input type="hidden" name="_method" id="taskFormMethod" value="POST">
                <input type="hidden" name="view_redirect" value="{{ $viewMode }}">
                @if($statusFilter !== '')
                    <input type="hidden" name="status_redirect" value="{{ $statusFilter }}">
                @endif
                @if($assigneeFilter !== null)
                    <input type="hidden" name="assignee_redirect" value="{{ $assigneeFilter }}">
                @endif
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h2 id="taskFormHeading" class="text-sm font-semibold text-slate-800 dark:text-slate-100">New task</h2>
                    <span id="taskFormModeBadge" class="hidden rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-900 dark:bg-amber-500/20 dark:text-amber-200">Editing</span>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label for="taskTitle" class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Task name</label>
                        <input type="text" id="taskTitle" name="title" required maxlength="255" placeholder="Task name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="taskAssignee" class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Assignee</label>
                        <select id="taskAssignee" name="assignee_user_id" class="task-filter-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-white">
                            <option value="">Unassigned</option>
                            @foreach($staffUsers as $user)
                                <option value="{{ $user->id }}">{{ $displayName($user) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="taskDue" class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Due</label>
                        <input type="date" id="taskDue" name="due_date" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="taskStatus" class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Status</label>
                        <select id="taskStatus" name="status" class="task-filter-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-white">
                            @foreach($statusOptions as $value => $meta)
                                <option value="{{ $value }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <span class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Visibility</span>
                        <div id="taskVisibilityToggle" class="inline-flex w-full overflow-hidden rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900/50" role="group" aria-label="Task visibility">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="visibility" value="public" class="peer sr-only" checked>
                                <span class="block px-2 py-2 text-center text-xs font-semibold text-slate-600 transition-colors peer-checked:bg-emerald-600 peer-checked:text-white dark:text-slate-300 dark:peer-checked:bg-emerald-600">Public</span>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="visibility" value="shared" class="peer sr-only">
                                <span class="block px-2 py-2 text-center text-xs font-semibold text-slate-600 transition-colors peer-checked:bg-violet-600 peer-checked:text-white dark:text-slate-300 dark:peer-checked:bg-violet-600">Shared</span>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="visibility" value="personal" class="peer sr-only">
                                <span class="block px-2 py-2 text-center text-xs font-semibold text-slate-600 transition-colors peer-checked:bg-slate-800 peer-checked:text-white dark:text-slate-300 dark:peer-checked:bg-slate-200 dark:peer-checked:text-slate-900">Personal</span>
                            </label>
                        </div>
                        <p id="taskVisibilityHint" class="mt-1 hidden text-[11px] leading-snug text-slate-500 dark:text-slate-400">Only you can see this task.</p>
                        <p id="taskSharedHint" class="mt-1 hidden text-[11px] leading-snug text-slate-500 dark:text-slate-400">Only you, the assignee and the members below can see and update this task.</p>
                    </div>
                </div>
                <div id="taskMembersPanel" class="mt-3 hidden">
                    <span class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Members</span>
                    <div class="flex max-h-40 flex-wrap gap-1.5 overflow-y-auto rounded-lg border border-slate-200 bg-slate-50 p-2 dark:border-slate-600 dark:bg-slate-900/40">
                        @foreach($staffUsers as $user)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="member_ids[]" value="{{ $user->id }}" class="peer sr-only" data-task-member>
                                <span class="inline-block rounded-md border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-600 transition-colors peer-checked:border-violet-600 peer-checked:bg-violet-600 peer-checked:text-white dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $displayName($user) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="mt-3">
                    <label for="taskNotes" class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Text</label>
                    <input type="text" id="taskNotes" name="notes" maxlength="5000" placeholder="Optional note" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
                <div class="mt-3 flex justify-end gap-2">
                    <button type="button" id="taskAddCancel" class="cursor-pointer rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
                    <button type="submit" id="taskFormSubmit" class="cursor-pointer rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Add task</button>
                </div>
            </form>
        @endif

        <form method="GET" action="{{ route('task_management') }}" class="mb-4 flex flex-wrap items-center gap-2">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            <label class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                Status
                <select name="status" onchange="this.form.submit()" class="task-filter-select cursor-pointer border-0 bg-transparent py-0.5 pl-1 pr-1 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-0 dark:text-white">
                    <option value="">All</option>
                    @foreach($statusOptions as $value => $meta)
                        <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </label>
            <label class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                Visibility
                <select name="visibility" onchange="this.form.submit()" class="task-filter-select cursor-pointer border-0 bg-transparent py-0.5 pl-1 pr-1 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-0 dark:text-white">
                    <option value="">All</option>
                    <option value="public" @selected($visibilityFilter === 'public')>Public</option>
                    <option value="shared" @selected($visibilityFilter === 'shared')>Shared</option>
                    <option value="personal" @selected($visibilityFilter === 'personal')>Personal</option>
                </select>
            </label>
            @if(!empty($canViewAll))
            <label class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                Assignee
                <select name="assignee" onchange="this.form.submit()" class="task-filter-select cursor-pointer border-0 bg-transparent py-0.5 pl-1 pr-1 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-0 dark:text-white">
                    <option value="">All</option>
                    <option value="0" @selected($assigneeFilter === 0)>Unassigned</option>
                    @foreach($staffUsers as $user)
                        <option value="{{ $user->id }}" @selected($assigneeFilter === (int) $user->id)>{{ $displayName($user) }}</option>
                    @endforeach
                </select>
            </label>
            @endif
            @if($statusFilter !== '' || $visibilityFilter !== '' || (!empty($canViewAll) && $assigneeFilter !== null))
                <a href="{{ route('task_management', ['view' => $viewMode]) }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">Clear filters</a>
            @endif
        </form>

        @if($viewMode === 'board')
            @include('task-management.partials.board', [
                'boardColumns' => $boardColumns ?? collect(),
                'statusOptions' => $statusOptions,
                'canManage' => $canManage,
                'currentUserId' => $currentUserId,
                'displayName' => $displayName,
                'initials' => $initials,
                'avatarUrl' => $avatarUrl,
            ])
        @else
            @include('task-management.partials.table', [
                'tasks' => $tasks,
                'statusOptions' => $statusOptions,
                'canManage' => $canManage,
                'canViewSelf' => $canViewSelf ?? false,
                'currentUserId' => $currentUserId,
                'displayName' => $displayName,
                'initials' => $initials,
                'avatarUrl' => $avatarUrl,
            ])
        @endif
    </div>

    <div id="taskCommentsModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-900/50 p-4" role="dialog" aria-modal="true" aria-labelledby="taskCommentsTitle">
        <div class="flex max-h-[85vh] w-full max-w-xl flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 px-5 py-3 dark:border-slate-700">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Comments</p>
                    <h2 id="taskCommentsTitle" class="truncate text-base font-semibold text-slate-800 dark:text-slate-100">Task</h2>
                </div>
                <button type="button" id="taskCommentsClose" class="cursor-pointer rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-label="Close">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="taskCommentsList" class="flex-1 space-y-3 overflow-y-auto px-5 py-4"></div>
            <form id="taskCommentsForm" class="border-t border-slate-200 px-5 py-3 dark:border-slate-700">
                <textarea id="taskCommentsInput" rows="2" maxlength="5000" required placeholder="Write a comment…" class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"></textarea>
                <div class="mt-2 flex justify-end">
                    <button type="submit" class="cursor-pointer rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Post comment</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Status / Assignee native selects: pure-white option text in dark theme */
    .page-task-management .task-filter-select {
        color-scheme: light;
    }
    [data-theme="dark"] .page-task-management .task-filter-select {
        color-scheme: dark;
        color: #ffffff;
    }
    [data-theme="dark"] .page-task-management .task-filter-select option {
        background-color: #0f172a;
        color: #ffffff;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var addToggle = document.getElementById('taskAddToggle');
    var addForm = document.getElementById('taskAddForm');
    var addCancel = document.getElementById('taskAddCancel');
    var assigneeSelect = document.getElementById('taskAssignee');
    var visibilityHint = document.getElementById('taskVisibilityHint');
    var sharedHint = document.getElementById('taskSharedHint');
    var membersPanel = document.getElementById('taskMembersPanel');
    var formMethod = document.getElementById('taskFormMethod');
    var formHeading = document.getElementById('taskFormHeading');
    var formModeBadge = document.getElementById('taskFormModeBadge');
    var formSubmit = document.getElementById('taskFormSubmit');
    var currentUserId = @json((string) $currentUserId);
    var storeUrl = @json(route('task_management.store'));
    var updateUrlBase = @json(url('/dashboard/task-management'));
    var lastPublicAssignee = assigneeSelect ? assigneeSelect.value : '';

    function isPersonalSelected() {
        var checked = document.querySelector('#taskAddForm input[name="visibility"]:checked');
        return checked && checked.value === 'personal';
    }

    function setVisibility(value) {
        var radios = document.querySelectorAll('#taskAddForm input[name="visibility"]');
        radios.forEach(function (radio) {
            radio.checked = radio.value === value;
        });
    }

    function setMembers(ids) {
        var wanted = (ids || []).map(String);
        document.querySelectorAll('#taskAddForm [data-task-member]').forEach(function (box) {
            box.checked = wanted.indexOf(box.value) !== -1;
        });
    }

    function applyVisibilityUi() {
        var personal = isPersonalSelected();
        var checked = document.querySelector('#taskAddForm input[name="visibility"]:checked');
        var shared = !!checked && checked.value === 'shared';
        if (visibilityHint) {
            visibilityHint.classList.toggle('hidden', !personal);
        }
        if (sharedHint) sharedHint.classList.toggle('hidden', !shared);
        if (membersPanel) membersPanel.classList.toggle('hidden', !shared);
        if (!assigneeSelect) return;
        if (personal) {
            if (!assigneeSelect.disabled) {
                lastPublicAssignee = assigneeSelect.value;
            }
            if (currentUserId && currentUserId !== '0') {
                assigneeSelect.value = currentUserId;
            }
            assigneeSelect.disabled = true;
        } else {
            assigneeSelect.disabled = false;
            if (lastPublicAssignee !== null && lastPublicAssignee !== undefined) {
                assigneeSelect.value = lastPublicAssignee;
            }
        }
    }

    document.querySelectorAll('#taskAddForm input[name="visibility"]').forEach(function (radio) {
        radio.addEventListener('change', applyVisibilityUi);
    });
    applyVisibilityUi();

    function resetCreateForm(assigneeId) {
        if (!addForm) return;
        addForm.action = storeUrl;
        if (formMethod) formMethod.value = 'POST';
        if (formHeading) formHeading.textContent = 'New task';
        if (formModeBadge) formModeBadge.classList.add('hidden');
        if (formSubmit) formSubmit.textContent = 'Add task';
        var title = document.getElementById('taskTitle');
        var due = document.getElementById('taskDue');
        var status = document.getElementById('taskStatus');
        var notes = document.getElementById('taskNotes');
        if (title) title.value = '';
        if (due) due.value = '';
        if (status) status.value = status.options.length ? status.options[0].value : 'not_started';
        if (notes) notes.value = '';
        setVisibility('public');
        setMembers([]);
        if (assigneeSelect) {
            assigneeSelect.disabled = false;
            lastPublicAssignee = (typeof assigneeId !== 'undefined' && assigneeId !== null && assigneeId !== '')
                ? String(assigneeId)
                : '';
            assigneeSelect.value = lastPublicAssignee;
        }
        applyVisibilityUi();
    }

    function openAddForm(assigneeId) {
        if (!addForm) return;
        resetCreateForm(assigneeId);
        addForm.classList.remove('hidden');
        addForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        var title = document.getElementById('taskTitle');
        if (title) title.focus();
    }

    function openEditForm(btn) {
        if (!addForm || !btn) return;
        var id = btn.getAttribute('data-task-id') || '';
        if (!id) return;

        addForm.action = updateUrlBase + '/' + id;
        if (formMethod) formMethod.value = 'PUT';
        if (formHeading) formHeading.textContent = 'Edit task';
        if (formModeBadge) formModeBadge.classList.remove('hidden');
        if (formSubmit) formSubmit.textContent = 'Save changes';

        var title = document.getElementById('taskTitle');
        var due = document.getElementById('taskDue');
        var status = document.getElementById('taskStatus');
        var notes = document.getElementById('taskNotes');
        if (title) title.value = btn.getAttribute('data-title') || '';
        if (due) due.value = btn.getAttribute('data-due') || '';
        if (status) status.value = btn.getAttribute('data-status') || 'not_started';
        if (notes) notes.value = btn.getAttribute('data-notes') || '';

        var visibility = btn.getAttribute('data-visibility') || 'public';
        setVisibility(visibility);
        var membersAttr = btn.getAttribute('data-members') || '';
        setMembers(membersAttr ? membersAttr.split(',') : []);

        var assignee = btn.getAttribute('data-assignee') || '';
        if (assigneeSelect) {
            assigneeSelect.disabled = false;
            if (visibility !== 'personal') {
                lastPublicAssignee = assignee;
            }
            assigneeSelect.value = assignee;
        }
        applyVisibilityUi();

        addForm.classList.remove('hidden');
        addForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        if (title) title.focus();
    }

    if (addToggle) {
        addToggle.addEventListener('click', function () {
            if (!addForm) return;
            if (addForm.classList.contains('hidden')) openAddForm('');
            else {
                addForm.classList.add('hidden');
                resetCreateForm('');
            }
        });
    }
    document.querySelectorAll('.task-add-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var aid = btn.getAttribute('data-assignee-id');
            openAddForm(aid === null ? '' : aid);
        });
    });
    document.querySelectorAll('.task-edit-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openEditForm(btn);
        });
    });
    if (addCancel && addForm) {
        addCancel.addEventListener('click', function () {
            addForm.classList.add('hidden');
            resetCreateForm('');
        });
    }
    if (addForm) {
        addForm.addEventListener('submit', function () {
            if (assigneeSelect) assigneeSelect.disabled = false;
        });
    }

    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var statusMeta = @json($statusOptions);

    function closeAllStatusMenus(except) {
        document.querySelectorAll('[data-status-menu]').forEach(function (menu) {
            if (except && menu === except) return;
            menu.classList.add('hidden');
            var btn = menu.parentElement && menu.parentElement.querySelector('[data-status-toggle]');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    }

    function applyStatusUi(btn, status) {
        var meta = statusMeta[status];
        if (!meta || !btn) return;
        btn.setAttribute('data-current-status', status);
        btn.className = 'task-status-btn inline-flex cursor-pointer items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium transition-shadow hover:ring-2 hover:ring-slate-200 dark:hover:ring-slate-600 ' + meta.pill;
        btn.innerHTML =
            '<span class="h-1.5 w-1.5 rounded-full ' + meta.dot + '" data-status-dot></span>' +
            '<span data-status-label>' + meta.label + '</span>' +
            '<svg class="h-3 w-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
    }

    document.querySelectorAll('[data-status-cell]').forEach(function (cell) {
        var btn = cell.querySelector('[data-status-toggle]');
        var menu = cell.querySelector('[data-status-menu]');
        var row = cell.closest('[data-task-row]');
        if (!btn || !menu || !row) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var willOpen = menu.classList.contains('hidden');
            closeAllStatusMenus(willOpen ? menu : null);
            menu.classList.toggle('hidden', !willOpen);
            btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        menu.querySelectorAll('[data-status-option]').forEach(function (opt) {
            opt.addEventListener('click', function (e) {
                e.stopPropagation();
                var next = opt.getAttribute('data-status') || '';
                var current = btn.getAttribute('data-current-status') || '';
                closeAllStatusMenus();
                if (!next || next === current) return;

                var prev = current;
                applyStatusUi(btn, next);

                var body = new URLSearchParams();
                body.set('_method', 'PUT');
                body.set('status', next);
                body.set('_token', csrf);

                fetch('/dashboard/task-management/' + row.getAttribute('data-task-id'), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    credentials: 'same-origin',
                    body: body
                }).then(function (res) {
                    return res.json().then(function (json) { return { res: res, body: json }; });
                }).then(function (payload) {
                    if (!payload.res.ok || !payload.body || payload.body.status !== 'success') {
                        applyStatusUi(btn, prev);
                        if (window.showSuccessToast) window.showSuccessToast((payload.body && payload.body.message) || 'Could not update status.');
                        return;
                    }
                    var saved = (payload.body.task && payload.body.task.status) || next;
                    applyStatusUi(btn, saved);
                }).catch(function () {
                    applyStatusUi(btn, prev);
                    if (window.showSuccessToast) window.showSuccessToast('Could not update status.');
                });
            });
        });
    });

    document.addEventListener('click', function () { closeAllStatusMenus(); });

    var commentsModal = document.getElementById('taskCommentsModal');
    var commentsList = document.getElementById('taskCommentsList');
    var commentsTitle = document.getElementById('taskCommentsTitle');
    var commentsForm = document.getElementById('taskCommentsForm');
    var commentsInput = document.getElementById('taskCommentsInput');
    var commentsTaskId = null;

    function commentsUrl(taskId) {
        return updateUrlBase + '/' + taskId + '/comments';
    }

    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = text;
        return node;
    }

    function renderComment(comment, isReply) {
        var wrap = el('div', isReply
            ? 'rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800/60'
            : 'rounded-lg border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800');
        wrap.setAttribute('data-comment-id', comment.id);
        var head = el('div', 'mb-1 flex items-center justify-between gap-2');
        head.appendChild(el('span', 'text-xs font-semibold text-slate-800 dark:text-slate-100', comment.author));
        head.appendChild(el('span', 'text-[11px] text-slate-400', comment.created_at));
        wrap.appendChild(head);
        wrap.appendChild(el('p', 'm-0 whitespace-pre-wrap text-sm text-slate-700 dark:text-slate-200', comment.body));

        if (!isReply) {
            var replies = el('div', 'mt-2 space-y-2 border-l-2 border-slate-200 pl-3 dark:border-slate-700');
            replies.setAttribute('data-replies', '');
            (comment.replies || []).forEach(function (reply) {
                replies.appendChild(renderComment(reply, true));
            });
            if (!(comment.replies || []).length) replies.classList.add('hidden');
            wrap.appendChild(replies);

            var replyBtn = el('button', 'mt-1.5 cursor-pointer text-xs font-semibold text-emerald-700 hover:underline dark:text-emerald-400', 'Reply');
            replyBtn.type = 'button';
            var replyForm = el('form', 'mt-2 hidden');
            var replyInput = el('textarea', 'w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100');
            replyInput.rows = 2;
            replyInput.maxLength = 5000;
            replyInput.required = true;
            replyInput.placeholder = 'Write a reply…';
            var replyActions = el('div', 'mt-1.5 flex justify-end gap-2');
            var cancelBtn = el('button', 'cursor-pointer rounded-md px-3 py-1 text-xs font-medium text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700', 'Cancel');
            cancelBtn.type = 'button';
            var sendBtn = el('button', 'cursor-pointer rounded-md bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-500', 'Reply');
            sendBtn.type = 'submit';
            replyActions.appendChild(cancelBtn);
            replyActions.appendChild(sendBtn);
            replyForm.appendChild(replyInput);
            replyForm.appendChild(replyActions);

            replyBtn.addEventListener('click', function () {
                replyForm.classList.toggle('hidden');
                if (!replyForm.classList.contains('hidden')) replyInput.focus();
            });
            cancelBtn.addEventListener('click', function () {
                replyForm.classList.add('hidden');
                replyInput.value = '';
            });
            replyForm.addEventListener('submit', function (e) {
                e.preventDefault();
                postComment(replyInput.value, comment.id, sendBtn).then(function (saved) {
                    if (!saved) return;
                    replies.classList.remove('hidden');
                    replies.appendChild(renderComment(saved, true));
                    replyInput.value = '';
                    replyForm.classList.add('hidden');
                });
            });

            wrap.appendChild(replyBtn);
            wrap.appendChild(replyForm);
        }

        return wrap;
    }

    function renderEmpty() {
        commentsList.innerHTML = '';
        commentsList.appendChild(el('p', 'py-6 text-center text-sm text-slate-500 dark:text-slate-400', 'No comments yet. Start the conversation below.'));
    }

    function updateCommentCount(taskId, delta) {
        document.querySelectorAll('.task-comments-open[data-task-id="' + taskId + '"] [data-comment-count]').forEach(function (label) {
            var current = parseInt(label.getAttribute('data-count') || label.textContent, 10) || 0;
            var next = current + delta;
            label.setAttribute('data-count', String(next));
            label.textContent = next > 0 ? next + (next === 1 ? ' comment' : ' comments') : 'Comment';
        });
    }

    function postComment(body, parentId, button) {
        body = String(body || '').trim();
        if (!body || !commentsTaskId) return Promise.resolve(null);
        var payload = new URLSearchParams();
        payload.set('body', body);
        if (parentId) payload.set('parent_id', parentId);
        payload.set('_token', csrf);
        if (button) button.disabled = true;
        var taskId = commentsTaskId;
        return fetch(commentsUrl(taskId), {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: payload
        }).then(function (res) {
            return res.json().then(function (json) { return { ok: res.ok, body: json }; });
        }).then(function (result) {
            if (!result.ok || !result.body || result.body.status !== 'success') {
                if (window.showSuccessToast) window.showSuccessToast((result.body && result.body.message) || 'Could not post comment.');
                return null;
            }
            updateCommentCount(taskId, 1);
            return result.body.comment;
        }).catch(function () {
            if (window.showSuccessToast) window.showSuccessToast('Could not post comment.');
            return null;
        }).finally(function () {
            if (button) button.disabled = false;
        });
    }

    function openComments(taskId, title) {
        if (!commentsModal || !taskId) return;
        commentsTaskId = String(taskId);
        commentsTitle.textContent = title || 'Task';
        commentsList.innerHTML = '';
        commentsList.appendChild(el('p', 'py-6 text-center text-sm text-slate-500 dark:text-slate-400', 'Loading…'));
        commentsModal.classList.remove('hidden');
        commentsModal.classList.add('flex');

        fetch(commentsUrl(taskId), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        }).then(function (res) {
            return res.json().then(function (json) { return { ok: res.ok, body: json }; });
        }).then(function (result) {
            if (!result.ok || !result.body || result.body.status !== 'success') {
                commentsList.innerHTML = '';
                commentsList.appendChild(el('p', 'py-6 text-center text-sm text-red-500', (result.body && result.body.message) || 'Could not load comments.'));
                return;
            }
            if (result.body.task && result.body.task.title) commentsTitle.textContent = result.body.task.title;
            var comments = result.body.comments || [];
            if (!comments.length) {
                renderEmpty();
                return;
            }
            commentsList.innerHTML = '';
            comments.forEach(function (comment) {
                commentsList.appendChild(renderComment(comment, false));
            });
        }).catch(function () {
            commentsList.innerHTML = '';
            commentsList.appendChild(el('p', 'py-6 text-center text-sm text-red-500', 'Could not load comments.'));
        });
        if (commentsInput) commentsInput.focus();
    }

    function closeComments() {
        if (!commentsModal) return;
        commentsModal.classList.add('hidden');
        commentsModal.classList.remove('flex');
        commentsTaskId = null;
    }

    document.querySelectorAll('.task-comments-open').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openComments(btn.getAttribute('data-task-id'), btn.getAttribute('data-task-title'));
        });
    });
    var commentsClose = document.getElementById('taskCommentsClose');
    if (commentsClose) commentsClose.addEventListener('click', closeComments);
    if (commentsModal) {
        commentsModal.addEventListener('click', function (e) {
            if (e.target === commentsModal) closeComments();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeComments();
    });
    if (commentsForm) {
        commentsForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var button = commentsForm.querySelector('button[type="submit"]');
            postComment(commentsInput.value, null, button).then(function (saved) {
                if (!saved) return;
                if (commentsList.querySelector('[data-comment-id]') === null) commentsList.innerHTML = '';
                commentsList.appendChild(renderComment(saved, false));
                commentsInput.value = '';
                commentsList.scrollTop = commentsList.scrollHeight;
            });
        });
    }
    var openFromQuery = new URLSearchParams(window.location.search).get('task_comments');
    if (openFromQuery) openComments(openFromQuery, '');

    document.querySelectorAll('tr[data-job-url]').forEach(function (row) {
        row.addEventListener('click', function (e) {
            if (e.target.closest('a, button, input, select, label, form')) return;
            var url = row.getAttribute('data-job-url');
            if (url) window.location.href = url;
        });
    });
});
</script>
@endpush
