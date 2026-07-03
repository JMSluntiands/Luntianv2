<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * NatHERS & BASIX assessor workflow (Excel columns) — separate from legacy job_fyrs.
     */
    public function up(): void
    {
        if (Schema::hasTable('fyrs_assessor_jobs')) {
            return;
        }

        Schema::create('fyrs_assessor_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_fyrs_id')->nullable()->index();
            $table->string('reference', 50)->nullable();
            $table->date('job_date')->nullable();
            $table->string('job_number', 100);
            $table->string('estate', 255)->nullable();
            $table->string('house_type', 255)->nullable();
            $table->string('facade', 255)->nullable();
            $table->string('garage', 50)->nullable();
            $table->json('tasks')->nullable();
            $table->text('notes')->nullable();
            $table->string('stage', 100)->nullable();
            $table->string('climate_zone', 100)->nullable();
            $table->string('basix_number', 100)->nullable();
            $table->string('storeys', 20)->nullable();
            $table->date('due_date')->nullable();
            $table->date('est_completion_certification')->nullable();
            $table->date('est_completion_basix')->nullable();
            $table->string('feedback_bers', 500)->nullable();
            $table->string('feedback_basix', 500)->nullable();
            $table->string('feedback_commitments_form', 500)->nullable();
            $table->text('basix_note')->nullable();
            $table->string('status', 50)->default('Allocated');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fyrs_assessor_jobs');
    }
};
