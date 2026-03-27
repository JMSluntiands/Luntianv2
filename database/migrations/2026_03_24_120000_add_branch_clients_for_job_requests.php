<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add client rows for branches used in Job Request (create/edit) dropdown.
     */
    public function up(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        $rows = [
            ['client_code' => 'EL01', 'client_name' => 'Efficient Living Account', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'CSP01', 'client_name' => 'CSP Account', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'NH01', 'client_name' => 'NH Account', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'LCB01', 'client_name' => 'LC Home Builder Account', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'LE01', 'client_name' => 'Leading Energy Account', 'client_email' => 'admin@luntiands.com'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('clients')->where('client_code', $row['client_code'])->exists();
            if (! $exists) {
                DB::table('clients')->insert($row);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        DB::table('clients')->whereIn('client_code', ['EL01', 'CSP01', 'NH01', 'LCB01', 'LE01'])->delete();
    }
};
