<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'leave_credits')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedSmallInteger('leave_credits')->default(15)->after('status');
            });
        }

        if (! Schema::hasTable('leave_days')) {
            Schema::create('leave_days', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('leave_date')->index();
                $table->string('leave_type', 50)->default('leave');
                $table->string('status', 20)->default('approved')->index();
                $table->timestamps();

                $table->unique(['user_id', 'leave_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_days');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'leave_credits')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('leave_credits');
            });
        }
    }
};
