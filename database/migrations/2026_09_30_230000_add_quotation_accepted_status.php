<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('statuses')->where('name', 'Quotation Accepted')->exists()) {
            return;
        }

        $row = [
            'name' => 'Quotation Accepted',
            'color' => '#2563eb',
            'font_color' => '#ffffff',
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
        DB::table('statuses')->where('name', 'Quotation Accepted')->delete();
    }
};
