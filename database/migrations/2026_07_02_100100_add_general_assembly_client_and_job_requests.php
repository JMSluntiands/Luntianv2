<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * General Assembly vertical (GA01): client row + job request types mirrored from LBS01 (EA_GA_*).
     */
    public function up(): void
    {
        if (Schema::hasTable('clients')) {
            $exists = DB::table('clients')->where('client_code', 'GA01')->exists();
            if (! $exists) {
                DB::table('clients')->insert([
                    'client_code' => 'GA01',
                    'client_name' => 'Generic Assessment Account',
                    'client_email' => 'admin@generalassembly.com',
                ]);
            }
        }

        if (! Schema::hasTable('job_requests')) {
            return;
        }

        $lbsRows = DB::table('job_requests')->where('client_code', 'LBS01')->get();
        if ($lbsRows->isEmpty()) {
            return;
        }

        $nextId = (int) DB::table('job_requests')->max('id') + 1;

        foreach ($lbsRows as $row) {
            $newRequestId = str_replace('EA_LBS_', 'EA_GA_', (string) $row->job_request_id);
            if ($newRequestId === (string) $row->job_request_id) {
                continue;
            }

            $exists = DB::table('job_requests')
                ->where('client_code', 'GA01')
                ->where('job_request_id', $newRequestId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('job_requests')->insert([
                'id'               => $nextId++,
                'client_code'      => 'GA01',
                'job_request_id'   => $newRequestId,
                'job_request_type' => $row->job_request_type,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_requests')) {
            DB::table('job_requests')
                ->where('client_code', 'GA01')
                ->whereRaw("job_request_id LIKE 'EA\\_GA\\_%'")
                ->delete();
        }

        if (Schema::hasTable('clients')) {
            DB::table('clients')->where('client_code', 'GA01')->delete();
        }
    }
};
