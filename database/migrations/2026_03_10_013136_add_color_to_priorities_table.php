<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('priorities', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('name')->comment('Hex color e.g. #ff0000');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('priorities', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
