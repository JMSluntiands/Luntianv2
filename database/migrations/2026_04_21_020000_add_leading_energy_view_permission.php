<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function copyForUserPermissions(): void
    {
        if (!Schema::hasTable('user_permissions')) {
            return;
        }

        $rows = DB::table('user_permissions')
            ->where('route_name', 'leading_energy.update')
            ->select('user_id', 'branch')
            ->get();

        $payload = [];
        foreach ($rows as $row) {
            $payload[] = [
                'user_id' => (int) $row->user_id,
                'branch' => (string) $row->branch,
                'route_name' => 'leading_energy.view',
            ];
        }

        foreach (array_chunk($payload, 300) as $chunk) {
            DB::table('user_permissions')->insertOrIgnore($chunk);
        }
    }

    private function copyForRolePermissions(): void
    {
        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        $rows = DB::table('role_permissions')
            ->where('route_name', 'leading_energy.update')
            ->select('role', 'branch')
            ->get();

        $payload = [];
        foreach ($rows as $row) {
            $payload[] = [
                'role' => (string) $row->role,
                'branch' => (string) $row->branch,
                'route_name' => 'leading_energy.view',
            ];
        }

        foreach (array_chunk($payload, 300) as $chunk) {
            DB::table('role_permissions')->insertOrIgnore($chunk);
        }
    }

    public function up(): void
    {
        $this->copyForUserPermissions();
        $this->copyForRolePermissions();
    }

    public function down(): void
    {
        // Keep data non-destructive on rollback.
    }
};
