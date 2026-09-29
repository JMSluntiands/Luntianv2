<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_general_assembly') || Schema::hasColumn('job_general_assembly', 'client_email')) {
            return;
        }

        Schema::table('job_general_assembly', function (Blueprint $table) {
            $table->string('client_email', 255)->nullable()->after('client_reference_no');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_general_assembly') || ! Schema::hasColumn('job_general_assembly', 'client_email')) {
            return;
        }

        Schema::table('job_general_assembly', function (Blueprint $table) {
            $table->dropColumn('client_email');
        });
    }
};
