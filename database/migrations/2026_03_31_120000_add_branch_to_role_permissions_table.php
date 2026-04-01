<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropUnique(['role', 'route_name']);
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->string('branch', 255)->default('')->after('role');
            $table->unique(['role', 'branch', 'route_name']);
        });
    }

    public function down(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropUnique(['role', 'branch', 'route_name']);
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropColumn('branch');
            $table->unique(['role', 'route_name']);
        });
    }
};
