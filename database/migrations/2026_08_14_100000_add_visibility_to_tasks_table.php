<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        if (! Schema::hasColumn('tasks', 'visibility')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('visibility', 20)->default('public')->index()->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'visibility')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('visibility');
            });
        }
    }
};
