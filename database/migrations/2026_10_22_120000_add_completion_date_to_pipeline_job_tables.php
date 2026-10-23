<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'job_bph',
        'job_amt',
        'job_fyrs',
        'job_csp',
        'job_nh',
        'job_lc_home_builder',
        'job_leading_energy',
        'job_el',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'completion_date')) {
                continue;
            }

            Schema::table($table, function (Blueprint $t) {
                $t->dateTime('completion_date')->nullable();
            });

            // Existing completed jobs have no recorded completion time; updated_at is the closest value.
            DB::table($table)
                ->where('status', 'Completed')
                ->whereNull('completion_date')
                ->update(['completion_date' => DB::raw('updated_at')]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'completion_date')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('completion_date');
                });
            }
        }
    }
};
