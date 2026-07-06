<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legacy tables (clients, jobs, client_accounts) use utf8mb4_general_ci.
     * job_general_assembly was created with Laravel's default utf8mb4_unicode_ci,
     * which breaks JOINs (e.g. General Assembly mailbox).
     */
    public function up(): void
    {
        if (! Schema::hasTable('job_general_assembly')) {
            return;
        }

        DB::statement(
            'ALTER TABLE job_general_assembly CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci'
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_general_assembly')) {
            return;
        }

        DB::statement(
            'ALTER TABLE job_general_assembly CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }
};
