<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('job_lc_home_builder')) {
            return;
        }

        Schema::create('job_lc_home_builder', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('reference', 50)->index();
            $table->string('client_code', 20)->nullable()->index();
            $table->string('urgent', 3)->nullable()->index();
            $table->string('job_type', 100)->nullable();
            $table->string('ncc', 255)->nullable();
            $table->string('job_number', 6)->index();
            $table->string('client_name');
            $table->string('contact_email');
            $table->text('notes')->nullable();
            $table->text('plans_files')->nullable();
            $table->text('docs_files')->nullable();
            $table->string('status', 50)->default('Allocated')->index();
            $table->date('date')->nullable()->index();
            $table->string('assigned', 50)->nullable()->index();
            $table->string('checked', 50)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_lc_home_builder');
    }
};

