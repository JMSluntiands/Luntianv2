<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * General Assembly jobs — same shape as legacy `jobs` (LBS pipeline).
     */
    public function up(): void
    {
        if (Schema::hasTable('job_general_assembly')) {
            return;
        }

        Schema::create('job_general_assembly', function (Blueprint $table) {
            $table->increments('job_id');
            $table->string('reference', 255);
            $table->string('log_date', 50);
            $table->string('client_code', 10);
            $table->string('job_reference_no', 50);
            $table->string('client_reference_no', 50)->nullable();
            $table->string('staff_id', 10)->nullable();
            $table->string('checker_id', 10)->nullable();
            $table->string('ncc_compliance', 50)->nullable();
            $table->string('job_request_id', 50);
            $table->string('address_client', 255)->nullable();
            $table->string('job_type', 100)->nullable();
            $table->string('priority', 50);
            $table->string('plan_complexity', 255)->nullable();
            $table->text('notes')->nullable();
            $table->longText('upload_files')->nullable();
            $table->longText('upload_project_files')->nullable();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->string('updated_by', 50)->nullable();
            $table->string('job_status', 255);
            $table->string('dwelling', 255)->default('');
            $table->unsignedInteger('client_account_id')->nullable();
            $table->dateTime('completion_date')->nullable();
            $table->integer('units')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_general_assembly');
    }
};
