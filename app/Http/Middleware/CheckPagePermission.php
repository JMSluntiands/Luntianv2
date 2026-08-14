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
            $routePermissionAlternates = [
                'lbs.job.uploadFiles' => [
                    'job_view.lbs.button.files.add',
                    'job_view.efficient_living.button.files.add',
                    'job_view.luntian.button.files.add',
                ],
                'lbs.job.deleteFile' => [
                    'job_view.lbs.button.files.delete',
                    'job_view.efficient_living.button.files.delete',
                    'job_view.luntian.button.files.delete',
                ],
                'lbs.job.file' => [
                    'job_view.lbs.button.files.add',
                    'job_view.lbs.button.files.delete',
                    'job_view.lbs.card.plans',
                    'job_view.lbs.card.documents',
                    'job_view.lbs.card.checker_uploads',
                    'job_view.efficient_living.button.files.add',
                    'job_view.efficient_living.card.plans',
                    'job_view.efficient_living.card.documents',
                    'job_view.efficient_living.card.checker_uploads',
                    'job_view.luntian.button.files.add',
                    'job_view.luntian.button.files.delete',
                    'job_view.luntian.card.checker_uploads',
                    'job_view.luntian.card.plans',
                    'job_view.luntian.card.documents',
                    'luntian.job.view',
                ],
                'lbs.job.runComment' => [
                    'job_view.lbs.button.comments.run.send',
                    'job_view.efficient_living.button.comments.run.send',
                    'job_view.luntian.button.comments.run.send',
                ],
                'lbs.job.comment' => [
                    'job_view.lbs.button.comments.job.send',
                    'job_view.efficient_living.button.comments.job.send',
                    'job_view.luntian.button.comments.job.send',
                ],
                'lbs.job.checkerUploads' => [
                    'job_view.lbs.button.checker_uploads.add',
                    'job_view.efficient_living.button.checker_uploads.add',
                    'job_view.luntian.button.checker_uploads.add',
                ],
                'bph.job.checkerUploads' => [
                    'job_view.bph.button.checker_uploads.add',
                ],
                'reports.export' => [
                    'reports',
                ],
                'hr' => [
                    'hr',
                    'reports',
                ],
                'timesheet' => [
                    'timesheet',
                    'reports',
                ],
                'users.status' => [
                    'users.update',
                    'users.index',
                    'users.edit',
                ],
                'task_management' => [
                    'task_management.view_all',
                    'task_management.view_self',
                    'dashboard',
                ],
                'task_management.view_all' => [
                    'task_management',
                ],
                'task_management.view_self' => [
                    'task_management',
                ],
                'task_management.store' => [
                    'task_management',
                    'task_management.view_all',
                    'task_management.view_self',
                    'dashboard',
                ],
                'task_management.update' => [
                    'task_management',
                    'task_management.view_all',
                    'task_management.view_self',
                    'dashboard',
                ],
                'task_management.destroy' => [
                    'task_management',
                    'task_management.view_all',
                    'dashboard',
                ],
                'forum_thread' => [
                    'dashboard',
                ],
                'forum_thread.recent' => [
                    'forum_thread',
                    'dashboard',
                ],
                'forum_thread.store' => [
                    'forum_thread.post',
                    'forum_thread',
                ],
                'forum_thread.pin' => [
                    'forum_thread.post',
                    'forum_thread',
                    'dashboard',
                ],
                'forum_thread.image' => [
                    'forum_thread',
                ],
                'forum_thread.destroy' => [
                    'forum_thread',
                ],
                'forum_thread.comment.store' => [
                    'forum_thread.comment',
                    'forum_thread',
                ],
                'forum_thread.comment.destroy' => [
                    'forum_thread.comment',
                    'forum_thread',
                ],
            ];
            if (isset($routePermissionAlternates[$routeName])) {
                foreach ($routePermissionAlternates[$routeName] as $altRoute) {
                    if (RolePermission::userMayAccessRoute($altRoute)) {
                        return $next($request);
                    }
                }
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You do not have permission to access this page.'], 403);
            }
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
