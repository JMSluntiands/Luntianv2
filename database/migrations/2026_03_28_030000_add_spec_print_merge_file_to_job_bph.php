<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_bph')) {
            return;
        }
        if (!Schema::hasColumn('job_bph', 'spec_print_merge_file')) {
            Schema::table('job_bph', function (Blueprint $table) {
                $table->string('spec_print_merge_file', 255)->nullable()->after('spec_additional');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_bph') && Schema::hasColumn('job_bph', 'spec_print_merge_file')) {
            Schema::table('job_bph', function (Blueprint $table) {
                $table->dropColumn('spec_print_merge_file');
            });
        }
    }
};
