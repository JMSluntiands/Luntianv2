<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BPH pipeline jobs (legacy-compatible shape).
     */
    public function up(): void
    {
        if (Schema::hasTable('job_bph')) {
            return;
        }

        Schema::create('job_bph', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50);
            $table->string('client_code', 50);
            $table->string('urgent', 10)->default('NO');
            $table->string('job_type', 100);
            $table->string('ncc', 255)->default('2019');
            $table->string('job_number', 6);
            $table->string('client_name', 255);
            $table->string('contact_email', 255);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('assigned', 50)->nullable();
            $table->string('checked', 50)->nullable();
            $table->longText('plans_files')->nullable();
            $table->longText('docs_files')->nullable();
            $table->string('status', 50)->default('Allocated');
            $table->date('date')->nullable();
            $table->text('address')->nullable();
            $table->string('climate_zone', 100)->nullable();
            $table->text('compliance_summary_description')->nullable();
            $table->string('spec_client_no', 100)->nullable();
            $table->string('spec_lbs_no', 100)->nullable();
            $table->text('spec_plans')->nullable();
            $table->text('spec_insulation')->nullable();
            $table->text('spec_glazing')->nullable();
            $table->text('spec_sealing')->nullable();
            $table->text('spec_services')->nullable();
            $table->text('spec_additional')->nullable();
            $table->integer('units')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_bph');
    }
};
