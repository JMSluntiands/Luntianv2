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

        if (strtolower(trim($role)) === 'admin') {
            return $next($request);
        }

        if (!RolePermission::userMayAccessRoute($routeName)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You do not have permission to access this page.'], 403);
            }
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
