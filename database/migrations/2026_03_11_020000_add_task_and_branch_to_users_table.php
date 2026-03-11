<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch')) {
                $table->string('branch', 255)->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'task')) {
                $table->string('task', 255)->nullable()->after('branch');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'task')) {
                $table->dropColumn('task');
            }

            if (Schema::hasColumn('users', 'branch')) {
                $table->dropColumn('branch');
            }
        });
    }
};

