<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('standalone_form_settings')) {
            return;
        }

        if (! Schema::hasColumn('standalone_form_settings', 'is_visible')) {
            Schema::table('standalone_form_settings', function (Blueprint $table) {
                $table->boolean('is_visible')->default(true)->after('is_required');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('standalone_form_settings') || ! Schema::hasColumn('standalone_form_settings', 'is_visible')) {
            return;
        }

        Schema::table('standalone_form_settings', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
};
