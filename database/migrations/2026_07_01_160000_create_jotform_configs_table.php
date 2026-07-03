<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jotform_configs')) {
            return;
        }

        Schema::create('jotform_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('webhook_secret', 64)->nullable();
            $table->string('jotform_form_id', 50)->nullable();
            $table->boolean('queue_in_forms_submitted')->default(true);
            $table->unsignedBigInteger('default_compliance_id')->nullable();
            $table->unsignedBigInteger('default_client_account_id')->nullable();
            $table->unsignedBigInteger('default_priority_id')->nullable();
            $table->unsignedBigInteger('default_job_request_id')->nullable();
            $table->string('default_assigned_to', 50)->nullable();
            $table->string('default_checked_by', 50)->nullable();
            $table->string('map_reference_no', 120)->nullable();
            $table->string('map_client_reference', 120)->nullable();
            $table->string('map_job_address', 120)->nullable();
            $table->string('map_notes', 120)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jotform_configs');
    }
};
