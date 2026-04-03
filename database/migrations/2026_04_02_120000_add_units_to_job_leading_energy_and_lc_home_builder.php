<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_leading_energy') && ! Schema::hasColumn('job_leading_energy', 'units')) {
            Schema::table('job_leading_energy', function (Blueprint $table) {
                $table->unsignedInteger('units')->default(0)->after('status');
            });
        }
        if (Schema::hasTable('job_lc_home_builder') && ! Schema::hasColumn('job_lc_home_builder', 'units')) {
            Schema::table('job_lc_home_builder', function (Blueprint $table) {
                $table->unsignedInteger('units')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_leading_energy') && Schema::hasColumn('job_leading_energy', 'units')) {
            Schema::table('job_leading_energy', function (Blueprint $table) {
                $table->dropColumn('units');
            });
        }
        if (Schema::hasTable('job_lc_home_builder') && Schema::hasColumn('job_lc_home_builder', 'units')) {
            Schema::table('job_lc_home_builder', function (Blueprint $table) {
                $table->dropColumn('units');
            });
        }
    }
};
