<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jotform_configs')) {
            return;
        }

        Schema::table('jotform_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('jotform_configs', 'map_upload_plans')) {
                $table->string('map_upload_plans', 120)->nullable()->after('map_notes');
            }
            if (! Schema::hasColumn('jotform_configs', 'map_upload_documents')) {
                $table->string('map_upload_documents', 120)->nullable()->after('map_upload_plans');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('jotform_configs')) {
            return;
        }

        Schema::table('jotform_configs', function (Blueprint $table) {
            if (Schema::hasColumn('jotform_configs', 'map_upload_documents')) {
                $table->dropColumn('map_upload_documents');
            }
            if (Schema::hasColumn('jotform_configs', 'map_upload_plans')) {
                $table->dropColumn('map_upload_plans');
            }
        });
    }
};
