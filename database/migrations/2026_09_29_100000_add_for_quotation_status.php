<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('statuses')->where('name', 'For Quotation')->exists();
        if ($exists) {
            return;
        }

        DB::table('statuses')->insert([
            'name' => 'For Quotation',
            'color' => '#f59e0b',
            'font_color' => '#333333',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('statuses')->where('name', 'For Quotation')->delete();
    }
};
