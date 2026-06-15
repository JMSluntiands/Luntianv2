<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure Luntian job types resolve for the add form: LT01 client + normalize client_code.
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

        // Admin may have saved rows as client_code "Luntian" — normalize to LT01 when that client exists.
        if (Schema::hasTable('clients') && DB::table('clients')->where('client_code', 'LT01')->exists()) {
            DB::table('job_requests')
                ->whereRaw('LOWER(TRIM(client_code)) = ?', ['luntian'])
                ->where('client_code', '!=', 'LT01')
                ->update(['client_code' => 'LT01']);
        }
    }

    public function down(): void
    {
        // Non-destructive data normalization; no rollback.
    }
};
