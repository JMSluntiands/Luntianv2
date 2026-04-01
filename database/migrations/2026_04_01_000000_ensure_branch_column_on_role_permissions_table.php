<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs MySQL (or any) databases where role_permissions.branch is missing —
 * e.g. code was deployed but migrate was not run, or migrations table is out of sync.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('role_permissions')) {
            return;
        }

        if (Schema::hasColumn('role_permissions', 'branch')) {
            return;
        }

        try {
            Schema::table('role_permissions', static function (Blueprint $table) {
                $table->dropUnique(['role', 'route_name']);
            });
        } catch (\Throwable) {
            // Index may already be dropped or have a different name on older DBs.
        }

        Schema::table('role_permissions', static function (Blueprint $table) {
            $table->string('branch', 255)->default('')->after('role');
        });

        try {
            Schema::table('role_permissions', static function (Blueprint $table) {
                $table->unique(['role', 'branch', 'route_name']);
            });
        } catch (\Throwable) {
            // Composite unique may already exist.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('role_permissions') || ! Schema::hasColumn('role_permissions', 'branch')) {
            return;
        }

        try {
            Schema::table('role_permissions', static function (Blueprint $table) {
                $table->dropUnique(['role', 'branch', 'route_name']);
            });
        } catch (\Throwable) {
        }

        Schema::table('role_permissions', static function (Blueprint $table) {
            $table->dropColumn('branch');
        });

        try {
            Schema::table('role_permissions', static function (Blueprint $table) {
                $table->unique(['role', 'route_name']);
            });
        } catch (\Throwable) {
        }
    }
};
