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
        $statuses = [
            ['name' => 'For Email Confirmation', 'color' => '#6366f1'],
            ['name' => 'Cancelled', 'color' => '#64748b'],
        ];

        foreach ($statuses as $status) {
            $exists = DB::table('statuses')->where('name', $status['name'])->exists();
            if (!$exists) {
                DB::table('statuses')->insert([
                    'name' => $status['name'],
                    'color' => $status['color'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('statuses')->whereIn('name', ['For Email Confirmation', 'Cancelled'])->delete();
    }
};
