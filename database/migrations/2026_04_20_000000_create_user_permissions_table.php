<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('branch', 255)->default('');
            $table->string('route_name', 128)->index();
            $table->unique(['user_id', 'branch', 'route_name']);
        });

        if (Schema::hasTable('users') && Schema::hasTable('role_permissions')) {
            $this->seedFromRolePermissions();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }

    private function seedFromRolePermissions(): void
    {
        $rolesConfigured = ['Admin', 'Branch', 'Staff', 'Checker', 'User'];
        $canonical = static function (string $role) use ($rolesConfigured): string {
            $role = trim($role);
            foreach ($rolesConfigured as $configured) {
                if (strcasecmp((string) $configured, $role) === 0) {
                    return (string) $configured;
                }
            }

            return $role;
        };

        $roleRows = DB::table('role_permissions')->select('role', 'branch', 'route_name')->get();
        if ($roleRows->isEmpty()) {
            return;
        }

        $users = DB::table('users')->select('id', 'role', 'branch')->get();
        $insert = [];

        foreach ($roleRows as $rp) {
            $permRole = $canonical((string) $rp->role);
            $branch = trim((string) ($rp->branch ?? ''));

            foreach ($users as $u) {
                if ($canonical((string) $u->role) !== $permRole) {
                    continue;
                }
                if ($branch !== '') {
                    $ub = trim((string) ($u->branch ?? ''));
                    if (mb_strtolower($ub) !== mb_strtolower($branch)) {
                        continue;
                    }
                }
                $insert[] = [
                    'user_id' => (int) $u->id,
                    'branch' => $branch,
                    'route_name' => (string) $rp->route_name,
                ];
            }
        }

        foreach (array_chunk($insert, 200) as $chunk) {
            DB::table('user_permissions')->insertOrIgnore($chunk);
        }
    }
};
