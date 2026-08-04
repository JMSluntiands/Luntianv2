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

        Schema::table('forum_posts', function (Blueprint $table) {
            if (! Schema::hasColumn('forum_posts', 'post_type')) {
                $table->string('post_type', 20)->default('discussion')->after('body')->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('forum_posts') || ! Schema::hasColumn('forum_posts', 'post_type')) {
            return;
        }

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn('post_type');
        });
    }
};
