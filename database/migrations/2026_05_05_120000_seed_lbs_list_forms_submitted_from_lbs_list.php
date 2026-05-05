<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Grant "LBS Forms Submitted Jobs" wherever "LBS List" is already granted so
     * existing behaviour stays the same after the permission is added.
     */
    public function up(): void
    {
        $newRoute = 'lbs.list.formsSubmitted';
        $sourceRoute = 'lbs.list';

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')
                ->where('route_name', $sourceRoute)
                ->select('role', 'branch')
                ->get();

            $insert = [];
            foreach ($rows as $r) {
                $insert[] = [
                    'role' => (string) $r->role,
                    'branch' => (string) ($r->branch ?? ''),
                    'route_name' => $newRoute,
                ];
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
                $insert[] = [
                    'user_id' => (int) $r->user_id,
                    'branch' => (string) ($r->branch ?? ''),
                    'route_name' => $newRoute,
                ];
            }
            if ($insert !== []) {
                DB::table('user_permissions')->insertOrIgnore($insert);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->where('route_name', 'lbs.list.formsSubmitted')->delete();
        }
        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->where('route_name', 'lbs.list.formsSubmitted')->delete();
        }
    }
};
