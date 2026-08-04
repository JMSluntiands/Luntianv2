<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('attendances')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('attendances', 'clocked_out_at')) {
                $table->dateTime('clocked_out_at')->nullable()->after('clocked_in_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('attendances') || ! Schema::hasColumn('attendances', 'clocked_out_at')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('clocked_out_at');
        });
    }
};
