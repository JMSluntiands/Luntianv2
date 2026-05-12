<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slack_configs', function (Blueprint $table) {
            $table->boolean('new_job_slack_active')->default(true)->after('is_active');
            $table->boolean('assignment_slack_active')->default(true)->after('new_job_slack_active');
        });

        if (Schema::hasColumn('slack_configs', 'is_active')) {
            DB::table('slack_configs')->update([
                'new_job_slack_active' => DB::raw('`is_active`'),
                'assignment_slack_active' => DB::raw('`is_active`'),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('slack_configs', function (Blueprint $table) {
            $table->dropColumn(['new_job_slack_active', 'assignment_slack_active']);
        });
    }
};
