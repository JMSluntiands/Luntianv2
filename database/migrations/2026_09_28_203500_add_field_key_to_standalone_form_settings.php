<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('standalone_form_settings')) {
            return;
        }

        if (! Schema::hasColumn('standalone_form_settings', 'field_key')) {
            Schema::table('standalone_form_settings', function (Blueprint $table) {
                $table->string('field_key', 80)->nullable()->after('form_key');
            });
        }

        DB::table('standalone_form_settings')->delete();

        Schema::table('standalone_form_settings', function (Blueprint $table) {
            $table->dropUnique('standalone_form_settings_form_key_unique');
        });

        Schema::table('standalone_form_settings', function (Blueprint $table) {
            $table->unique(['form_key', 'field_key']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('standalone_form_settings')) {
            return;
        }

        Schema::table('standalone_form_settings', function (Blueprint $table) {
            $table->dropUnique(['form_key', 'field_key']);
        });

        if (Schema::hasColumn('standalone_form_settings', 'field_key')) {
            Schema::table('standalone_form_settings', function (Blueprint $table) {
                $table->dropColumn('field_key');
            });
        }

        Schema::table('standalone_form_settings', function (Blueprint $table) {
            $table->unique('form_key');
        });
    }
};
