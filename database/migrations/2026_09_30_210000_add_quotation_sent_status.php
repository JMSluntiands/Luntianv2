<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('statuses')->where('name', 'Quotation Sent')->exists()) {
            return;
        }

        $row = [
            'name' => 'Quotation Sent',
            'color' => '#10b981',
            'font_color' => '#064e3b',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (Schema::hasColumn('statuses', 'show_on_form')) {
            $row['show_on_form'] = false;
        }

        DB::table('statuses')->insert($row);
    }

    public function down(): void
    {
        DB::table('statuses')->where('name', 'Quotation Sent')->delete();
    }
};
