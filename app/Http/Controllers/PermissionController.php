<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Flatten config into ordered blocks with group headings (matches sidebar: Job management, …).
     *
     * @return list<array{group_heading: ?string, section: string, routes: array<string, string>}>
     */
    private static function permissionBlocksForView(): array
    {
        $routes = config('permissions.routes', []);
        $display = config('permissions.route_display', []);
        $blocks = [];
        $seen = [];

        foreach ($display as $group) {
            $groupHeading = $group['heading'] ?? null;
            $firstInGroup = true;
            foreach ($group['sections'] ?? [] as $sectionName) {
                if (!isset($routes[$sectionName]) || !is_array($routes[$sectionName])) {
                    continue;
                }
                $blocks[] = [
                    'group_heading' => $firstInGroup ? $groupHeading : null,
                    'section' => $sectionName,
                    'routes' => $routes[$sectionName],
                ];
                $seen[$sectionName] = true;
                $firstInGroup = false;
            }
        }

        foreach ($routes as $sectionName => $sectionRoutes) {
            if (isset($seen[$sectionName]) || !is_array($sectionRoutes)) {
                continue;
            }
            $blocks[] = [
                'group_heading' => 'Other',
                'section' => $sectionName,
                'routes' => $sectionRoutes,
            ];
        }

        return $blocks;
    }

    /**
     * @return list<string>
     */
    private static function branchNamesOrdered(): array
    {
        return Branch::query()
            ->orderBy('branch_name')
            ->pluck('branch_name')
            ->filter(static fn ($b) => $b !== null && trim((string) $b) !== '')
            ->values()
            ->all();
    }

    /**
     * Resolve submitted/query branch to a stored branch_name, or '' (all branches).
     */
    private static function resolvePermissionBranch(string $raw): ?string
    {
        $norm = RolePermission::normalizeBranch($raw);
        if ($norm === '') {
            return '';
        }

        $canonical = Branch::query()
            ->whereRaw('LOWER(branch_name) = ?', [mb_strtolower($norm)])
            ->value('branch_name');

        return $canonical !== null ? (string) $canonical : null;
    }

    public function index(Request $request)
    {
        if (strtolower((string) session('user_role', '')) !== 'admin') {
            return redirect()->route('unauthorized');
        }

        $roles = config('permissions.roles', []);
        $branches = self::branchNamesOrdered();

        $selectedRole = (string) $request->query('role', $roles[0] ?? '');
        $selectedRole = RolePermission::canonicalRole($selectedRole);
        if (!in_array($selectedRole, $roles, true)) {
            $selectedRole = $roles[0] ?? '';
        }

        $branchParam = RolePermission::normalizeBranch((string) $request->query('branch', ''));
        $selectedBranch = '';
        if ($branchParam !== '') {
            $resolved = self::resolvePermissionBranch($branchParam);
            $selectedBranch = $resolved !== null ? $resolved : '';
        }

        $allowedForSelection = [];
        if ($selectedRole !== '') {
            if ($selectedBranch === '') {
                $allowedForSelection = RolePermission::where('role', $selectedRole)
                    ->where('branch', '')
                    ->pluck('route_name')
                    ->toArray();
            } else {
                $allowedForSelection = RolePermission::where('role', $selectedRole)
                    ->whereRaw('LOWER(branch) = ?', [mb_strtolower($selectedBranch)])
                    ->pluck('route_name')
                    ->toArray();
            }
        }

        $editorBranchName = null;
        $userId = session('user_id');
        if ($userId) {
            $editorBranchName = User::whereKey($userId)->value('branch');
            $editorBranchName = $editorBranchName !== null && trim((string) $editorBranchName) !== ''
                ? trim((string) $editorBranchName)
                : null;
        }

        return view('settings.permission', [
            'sidebar_active' => 'settings.permission',
            'permissionBlocks' => self::permissionBlocksForView(),
            'roles' => $roles,
            'branches' => $branches,
            'selectedRole' => $selectedRole,
            'selectedBranch' => $selectedBranch,
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
            'permission_role' => 'required|string|max:64',
            'permission_branch' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|string|max:8',
        ]);

        $roles = config('permissions.roles', []);
        $role = RolePermission::canonicalRole((string) $request->input('permission_role'));
        if (!in_array($role, $roles, true)) {
            return redirect()->route('settings.permission')->withErrors(['permission_role' => 'Invalid role.']);
        }

        $branchRaw = RolePermission::normalizeBranch((string) $request->input('permission_branch', ''));
        $branch = '';
        if ($branchRaw !== '') {
            $resolved = self::resolvePermissionBranch($branchRaw);
            if ($resolved === null || $resolved === '') {
                return redirect()->route('settings.permission', [
                    'role' => $role,
                    'branch' => $branchRaw,
                ])->withErrors(['permission_branch' => 'Unknown branch office.']);
            }
            $branch = $resolved;
        }

        $permissions = $request->input('permissions', []);
        $allRouteNames = [];
        foreach (config('permissions.routes', []) as $group) {
            foreach (array_keys($group) as $routeName) {
                $allRouteNames[] = $routeName;
            }
        }

        DB::transaction(function () use ($permissions, $allRouteNames, $role, $branch) {
            if ($branch === '') {
                RolePermission::where('role', $role)->where('branch', '')->delete();
            } else {
                RolePermission::where('role', $role)
                    ->whereRaw('LOWER(branch) = ?', [mb_strtolower($branch)])
                    ->delete();
            }

            $rows = [];
            foreach ($permissions as $routeName => $checked) {
                if (!is_string($routeName) || !in_array($routeName, $allRouteNames, true)) {
                    continue;
                }
                $on = $checked === '1' || $checked === 1 || $checked === true || $checked === 'true';
                if ($on) {
                    $rows[] = ['role' => $role, 'branch' => $branch, 'route_name' => $routeName];
                }
            }
            foreach (array_chunk($rows, 100) as $chunk) {
                RolePermission::insert($chunk);
            }
        });

        $query = ['saved' => 1, 'role' => $role];
        if ($branch !== '') {
            $query['branch'] = $branch;
        }

        return redirect()->route('settings.permission', $query);
    }
}
