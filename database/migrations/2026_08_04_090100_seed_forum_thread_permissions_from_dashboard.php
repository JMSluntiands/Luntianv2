<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Grant forum feed/post/comment/delete to roles (and user permissions) that already have dashboard or forum_thread.
     */
    public function up(): void
    {
        $targets = [
            'forum_thread',
            'forum_thread.post',
            'forum_thread.comment',
            'forum_thread.destroy',
            'forum_thread.comment.destroy',
        ];

        if (Schema::hasTable('role_permissions')) {
            $sources = DB::table('role_permissions')
                ->whereIn('route_name', ['dashboard', 'forum_thread'])
                ->get(['role', 'branch', 'route_name']);

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

        if (Schema::hasTable('user_permissions')) {
            $userIds = DB::table('user_permissions')
                ->whereIn('route_name', ['dashboard', 'forum_thread'])
                ->distinct()
                ->pluck('user_id');

            foreach ($userIds as $userId) {
                $branches = DB::table('user_permissions')
                    ->where('user_id', $userId)
                    ->whereIn('route_name', ['dashboard', 'forum_thread'])
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

    public function down(): void
    {
        // Keep granted permissions; no destructive rollback.
    }
};
