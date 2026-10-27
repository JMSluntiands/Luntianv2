<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('forum_comments') || Schema::hasColumn('forum_comments', 'parent_id')) {
            return;
        }

        Schema::table('forum_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->index()->after('user_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('forum_comments') && Schema::hasColumn('forum_comments', 'parent_id')) {
            Schema::table('forum_comments', function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }
    }
};
