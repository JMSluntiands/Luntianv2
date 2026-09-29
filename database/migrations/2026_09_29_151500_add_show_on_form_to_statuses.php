<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('statuses', 'show_on_form')) {
            Schema::table('statuses', function (Blueprint $table) {
                $table->boolean('show_on_form')->default(false)->after('font_color');
            });
        }

        DB::table('statuses')
            ->whereIn('name', ['For Inquiries', 'For Quotation'])
            ->update(['show_on_form' => true]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('statuses', 'show_on_form')) {
            Schema::table('statuses', function (Blueprint $table) {
                $table->dropColumn('show_on_form');
            });
        }
    }
};
