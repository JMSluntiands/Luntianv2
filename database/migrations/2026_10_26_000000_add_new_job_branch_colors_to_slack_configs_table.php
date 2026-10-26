<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('slack_configs') || Schema::hasColumn('slack_configs', 'new_job_branch_colors')) {
            return;
        }

        Schema::table('slack_configs', function (Blueprint $table) {
            // NULL or a missing branch = use the default color in SlackNewJobNotifier.
            $table->text('new_job_branch_colors')->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('slack_configs') && Schema::hasColumn('slack_configs', 'new_job_branch_colors')) {
            Schema::table('slack_configs', function (Blueprint $table) {
                $table->dropColumn('new_job_branch_colors');
            });
        }
    }
};
