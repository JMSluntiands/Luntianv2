<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_fyrs') || ! Schema::hasColumn('job_fyrs', 'job_number')) {
            return;
        }

        DB::statement('ALTER TABLE `job_fyrs` MODIFY `job_number` VARCHAR(100) NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_fyrs') || ! Schema::hasColumn('job_fyrs', 'job_number')) {
            return;
        }

        DB::statement('ALTER TABLE `job_fyrs` MODIFY `job_number` VARCHAR(6) NOT NULL');
    }
};
