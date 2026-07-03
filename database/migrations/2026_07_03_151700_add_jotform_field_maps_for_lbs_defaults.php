<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jotform_configs')) {
            return;
        }

        Schema::table('jotform_configs', function (Blueprint $table) {
            $columns = [
                'map_compliance' => 'map_notes',
                'map_client' => 'map_compliance',
                'map_priority' => 'map_client',
                'map_job_type' => 'map_priority',
                'map_assigned_to' => 'map_job_type',
                'map_checked_by' => 'map_assigned_to',
                'map_job_status' => 'map_checked_by',
            ];

            foreach ($columns as $name => $after) {
                if (! Schema::hasColumn('jotform_configs', $name)) {
                    $table->string($name, 120)->nullable()->after($after);
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('jotform_configs')) {
            return;
        }

        Schema::table('jotform_configs', function (Blueprint $table) {
            foreach (['map_job_status', 'map_checked_by', 'map_assigned_to', 'map_job_type', 'map_priority', 'map_client', 'map_compliance'] as $column) {
                if (Schema::hasColumn('jotform_configs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
