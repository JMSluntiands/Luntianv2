<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Grant newly added page permissions to roles/users that already have related access.
     *
     * - reports → reports.export, hr, timesheet
     * - dashboard → task_management (+ create/update/delete)
     */
    public function up(): void
    {
        $grants = [
            'reports' => [
                'reports.export',
                'hr',
                'timesheet',
            ],
            'dashboard' => [
                'task_management',
                'task_management.store',
                'task_management.update',
                'task_management.destroy',
            ],
        ];

        if (Schema::hasTable('role_permissions')) {
            foreach ($grants as $sourceRoute => $targets) {
                $sources = DB::table('role_permissions')
                    ->where('route_name', $sourceRoute)
                    ->get(['role', 'branch']);

                $pairs = [];
                foreach ($sources as $row) {
                    $key = strtolower(trim((string) $row->role)).'|'.(string) $row->branch;
                    $pairs[$key] = ['role' => $row->role, 'branch' => (string) $row->branch];
                }

                foreach ($pairs as $pair) {
                    foreach ($targets as $route) {
                        $exists = DB::table('role_permissions')
                            ->where('role', $pair['role'])
                            ->where('branch', $pair['branch'])
                            ->where('route_name', $route)
                            ->exists();
                        if ($exists) {
                            continue;
                        }
                        DB::table('role_permissions')->insert([
                            'role' => $pair['role'],
                            'branch' => $pair['branch'],
                            'route_name' => $route,
                        ]);
                    }
                }
            }
        }

        if (Schema::hasTable('user_permissions')) {
            foreach ($grants as $sourceRoute => $targets) {
                $userIds = DB::table('user_permissions')
                    ->where('route_name', $sourceRoute)
                    ->distinct()
                    ->pluck('user_id');

                foreach ($userIds as $userId) {
                    $branches = DB::table('user_permissions')
                        ->where('user_id', $userId)
                        ->where('route_name', $sourceRoute)
                        ->pluck('branch')
                        ->unique()
                        ->values();

                    foreach ($branches as $branch) {
                        foreach ($targets as $route) {
                            $exists = DB::table('user_permissions')
                                ->where('user_id', $userId)
                                ->where('branch', (string) $branch)
                                ->where('route_name', $route)
                                ->exists();
                            if ($exists) {
                                continue;
                            }
                            DB::table('user_permissions')->insert([
                                'user_id' => $userId,
                                'branch' => (string) $branch,
                                'route_name' => $route,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Keep granted permissions; no destructive rollback.
    }
};
