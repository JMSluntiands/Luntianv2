<?php

use App\Models\JobRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Luntian add form needs job_requests rows (LT01 / Luntian / EA_LT_*).
 * Re-run safe: seeds from LBS01 only when no Luntian vertical types exist yet.
 */
return new class extends Migration
{
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

        if (Schema::hasTable('job_requests') && Schema::hasTable('clients') && DB::table('clients')->where('client_code', 'LT01')->exists()) {
            DB::table('job_requests')
                ->whereRaw('LOWER(TRIM(client_code)) = ?', ['luntian'])
                ->where('client_code', '!=', 'LT01')
                ->update(['client_code' => 'LT01']);
        }

        JobRequest::seedLuntianTypesFromLbsIfMissing();
    }

    public function down(): void
    {
        // Non-destructive seed; no rollback.
    }
};
