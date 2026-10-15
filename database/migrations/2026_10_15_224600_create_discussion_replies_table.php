<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('discussion_replies')) {
            Schema::create('discussion_replies', function (Blueprint $table) {
                $table->id();
                $table->string('source', 40);
                $table->unsignedBigInteger('parent_id');
                $table->unsignedBigInteger('job_id');
                $table->string('author_name', 120);
                $table->text('message');
                $table->string('created_at', 50)->nullable();
                $table->index(['source', 'parent_id']);
                $table->index('job_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
    }
};
