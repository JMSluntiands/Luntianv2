<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use App\Models\Task;
use App\Models\User;
use App\Services\AllocatedJobsTaskFeed;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $view = strtolower(trim((string) $request->query('view', 'table')));
        if (! in_array($view, ['table', 'board'], true)) {
            $view = 'table';
        }

        $statusFilter = trim((string) $request->query('status', ''));
        if ($statusFilter !== '' && ! in_array($statusFilter, Task::STATUSES, true)) {
            $statusFilter = '';
        }

        $assigneeFilter = $request->query('assignee');
        $assigneeFilter = $assigneeFilter === null || $assigneeFilter === '' ? null : (int) $assigneeFilter;

        $users = User::query()
            ->orderByRaw("CASE WHEN COALESCE(NULLIF(TRIM(unique_code), ''), '') = '' THEN 1 ELSE 0 END")
            ->orderBy('unique_code')
            ->orderBy('fullname')
            ->orderBy('username')
            ->get(['id', 'fullname', 'username', 'email', 'profile_image', 'unique_code', 'role', 'branch']);

        $canManage = $this->mayManageTasks();
        $canViewAll = $this->mayViewAllTasks();
        $canViewSelf = $this->mayViewSelfTasks();
        $currentUserId = (int) session('user_id', 0);

        // View Self only (no View All): lock list to the signed-in user.
        if (! $canViewAll && $canViewSelf && $currentUserId > 0) {
            $assigneeFilter = $currentUserId;
        }

        $jobItemsAll = collect();
        try {
            $jobItemsAll = AllocatedJobsTaskFeed::all();
        } catch (\Throwable) {
            $jobItemsAll = collect();
        }

        // Assignee picker must include everyone who appears on allocated jobs (e.g. PEP),
        // plus other staff accounts. Branch/Admin stay out unless they are on a job row.
        $assigneeEligibleUsers = $this->assigneeEligibleUsers($users, $jobItemsAll);
        $assigneeUsers = $this->dedupeAssigneesByCode($assigneeEligibleUsers, $assigneeFilter);
        $assigneeFilterIds = $this->assigneeFilterUserIds($assigneeEligibleUsers, $assigneeFilter);
        $assigneeFilterCode = '';
        if ($assigneeFilter !== null && $assigneeFilter > 0) {
            $selectedAssignee = $assigneeEligibleUsers->firstWhere('id', $assigneeFilter);
            $assigneeFilterCode = strtoupper(trim((string) ($selectedAssignee->unique_code ?? '')));
        }

        $jobItems = $jobItemsAll;
        if ($assigneeFilter !== null) {
            $jobItems = $jobItems->filter(function (object $row) use ($assigneeFilter, $assigneeFilterIds, $assigneeFilterCode) {
                $uid = (int) ($row->assignee_user_id ?? 0);
                $code = strtoupper(trim((string) ($row->assignee_code ?? '')));
                if ($assigneeFilter === 0) {
                    return $uid === 0 && $code === '';
                }
                if ($uid > 0 && in_array($uid, $assigneeFilterIds, true)) {
                    return true;
                }

                return $assigneeFilterCode !== '' && $code === $assigneeFilterCode;
            })->values();
        }

        // Manual tasks: only when status filter is set (job rows are always "Allocated")
        // or when browsing all.
        $manualTasks = collect();
        if (Schema::hasTable('tasks')) {
            try {
                $manualQuery = Task::query()
                    ->visibleTo($currentUserId)
                    ->with(['assignee:id,fullname,username,email,profile_image,unique_code'])
                    ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('due_date')
                    ->orderByDesc('updated_at');

                if ($statusFilter !== '') {
                    $manualQuery->where('status', $statusFilter);
                    $jobItems = collect(); // allocated jobs are not task statuses
                }

                if ($assigneeFilter !== null) {
                    if ($assigneeFilter === 0) {
                        $manualQuery->whereNull('assignee_user_id');
                    } else {
                        $manualQuery->whereIn('assignee_user_id', $assigneeFilterIds !== [] ? $assigneeFilterIds : [$assigneeFilter]);
                    }
                }

                $manualTasks = $manualQuery->get();
            } catch (\Throwable) {
                $manualTasks = collect();
            }
        } elseif ($statusFilter !== '') {
            $jobItems = collect();
        }

        $rows = $this->mergeTaskRows($jobItems, $manualTasks, $users);

        $boardColumns = collect();
        $tasks = null;

        if ($view === 'board') {
            $boardColumns = $this->buildBoardColumnsFromRows($rows, $users);
        } else {
            $page = max(1, (int) $request->query('page', 1));
            $perPage = 50;
            $slice = $rows->slice(($page - 1) * $perPage, $perPage)->values();
            $tasks = new LengthAwarePaginator(
                $slice,
                $rows->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return view('task-management.index', [
            'sidebar_active' => 'task_management',
            'tasks' => $tasks,
            'boardColumns' => $boardColumns,
            'users' => $users,
            'assigneeUsers' => $assigneeUsers,
            'statusFilter' => $statusFilter,
            'assigneeFilter' => $assigneeFilter,
            'viewMode' => $view,
            'canManage' => $canManage,
            'canViewAll' => $canViewAll,
            'canViewSelf' => $canViewSelf,
            'currentUserId' => $currentUserId,
        ]);
    }

    public function store(Request $request)
    {
        if (! $this->mayManageTasks()) {
            return $this->deny($request, 'You do not have permission to create tasks.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignee_user_id' => ['nullable', 'integer', Rule::in($this->assignableStaffUserIds())],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(Task::STATUSES)],
            'notes' => ['nullable', 'string', 'max:5000'],
            'visibility' => ['nullable', Rule::in(Task::VISIBILITIES)],
        ]);

        $creatorId = (int) session('user_id', 0) ?: null;
        $visibility = $data['visibility'] ?? Task::VISIBILITY_PUBLIC;
        $assigneeId = $data['assignee_user_id'] ?? null;
        if ($visibility === Task::VISIBILITY_PERSONAL && $creatorId) {
            $assigneeId = $creatorId;
        }

        Task::create(array_filter([
            'title' => trim($data['title']),
            'assignee_user_id' => $assigneeId,
            'due_date' => $data['due_date'] ?? null,
            'status' => $data['status'] ?? Task::STATUS_NOT_STARTED,
            'notes' => isset($data['notes']) ? trim((string) $data['notes']) : null,
            'visibility' => Task::supportsVisibility() ? $visibility : null,
            'created_by' => $creatorId,
        ], static fn ($value) => $value !== null));

        return redirect()
            ->route('task_management', $this->redirectQuery($request))
            ->with('success', 'Task created.');
    }

    public function update(Request $request, int $id)
    {
        $task = Task::findOrFail($id);

        $mayUpdate = $this->mayManageTasks()
            || RolePermission::userMayAccessRoute('task_management.update')
            || $this->mayViewSelfTasks();

        if (! $mayUpdate) {
            return $this->deny($request, 'You do not have permission to update tasks.');
        }

        if (! $this->mayMutateTask($task)) {
            return $this->deny($request, 'You can only update your own tasks.');
        }
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'assignee_user_id' => ['sometimes', 'nullable', 'integer', Rule::in($this->assignableStaffUserIds())],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'required', Rule::in(Task::STATUSES)],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'visibility' => ['sometimes', Rule::in(Task::VISIBILITIES)],
        ]);

        if (array_key_exists('title', $data)) {
            $task->title = trim($data['title']);
        }
        if (array_key_exists('assignee_user_id', $data)) {
            if (! $this->mayViewAllTasks()) {
                // View Self: cannot reassign away from self
                $data['assignee_user_id'] = (int) session('user_id', 0) ?: $task->assignee_user_id;
            }
            $task->assignee_user_id = $data['assignee_user_id'];
        }
        if (array_key_exists('due_date', $data)) {
            $task->due_date = $data['due_date'];
        }
        if (array_key_exists('status', $data)) {
            $task->status = $data['status'];
        }
        if (array_key_exists('notes', $data)) {
            $task->notes = $data['notes'] !== null ? trim((string) $data['notes']) : null;
        }
        if (array_key_exists('visibility', $data) && Task::supportsVisibility()) {
            $task->visibility = $data['visibility'];
            if ($task->visibility === Task::VISIBILITY_PERSONAL) {
                $uid = (int) session('user_id', 0);
                if ($uid > 0) {
                    $task->assignee_user_id = $uid;
                    $task->created_by = $task->created_by ?: $uid;
                }
            }
        }
        $task->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task updated.',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'status_label' => Task::statusLabel($task->status),
                    'status_meta' => Task::statusMeta($task->status),
                ],
            ]);
        }

        return redirect()
            ->route('task_management', $this->redirectQuery($request))
            ->with('success', 'Task updated.');
    }

    public function destroy(Request $request, int $id)
    {
        if (! $this->mayManageTasks() && ! RolePermission::userMayAccessRoute('task_management.destroy')) {
            return $this->deny($request, 'You do not have permission to delete tasks.');
        }

        $task = Task::findOrFail($id);
        if (! $this->mayMutateTask($task)) {
            return $this->deny($request, 'You can only delete your own tasks.');
        }
        $task->delete();

        return redirect()
            ->route('task_management', $this->redirectQuery($request))
            ->with('success', 'Task deleted.');
    }

    /**
     * User ids allowed when creating/editing manual tasks (staff accounts only).
     *
     * @return list<int>
     */
    private function assignableStaffUserIds(): array
    {
        return User::query()
            ->whereRaw('LOWER(TRIM(COALESCE(role, ""))) NOT IN (?, ?)', ['admin', 'branch'])
            ->whereNotNull('unique_code')
            ->whereRaw('TRIM(unique_code) != ?', [''])
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /**
     * Filter dropdown users: all staff + anyone currently assigned on allocated jobs.
     * Job assignees are included even if their account role/branch would normally hide them,
     * so codes like PEP always appear when they have allocated work.
     *
     * @param  Collection<int, User>  $users
     * @param  Collection<int, object>  $jobItems
     * @return Collection<int, User>
     */
    private function assigneeEligibleUsers(Collection $users, Collection $jobItems): Collection
    {
        $userMap = $users->keyBy('id');

        $fromJobs = collect();
        foreach ($jobItems as $job) {
            $uid = (int) ($job->assignee_user_id ?? 0);
            if ($uid > 0 && $userMap->has($uid)) {
                $fromJobs->put($uid, $userMap->get($uid));
                continue;
            }

            $code = strtoupper(trim((string) ($job->assignee_code ?? '')));
            if ($code === '') {
                continue;
            }

            $match = $users->first(
                static fn (User $user) => strtoupper(trim((string) $user->unique_code)) === $code
            );
            if ($match) {
                $fromJobs->put((int) $match->id, $match);
            } else {
                // Synthetic row so the filter still lists the job assignee code.
                $fromJobs->put('code:'.$code, (object) [
                    'id' => 0,
                    'unique_code' => $code,
                    'fullname' => $code,
                    'username' => $code,
                    'email' => '',
                    'profile_image' => null,
                    'role' => 'User',
                    'branch' => '',
                    'assignee_filter_code' => $code,
                ]);
            }
        }

        $blockedBranchCodes = collect();
        if (Schema::hasTable('branches')) {
            $blockedBranchCodes = $blockedBranchCodes->merge(DB::table('branches')->pluck('branch_name'));
        }
        if (Schema::hasTable('clients')) {
            $blockedBranchCodes = $blockedBranchCodes->merge(DB::table('clients')->pluck('client_code'));
        }
        $blockedBranchCodes = $blockedBranchCodes
            ->map(static fn ($code) => strtoupper(trim((string) $code)))
            ->filter()
            ->unique();

        $fromStaff = $users->filter(function (User $user) use ($blockedBranchCodes) {
            $role = strtolower(trim((string) ($user->role ?? '')));
            if (in_array($role, ['admin', 'branch'], true)) {
                return false;
            }
            $code = strtoupper(trim((string) ($user->unique_code ?? '')));
            if ($code === '') {
                return false;
            }
            // Hide pure branch/client login codes unless they already appear via jobs above.
            if ($blockedBranchCodes->contains($code)) {
                return false;
            }
            $task = strtolower(trim((string) ($user->task ?? '')));

            return $task !== 'archived';
        });

        return $fromStaff
            ->concat($fromJobs->values())
            ->unique(static function ($user) {
                if (is_object($user) && isset($user->assignee_filter_code)) {
                    return 'code:'.strtoupper((string) $user->assignee_filter_code);
                }

                return 'id:'.(int) ($user->id ?? 0);
            })
            ->sortBy(static fn ($user) => strtoupper(trim((string) ($user->unique_code ?? ''))))
            ->values();
    }

    /**
     * @param  Collection<int, User|object>  $assigneeUsers
     * @return Collection<int, User|object>
     */
    private function dedupeAssigneesByCode(Collection $assigneeUsers, ?int $assigneeFilter): Collection
    {
        return $assigneeUsers
            ->groupBy(static fn ($user) => strtoupper(trim((string) ($user->unique_code ?? ''))))
            ->map(function ($group) use ($assigneeFilter) {
                if ($assigneeFilter) {
                    $selected = $group->firstWhere('id', (int) $assigneeFilter);
                    if ($selected) {
                        return $selected;
                    }
                }

                // Prefer real user rows (id > 0) over synthetic code placeholders.
                return $group->sortBy([
                    static fn ($user) => (int) ($user->id ?? 0) > 0 ? 0 : 1,
                    static fn ($user) => (int) ($user->id ?? 0),
                ])->first();
            })
            ->sortBy(static fn ($user) => strtoupper(trim((string) ($user->unique_code ?? ''))))
            ->values();
    }

    /**
     * Expand an assignee filter user id to all assignment-eligible users sharing the same unique_code.
     *
     * @param  Collection<int, User|object>  $assigneeUsers
     * @return list<int>
     */
    private function assigneeFilterUserIds(Collection $assigneeUsers, ?int $assigneeFilter): array
    {
        if ($assigneeFilter === null || $assigneeFilter <= 0) {
            return [];
        }

        $selected = $assigneeUsers->firstWhere('id', $assigneeFilter);
        $code = strtoupper(trim((string) ($selected->unique_code ?? '')));
        if ($code === '') {
            return [$assigneeFilter];
        }

        $ids = $assigneeUsers
            ->filter(static function ($user) use ($code) {
                return strtoupper(trim((string) ($user->unique_code ?? ''))) === $code
                    && (int) ($user->id ?? 0) > 0;
            })
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();

        return $ids !== [] ? $ids : [$assigneeFilter];
    }

    /**
     * @param  Collection<int, object>  $jobItems
     * @param  Collection<int, Task>  $manualTasks
     * @param  Collection<int, User>  $users
     * @return Collection<int, object>
     */
    private function mergeTaskRows(Collection $jobItems, Collection $manualTasks, Collection $users): Collection
    {
        $userMap = $users->keyBy('id');

        $fromJobs = $jobItems->map(function (object $job) use ($userMap) {
            $assigneeId = (int) ($job->assignee_user_id ?? 0);
            $assignee = $assigneeId > 0 ? ($userMap->get($assigneeId) ?? null) : null;
            $code = strtoupper(trim((string) ($job->assignee_code ?? '')));
            if ($assignee === null && $code !== '') {
                $assignee = $userMap->first(
                    static fn (User $user) => strtoupper(trim((string) $user->unique_code)) === $code
                );
                if ($assignee) {
                    $assigneeId = (int) $assignee->id;
                } else {
                    $assignee = (object) [
                        'id' => 0,
                        'unique_code' => $code,
                        'fullname' => $code,
                        'username' => $code,
                        'email' => '',
                        'profile_image' => null,
                    ];
                }
            }

            return (object) [
                'row_type' => 'job',
                'key' => $job->key,
                'id' => null,
                'client' => $job->client,
                'reference' => $job->reference,
                'module' => $job->module,
                'title' => $job->reference,
                'assignee_user_id' => $assigneeId > 0 ? $assigneeId : null,
                'assignee' => $assignee,
                'due_date' => null,
                'status' => 'allocated',
                'notes' => null,
                'visibility' => Task::VISIBILITY_PUBLIC,
                'created_by' => null,
                'view_url' => $job->view_url ?? null,
                'sort_at' => $job->sort_at,
            ];
        });

        $fromManual = $manualTasks->map(function (Task $task) {
            return (object) [
                'row_type' => 'manual',
                'key' => 'task:'.$task->id,
                'id' => $task->id,
                'client' => '—',
                'reference' => $task->title,
                'module' => 'Manual',
                'title' => $task->title,
                'assignee_user_id' => $task->assignee_user_id,
                'assignee' => $task->assignee,
                'due_date' => $task->due_date,
                'status' => $task->status,
                'notes' => $task->notes,
                'visibility' => $task->visibility ?: Task::VISIBILITY_PUBLIC,
                'created_by' => $task->created_by,
                'view_url' => null,
                'sort_at' => optional($task->updated_at)?->toDateTimeString() ?? '',
            ];
        });

        return $fromJobs
            ->concat($fromManual)
            ->sortByDesc(fn (object $row) => $row->sort_at ?? '')
            ->values();
    }

    /**
     * @param  Collection<int, object>  $rows
     * @param  Collection<int, User>  $users
     * @return Collection<int, array{key: string, user: ?User, tasks: Collection<int, object>}>
     */
    private function buildBoardColumnsFromRows(Collection $rows, Collection $users): Collection
    {
        $grouped = $rows->groupBy(fn (object $row) => (string) ((int) ($row->assignee_user_id ?? 0)));
        $userMap = $users->keyBy('id');

        $columns = collect();

        foreach ($grouped->sortKeys() as $key => $columnTasks) {
            $userId = (int) $key;
            if ($userId === 0) {
                continue;
            }
            $columns->push([
                'key' => (string) $userId,
                'user' => $userMap->get($userId) ?? ($columnTasks->first()?->assignee ?? null),
                'tasks' => $columnTasks->values(),
            ]);
        }

        $columns = $columns->sortBy(function (array $col) {
            $user = $col['user'];
            if (! $user) {
                return 'zzz';
            }
            $name = trim((string) ($user->fullname ?? ''));
            if ($name === '') {
                $name = trim((string) ($user->username ?? ''));
            }
            if ($name === '') {
                $name = trim((string) ($user->email ?? ''));
            }

            return mb_strtolower($name !== '' ? $name : 'zzz');
        })->values();

        if ($grouped->has('0') && $grouped->get('0')->isNotEmpty()) {
            $columns->push([
                'key' => '0',
                'user' => null,
                'tasks' => $grouped->get('0')->values(),
            ]);
        }

        return $columns;
    }

    /**
     * @return array<string, mixed>
     */
    private function redirectQuery(Request $request): array
    {
        $view = $request->input('view_redirect') ?: $request->query('view');
        $status = $request->input('status_redirect') ?: $request->query('status');
        $assignee = $request->input('assignee_redirect');
        if ($assignee === null || $assignee === '') {
            $assignee = $request->query('assignee');
        }

        return array_filter([
            'view' => in_array($view, ['table', 'board'], true) ? $view : null,
            'status' => $status !== null && $status !== '' ? $status : null,
            'assignee' => $assignee !== null && $assignee !== '' ? $assignee : null,
        ], static fn ($v) => $v !== null && $v !== '');
    }

    private function mayViewAllTasks(): bool
    {
        return RolePermission::userMayAccessRoute('task_management.view_all')
            || RolePermission::userMayAccessRoute('task_management')
            || RolePermission::userMayAccessRoute('dashboard');
    }

    private function mayViewSelfTasks(): bool
    {
        return RolePermission::userMayAccessRoute('task_management.view_self');
    }

    private function mayManageTasks(): bool
    {
        return RolePermission::userMayAccessRoute('task_management')
            || RolePermission::userMayAccessRoute('task_management.store')
            || RolePermission::userMayAccessRoute('task_management.update')
            || RolePermission::userMayAccessRoute('task_management.view_all')
            || RolePermission::userMayAccessRoute('dashboard');
    }

    private function mayMutateTask(Task $task): bool
    {
        $uid = (int) session('user_id', 0);

        if ($task->isPersonal()) {
            return $uid > 0 && (int) ($task->created_by ?? 0) === $uid;
        }

        if ($this->mayViewAllTasks()) {
            return true;
        }

        return $uid > 0 && (int) ($task->assignee_user_id ?? 0) === $uid;
    }

    private function deny(Request $request, string $message, int $status = 403)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'error', 'message' => $message], $status);
        }

        return redirect()
            ->route('task_management')
            ->with('error', $message);
    }
}
