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
            if (! Schema::hasColumn('slack_configs', 'webhook_new_job_url')) {
                $table->string('webhook_new_job_url', 500)->nullable()->after('webhook_url');
            }
            if (! Schema::hasColumn('slack_configs', 'webhook_assignment_url')) {
                $table->string('webhook_assignment_url', 500)->nullable()->after('webhook_new_job_url');
            }
        });

        $rows = DB::table('slack_configs')
            ->whereNotNull('webhook_url')
            ->where('webhook_url', '!=', '')
            ->get(['id', 'webhook_url', 'webhook_new_job_url', 'webhook_assignment_url']);

        foreach ($rows as $row) {
            $update = [];
            if (empty($row->webhook_new_job_url)) {
                $update['webhook_new_job_url'] = $row->webhook_url;
            }
            if (empty($row->webhook_assignment_url)) {
                $update['webhook_assignment_url'] = $row->webhook_url;
            }
            if ($update !== []) {
                DB::table('slack_configs')->where('id', $row->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('slack_configs')) {
            return;
        }

        Schema::table('slack_configs', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('slack_configs', 'webhook_new_job_url')) {
                $drop[] = 'webhook_new_job_url';
            }
            if (Schema::hasColumn('slack_configs', 'webhook_assignment_url')) {
                $drop[] = 'webhook_assignment_url';
            }
            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
