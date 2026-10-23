<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('slack_configs') || Schema::hasColumn('slack_configs', 'new_job_branches')) {
            return;
        }

        Schema::table('slack_configs', function (Blueprint $table) {
            // NULL = every branch sends new-job messages.
            $table->text('new_job_branches')->nullable()->after('assignment_slack_active');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('slack_configs') && Schema::hasColumn('slack_configs', 'new_job_branches')) {
            Schema::table('slack_configs', function (Blueprint $table) {
                $table->dropColumn('new_job_branches');
            });
        }
    }
};
