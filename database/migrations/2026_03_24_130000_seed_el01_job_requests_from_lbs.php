<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mirror LBS01 job request types for Efficient Living (EL01), same labels, EA_EL_* ids.
     */
    public function up(): void
    {
        if (! Schema::hasTable('job_requests')) {
            return;
        }

        $lbsRows = DB::table('job_requests')->where('client_code', 'LBS01')->get();
        if ($lbsRows->isEmpty()) {
            return;
        }

        $nextId = (int) DB::table('job_requests')->max('id') + 1;

        foreach ($lbsRows as $row) {
            $newRequestId = str_replace('EA_LBS_', 'EA_EL_', (string) $row->job_request_id);
            if ($newRequestId === (string) $row->job_request_id) {
                continue;
            }

            $exists = DB::table('job_requests')
                ->where('client_code', 'EL01')
                ->where('job_request_id', $newRequestId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('job_requests')->insert([
                'id'               => $nextId++,
                'client_code'      => 'EL01',
                'job_request_id'   => $newRequestId,
                'job_request_type' => $row->job_request_type,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_requests')) {
            return;
        }

        DB::table('job_requests')
            ->where('client_code', 'EL01')
            ->whereRaw("job_request_id LIKE 'EA\\_EL\\_%'")
            ->delete();
    }
};
