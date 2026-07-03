<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $routes = ['settings.jotform_config', 'settings.jotform_config.toggle'];
        $sourceRoute = 'settings.email_config';

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')
                ->where('route_name', $sourceRoute)
                ->select('role', 'branch')
                ->get();

            $insert = [];
            foreach ($rows as $r) {
                foreach ($routes as $route) {
                    $insert[] = [
                        'role' => (string) $r->role,
                        'branch' => (string) ($r->branch ?? ''),
                        'route_name' => $route,
                    ];
                }
            }
            if ($insert !== []) {
                DB::table('role_permissions')->insertOrIgnore($insert);
            }
        }

        if (Schema::hasTable('user_permissions')) {
            $rows = DB::table('user_permissions')
                ->where('route_name', $sourceRoute)
                ->select('user_id', 'branch')
                ->get();

            $insert = [];
            foreach ($rows as $r) {
                foreach ($routes as $route) {
                    $insert[] = [
                        'user_id' => (int) $r->user_id,
                        'branch' => (string) ($r->branch ?? ''),
                        'route_name' => $route,
                    ];
                }
            }
            if ($insert !== []) {
                DB::table('user_permissions')->insertOrIgnore($insert);
            }
        }
    }

    public function down(): void
    {
        foreach (['settings.jotform_config', 'settings.jotform_config.toggle'] as $route) {
            if (Schema::hasTable('role_permissions')) {
                DB::table('role_permissions')->where('route_name', $route)->delete();
            }
            if (Schema::hasTable('user_permissions')) {
                DB::table('user_permissions')->where('route_name', $route)->delete();
            }
        }
    }
};
