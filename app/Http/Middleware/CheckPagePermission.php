<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckPagePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = Route::currentRouteName();
        if ($routeName === null || $routeName === 'unauthorized') {
            return $next($request);
        }

        $role = (string) (session('user_role') ?? '');
        if ($role === '') {
            return $next($request);
        }

        if (strtolower($role) === 'admin') {
            return $next($request);
        }

        $allowedRoutes = config('permissions.routes', []);
        $allRouteNames = [];
        foreach ($allowedRoutes as $group) {
            foreach (array_keys($group) as $name) {
                $allRouteNames[$name] = true;
            }
        }
        if (!isset($allRouteNames[$routeName])) {
            return $next($request);
        }

        if (!RolePermission::hasAccess($role, $routeName)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You do not have permission to access this page.'], 403);
            }
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
