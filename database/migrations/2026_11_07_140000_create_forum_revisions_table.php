<?php

use App\Models\ForumRevision;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        ForumRevision::ensureTable();
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_revisions');
    }
};
