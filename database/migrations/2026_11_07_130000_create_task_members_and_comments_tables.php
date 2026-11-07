<?php

use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Task::ensureMembersTable();
        Task::ensureCommentTables();
    }

    public function down(): void
    {
        Schema::dropIfExists('task_comment_notifications');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_members');
    }
};
