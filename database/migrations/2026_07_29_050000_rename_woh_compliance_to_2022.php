<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('compliances')) {
            $woh = DB::table('compliances')
                ->where(function ($q) {
                    $q->where('column', 'like', '%Whole of Home%')
                        ->orWhere('column', 'like', '%(WOH)%')
                        ->orWhere('column', 'like', '% WOH%');
                })
                ->get();

            $plain2022 = DB::table('compliances')->where('column', '2022')->first();

            foreach ($woh as $row) {
                if ($plain2022 && (int) $plain2022->id !== (int) $row->id) {
                    DB::table('compliances')->where('id', $row->id)->delete();
                } else {
                    DB::table('compliances')->where('id', $row->id)->update([
                        'column' => '2022',
                        'updated_at' => now(),
                    ]);
                    $plain2022 = (object) ['id' => $row->id];
                }
            }
        }

        $tables = [
            'jobs' => 'ncc_compliance',
            'job_bph' => 'ncc',
            'job_fyrs' => 'ncc',
            'job_amt' => 'ncc',
            'job_csp' => 'ncc',
            'job_nh' => 'ncc',
            'job_bluinq' => 'ncc',
            'job_lc_home_builder' => 'ncc',
            'job_leading_energy' => 'ncc',
        ];

        foreach ($tables as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }
            DB::table($table)
                ->where(function ($q) use ($column) {
                    $q->where($column, 'like', '%Whole of Home%')
                        ->orWhere($column, 'like', '%(WOH)%')
                        ->orWhere($column, 'like', '% WOH%')
                        ->orWhere($column, '2022_woh')
                        ->orWhere($column, '2023_woh');
                })
                ->update([$column => '2022']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('compliances')) {
            DB::table('compliances')
                ->where('column', '2022')
                ->update([
                    'column' => '2022 Whole of Home (WOH)',
                    'updated_at' => now(),
                ]);
        }
    }
};
