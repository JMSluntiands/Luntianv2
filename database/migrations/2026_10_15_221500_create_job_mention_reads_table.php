<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_mention_reads')) {
            Schema::create('job_mention_reads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('family', 16);
                $table->unsignedBigInteger('job_id');
                $table->string('tagged_code', 32);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'family', 'job_id', 'tagged_code'], 'job_mention_reads_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_mention_reads');
    }
};
