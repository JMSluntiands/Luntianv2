<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'add_job_staff_modules')) {
                $table->json('add_job_staff_modules')->nullable()->after('branch');
            }
            if (! Schema::hasColumn('users', 'add_job_checker_modules')) {
                $table->json('add_job_checker_modules')->nullable()->after('add_job_staff_modules');
            }
        });

        if (Schema::hasColumn('users', 'add_job_modules')) {
            DB::table('users')
                ->whereNotNull('add_job_modules')
                ->orderBy('id')
                ->chunkById(100, function ($users) {
                    foreach ($users as $user) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                'add_job_staff_modules' => $user->add_job_modules,
                                'add_job_checker_modules' => $user->add_job_modules,
                            ]);
                    }
                });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('add_job_modules');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'add_job_modules')) {
                $table->json('add_job_modules')->nullable()->after('branch');
            }
        });

        if (Schema::hasColumn('users', 'add_job_staff_modules')) {
            DB::table('users')
                ->where(function ($q) {
                    $q->whereNotNull('add_job_staff_modules')
                        ->orWhereNotNull('add_job_checker_modules');
                })
                ->orderBy('id')
                ->chunkById(100, function ($users) {
                    foreach ($users as $user) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                'add_job_modules' => $user->add_job_staff_modules ?? $user->add_job_checker_modules,
                            ]);
                    }
                });
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'add_job_checker_modules')) {
                $table->dropColumn('add_job_checker_modules');
            }
            if (Schema::hasColumn('users', 'add_job_staff_modules')) {
                $table->dropColumn('add_job_staff_modules');
            }
        });
    }
};
