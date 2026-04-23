<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Grant `amt.rfq` and `amt.purchase` wherever `amt.list` is already allowed (role + user overrides).
 */
return new class extends Migration
{
    public function up(): void
    {
        $targets = ['amt.rfq', 'amt.purchase'];

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')->where('route_name', 'amt.list')->select('role', 'branch')->get();
            $chunk = [];
            foreach ($rows as $row) {
                foreach ($targets as $routeName) {
                    $chunk[] = [
                        'role' => $row->role,
                        'branch' => $row->branch ?? '',
                        'route_name' => $routeName,
                    ];
                }
            }
            foreach (array_chunk($chunk, 200) as $part) {
                DB::table('role_permissions')->insertOrIgnore($part);
            }
        }

        if (Schema::hasTable('user_permissions')) {
            $rows = DB::table('user_permissions')->where('route_name', 'amt.list')->select('user_id', 'branch')->get();
            $chunk = [];
            foreach ($rows as $row) {
                foreach ($targets as $routeName) {
                    $chunk[] = [
                        'user_id' => (int) $row->user_id,
                        'branch' => (string) ($row->branch ?? ''),
                        'route_name' => $routeName,
                    ];
                }
            }
            foreach (array_chunk($chunk, 200) as $part) {
                DB::table('user_permissions')->insertOrIgnore($part);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->whereIn('route_name', ['amt.rfq', 'amt.purchase'])->delete();
        }
        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('route_name', ['amt.rfq', 'amt.purchase'])->delete();
        }
    }
};
