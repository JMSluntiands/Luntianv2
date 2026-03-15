<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('statuses')->where('name', 'Processing')->exists();
        if (!$exists) {
            DB::table('statuses')->insert([
                'name' => 'Processing',
                'color' => '#ec4899',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('statuses')->where('name', 'Processing')->delete();
    }
};
