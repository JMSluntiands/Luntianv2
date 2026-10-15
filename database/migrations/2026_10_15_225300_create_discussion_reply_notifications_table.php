<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('discussion_reply_notifications')) {
            Schema::create('discussion_reply_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('reply_id');
                $table->unsignedBigInteger('job_id');
                $table->string('target_url', 500)->nullable();
                $table->string('created_at', 50)->nullable();
                $table->timestamp('read_at')->nullable();
                $table->unique(['user_id', 'reply_id']);
                $table->index(['user_id', 'read_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_reply_notifications');
    }
};
