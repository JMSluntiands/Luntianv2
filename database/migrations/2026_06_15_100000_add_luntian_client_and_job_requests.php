<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Luntian vertical (LT01): client row + job request types mirrored from LBS01 (EA_LT_*).
     */
    public function up(): void
    {
        if (Schema::hasTable('clients')) {
            $exists = DB::table('clients')->where('client_code', 'LT01')->exists();
            if (! $exists) {
                DB::table('clients')->insert([
                    'client_code' => 'LT01',
                    'client_name' => 'Luntian Account',
                    'client_email' => 'admin@luntiands.com',
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
            $newRequestId = str_replace('EA_LBS_', 'EA_LT_', (string) $row->job_request_id);
            if ($newRequestId === (string) $row->job_request_id) {
                continue;
            }

            $exists = DB::table('job_requests')
                ->where('client_code', 'LT01')
                ->where('job_request_id', $newRequestId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('job_requests')->insert([
                'id'               => $nextId++,
                'client_code'      => 'LT01',
                'job_request_id'   => $newRequestId,
                'job_request_type' => $row->job_request_type,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_requests')) {
            DB::table('job_requests')
                ->where('client_code', 'LT01')
                ->whereRaw("job_request_id LIKE 'EA\\_LT\\_%'")
                ->delete();
        }

        if (Schema::hasTable('clients')) {
            DB::table('clients')->where('client_code', 'LT01')->delete();
        }
    }
};
