<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('statuses')->where('name', 'For Inquiries')->exists()) {
            return;
        }

        DB::table('statuses')->insert([
            'name' => 'For Inquiries',
            'color' => '#06b6d4',
            'font_color' => '#083344',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('statuses')->where('name', 'For Inquiries')->delete();
    }
};
