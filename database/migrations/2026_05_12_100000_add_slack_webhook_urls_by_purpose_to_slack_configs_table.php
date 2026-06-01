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
            $table->string('webhook_new_job_url', 500)->nullable()->after('webhook_url');
            $table->string('webhook_assignment_url', 500)->nullable()->after('webhook_new_job_url');
        });

        $rows = DB::table('slack_configs')->whereNotNull('webhook_url')->where('webhook_url', '!=', '')->get(['id', 'webhook_url']);
        foreach ($rows as $row) {
            DB::table('slack_configs')->where('id', $row->id)->update([
                'webhook_new_job_url' => $row->webhook_url,
                'webhook_assignment_url' => $row->webhook_url,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('slack_configs', function (Blueprint $table) {
            $table->dropColumn(['webhook_new_job_url', 'webhook_assignment_url']);
        });
    }
};
