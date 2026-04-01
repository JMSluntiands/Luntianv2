<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bph_staff_uploaded_files')) {
            Schema::create('bph_staff_uploaded_files', function (Blueprint $table) {
                $table->id('file_id');
                $table->unsignedBigInteger('job_id');
                $table->longText('files_json')->nullable();
                $table->text('comment')->nullable();
                $table->string('uploaded_at', 50)->nullable();
                $table->string('uploaded_by', 100)->nullable();
            });
        }

        if (!Schema::hasTable('bph_run_comments')) {
            Schema::create('bph_run_comments', function (Blueprint $table) {
                $table->id('run_comment_id');
                $table->unsignedBigInteger('job_id');
                $table->string('name', 100)->nullable();
                $table->text('message');
                $table->string('created_at', 50)->nullable();
            });
        }

        if (!Schema::hasTable('bph_comments')) {
            Schema::create('bph_comments', function (Blueprint $table) {
                $table->id('comment_id');
                $table->unsignedBigInteger('job_id');
                $table->string('username', 100)->nullable();
                $table->text('message');
                $table->string('created_at', 50)->nullable();
            });
        }

        if (!Schema::hasTable('bph_activity_logs')) {
            Schema::create('bph_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->dateTime('activity_date')->nullable();
                $table->string('activity_type', 100)->nullable();
                $table->text('activity_description')->nullable();
                $table->string('updated_by', 100)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bph_activity_logs');
        Schema::dropIfExists('bph_comments');
        Schema::dropIfExists('bph_run_comments');
        Schema::dropIfExists('bph_staff_uploaded_files');
    }
};

