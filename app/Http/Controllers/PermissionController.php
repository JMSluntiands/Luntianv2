<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        if (strtolower((string) session('user_role', '')) !== 'admin') {
            return redirect()->route('unauthorized');
        }

        $sections = config('permissions.routes', []);
        $roles = config('permissions.roles', []);

        $allowed = RolePermission::all()->groupBy('role');
        $allowedMap = [];
        foreach ($allowed as $role => $rows) {
            $allowedMap[$role] = $rows->pluck('route_name')->toArray();
        }

        return view('settings.permission', [
            'sidebar_active' => 'settings.permission',
            'sections' => $sections,
            'roles' => $roles,
            'allowedMap' => $allowedMap,
        ]);
    }

    public function store(Request $request)
    {
        if (strtolower((string) session('user_role', '')) !== 'admin') {
            return redirect()->route('unauthorized');
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|array',
            'permissions.*.*' => 'nullable|string|max:128',
        ]);

        $permissions = $request->input('permissions', []);
        $allRouteNames = [];
        foreach (config('permissions.routes', []) as $group) {
            foreach (array_keys($group) as $routeName) {
                $allRouteNames[] = $routeName;
            }
        }

        DB::transaction(function () use ($permissions, $allRouteNames) {
            RolePermission::query()->delete();
            $rows = [];
            foreach ($permissions as $role => $routes) {
                if (!is_array($routes)) {
                    continue;
                }
                foreach ($routes as $routeName => $checked) {
                    if ($checked && in_array($routeName, $allRouteNames, true)) {
                        $rows[] = ['role' => $role, 'route_name' => $routeName];
                    }
                }
            }
            foreach (array_chunk($rows, 100) as $chunk) {
                RolePermission::insert($chunk);
            }
        });

        return redirect()->route('settings.permission', ['saved' => 1]);
    }
}
