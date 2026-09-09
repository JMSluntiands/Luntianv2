<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('slack_configs')) {
            return;
        }

        Schema::table('slack_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('slack_configs', 'new_job_slack_active')) {
                $table->boolean('new_job_slack_active')->default(true)->after('is_active');
            }
            if (! Schema::hasColumn('slack_configs', 'assignment_slack_active')) {
                $table->boolean('assignment_slack_active')->default(true)->after('new_job_slack_active');
            }
        });

        if (Schema::hasColumn('slack_configs', 'is_active')
            && Schema::hasColumn('slack_configs', 'new_job_slack_active')
            && Schema::hasColumn('slack_configs', 'assignment_slack_active')) {
            DB::table('slack_configs')->update([
                'new_job_slack_active' => DB::raw('`is_active`'),
                'assignment_slack_active' => DB::raw('`is_active`'),
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('slack_configs')) {
            return;
        }

        Schema::table('slack_configs', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('slack_configs', 'new_job_slack_active')) {
                $drop[] = 'new_job_slack_active';
            }
            if (Schema::hasColumn('slack_configs', 'assignment_slack_active')) {
                $drop[] = 'assignment_slack_active';
            }
            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
