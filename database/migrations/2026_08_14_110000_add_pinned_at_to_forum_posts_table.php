<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_posts')) {
            return;
        }

        if (! Schema::hasColumn('forum_posts', 'pinned_at')) {
            Schema::table('forum_posts', function (Blueprint $table) {
                $table->timestamp('pinned_at')->nullable()->index()->after('post_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('forum_posts') && Schema::hasColumn('forum_posts', 'pinned_at')) {
            Schema::table('forum_posts', function (Blueprint $table) {
                $table->dropColumn('pinned_at');
            });
        }
    }
};
