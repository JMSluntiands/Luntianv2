<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['job_fyrs', 'fyrs_assessor_jobs'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            if (Schema::hasColumn($table, 'estate') && ! Schema::hasColumn($table, 'builder')) {
                DB::statement("ALTER TABLE `{$table}` CHANGE `estate` `builder` VARCHAR(255) NULL");
            }
        }
    }

    public function down(): void
    {
        foreach (['job_fyrs', 'fyrs_assessor_jobs'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            if (Schema::hasColumn($table, 'builder') && ! Schema::hasColumn($table, 'estate')) {
                DB::statement("ALTER TABLE `{$table}` CHANGE `builder` `estate` VARCHAR(255) NULL");
            }
        }
    }
};
