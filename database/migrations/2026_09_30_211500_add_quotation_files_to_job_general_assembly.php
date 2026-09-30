<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_general_assembly')) {
            return;
        }
        if (Schema::hasColumn('job_general_assembly', 'quotation_files')) {
            return;
        }

        Schema::table('job_general_assembly', function (Blueprint $table) {
            $table->longText('quotation_files')->nullable()->after('upload_project_files');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_general_assembly')) {
            return;
        }
        if (! Schema::hasColumn('job_general_assembly', 'quotation_files')) {
            return;
        }

        Schema::table('job_general_assembly', function (Blueprint $table) {
            $table->dropColumn('quotation_files');
        });
    }
};
