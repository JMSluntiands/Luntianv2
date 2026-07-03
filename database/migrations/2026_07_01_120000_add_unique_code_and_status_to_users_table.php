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
            if (! Schema::hasColumn('users', 'unique_code')) {
                $table->string('unique_code', 50)->nullable()->after('id');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 50)->nullable()->after('task');
            }
        });

        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'unique_code')) {
            return;
        }

        if (Schema::hasTable('staff')) {
            DB::statement("
                UPDATE users u
                INNER JOIN staff s ON s.username COLLATE utf8mb4_unicode_ci = u.username
                SET u.unique_code = s.staff_id
                WHERE u.unique_code IS NULL
                  AND s.staff_id IS NOT NULL
                  AND TRIM(s.staff_id) != ''
            ");
        }

        if (Schema::hasTable('checker')) {
            DB::statement("
                UPDATE users u
                INNER JOIN checker c ON c.username COLLATE utf8mb4_unicode_ci = u.username
                SET u.unique_code = c.checker_id
                WHERE u.unique_code IS NULL
                  AND c.checker_id IS NOT NULL
                  AND TRIM(c.checker_id) != ''
            ");
        }

        if (Schema::hasTable('user_logins')) {
            DB::statement("
                UPDATE users u
                INNER JOIN user_logins ul ON ul.username COLLATE utf8mb4_unicode_ci = u.username
                SET u.unique_code = ul.unique_code
                WHERE u.unique_code IS NULL
                  AND ul.unique_code IS NOT NULL
                  AND TRIM(ul.unique_code) != ''
            ");
        }

        DB::table('users')
            ->whereNull('unique_code')
            ->whereNotNull('username')
            ->whereRaw('TRIM(username) != ?', [''])
            ->update(['unique_code' => DB::raw('UPPER(TRIM(username))')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('users', 'unique_code')) {
                $table->dropColumn('unique_code');
            }
        });
    }
};
