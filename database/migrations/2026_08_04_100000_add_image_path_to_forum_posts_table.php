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
            if (! Schema::hasColumn('forum_posts', 'image_path')) {
                $table->string('image_path', 500)->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('forum_posts')) {
            return;
        }

        Schema::table('forum_posts', function (Blueprint $table) {
            if (Schema::hasColumn('forum_posts', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
