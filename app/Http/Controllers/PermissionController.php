<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * @return array<string, string>
     */
    private static function routeLabelMap(): array
    {
        $out = [];
        foreach (config('permissions.routes', []) as $group) {
            if (!is_array($group)) {
                continue;
            }
            foreach ($group as $name => $label) {
                if (is_string($name)) {
                    $out[$name] = is_string($label) ? $label : $name;
                }
            }
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function jobModuleKeysOrdered(): array
    {
        return array_keys(config('permissions.job_ui_modules', []));
    }

    /**
     * @return list<string>
     */
    private static function allConfigurableRouteNames(): array
    {
        $names = [];
        foreach (config('permissions.routes', []) as $group) {
            if (!is_array($group)) {
                continue;
            }
            foreach (array_keys($group) as $name) {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * @return list<array{heading: string, routes: array<string, string>}>
     */
    private static function permissionJobColumnsForView(string $jobKey): array
    {
        $modules = config('permissions.job_ui_modules', []);
        if (!isset($modules[$jobKey]) || !is_array($modules[$jobKey])) {
            return [];
        }

        $labels = self::routeLabelMap();
        $m = $modules[$jobKey];
        $columnTitles = [
            'sidebar' => 'Sidebar buttons',
            'card' => 'Card',
            'buttons' => 'Card edit/add buttons',
        ];
        $columns = [];
        foreach (['sidebar', 'card', 'buttons'] as $col) {
            $routes = [];
            foreach ($m[$col] ?? [] as $routeName) {
                if (!is_string($routeName) || $routeName === '') {
                    continue;
                }
                $routes[$routeName] = $labels[$routeName] ?? $routeName;
            }
            $columns[] = [
                'heading' => $columnTitles[$col],
                'routes' => $routes,
            ];
        }

        return $columns;
    }

    /**
     * @return list<array{key: string, label: string, columns: list<array{heading: string, routes: array<string, string>}>}>
     */
    private static function permissionSectionsForView(): array
    {
        $sections = [];
        foreach (self::jobModuleKeysOrdered() as $jk) {
            $sections[] = [
                'key' => $jk,
                'label' => (string) (config('permissions.job_ui_modules.'.$jk.'.label') ?? $jk),
                'columns' => self::permissionJobColumnsForView($jk),
            ];
        }

        return $sections;
    }

    public function index(Request $request)
    {
        if (strtolower((string) session('user_role', '')) !== 'admin') {
            return redirect()->route('unauthorized');
        }

        $permissionSections = self::permissionSectionsForView();

        $users = User::query()
            ->orderBy('fullname')
            ->orderBy('username')
            ->get(['id', 'fullname', 'username', 'role', 'branch']);

        if ($users->isEmpty()) {
            return view('settings.permission', [
                'sidebar_active' => 'settings.permission',
                'permissionSections' => [],
                'users' => $users,
                'selectedUserId' => 0,
                'selectedUser' => null,
                'allowedForSelection' => [],
                'editorBranchName' => null,
                'noUsers' => true,
            ]);
        }

        $selectedUserId = (int) $request->query('user', 0);
        if ($selectedUserId <= 0 && $users->isNotEmpty()) {
            $selectedUserId = (int) $users->first()->id;
        }

        $selectedUser = $users->firstWhere('id', $selectedUserId);
        if ($selectedUser === null && $users->isNotEmpty()) {
            $selectedUser = $users->first();
            $selectedUserId = (int) $selectedUser->id;
        }

        $allowedForSelection = [];
        if ($selectedUserId > 0) {
            $allowedForSelection = UserPermission::where('user_id', $selectedUserId)
                ->pluck('route_name')
                ->unique()
                ->values()
                ->all();
        }

        $editorBranchName = null;
        $editorId = session('user_id');
        if ($editorId) {
            $editorBranchName = User::whereKey($editorId)->value('branch');
            $editorBranchName = $editorBranchName !== null && trim((string) $editorBranchName) !== ''
                ? trim((string) $editorBranchName)
                : null;
        }

        return view('settings.permission', [
            'sidebar_active' => 'settings.permission',
            'permissionSections' => $permissionSections,
            'users' => $users,
            'selectedUserId' => $selectedUserId,
            'selectedUser' => $selectedUser,
            'allowedForSelection' => $allowedForSelection,
            'editorBranchName' => $editorBranchName,
        ]);
    }

    public function store(Request $request)
    {
        if (strtolower((string) session('user_role', '')) !== 'admin') {
            return redirect()->route('unauthorized');
        }

        $request->validate([
            'permission_user_id' => 'required|integer|exists:users,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|string|max:8',
        ]);

        $userId = (int) $request->input('permission_user_id');
        $permissions = $request->input('permissions', []);
        $allRouteNames = self::allConfigurableRouteNames();

        DB::transaction(function () use ($permissions, $allRouteNames, $userId) {
            UserPermission::where('user_id', $userId)->delete();

            $rows = [];
            foreach ($allRouteNames as $routeName) {
                $checked = $permissions[$routeName] ?? '0';
                $on = $checked === '1' || $checked === 1 || $checked === true || $checked === 'true';
                if ($on) {
                    $rows[] = ['user_id' => $userId, 'branch' => '', 'route_name' => $routeName];
                }
            }
            foreach (array_chunk($rows, 100) as $chunk) {
                UserPermission::insert($chunk);
            }
        });

        return redirect()->route('settings.permission', ['saved' => 1, 'user' => $userId]);
    }
}
