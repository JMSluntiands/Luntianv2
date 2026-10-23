<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $routes = [
        'settings.logo_management',
        'settings.logo_management.store',
        'settings.logo_management.destroy',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('client_logos')) {
            Schema::create('client_logos', function (Blueprint $table) {
                $table->id();
                $table->string('module_key', 64)->unique();
                $table->string('logo_path');
                $table->timestamps();
            });
        }

        $this->copyGrants('role_permissions', 'role');
        $this->copyGrants('user_permissions', 'user_id');
    }

    /**
     * Whoever can open the Permission settings page also gets Logo Management.
     */
    private function copyGrants(string $table, string $ownerColumn): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $owners = DB::table($table)
            ->where('route_name', 'settings.permission')
            ->get([$ownerColumn, 'branch']);

        foreach ($owners as $owner) {
            $branch = (string) ($owner->branch ?? '');
            foreach ($this->routes as $route) {
                $exists = DB::table($table)
                    ->where($ownerColumn, $owner->{$ownerColumn})
                    ->where('branch', $branch)
                    ->where('route_name', $route)
                    ->exists();
                if (! $exists) {
                    DB::table($table)->insert([
                        $ownerColumn => $owner->{$ownerColumn},
                        'branch' => $branch,
                        'route_name' => $route,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_logos');

        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->whereIn('route_name', $this->routes)->delete();
        }
        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('route_name', $this->routes)->delete();
        }
    }
};
