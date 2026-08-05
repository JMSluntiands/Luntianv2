<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_status_transitions')) {
            Schema::create('job_status_transitions', function (Blueprint $table) {
                $table->id();
                $table->string('source_table', 64);
                $table->unsignedBigInteger('job_id');
                $table->string('from_status', 80)->nullable();
                $table->string('to_status', 80);
                $table->timestamp('changed_at');
                $table->string('changed_by', 120)->nullable();
                $table->index(['source_table', 'job_id', 'changed_at'], 'jst_source_job_changed_idx');
            });
        }

        if (Schema::hasTable('statuses')) {
            $exists = DB::table('statuses')
                ->whereRaw('LOWER(TRIM(name)) = ?', ['on hold'])
                ->exists();
            if (! $exists) {
                $row = [
                    'name' => 'On Hold',
                    'color' => '#f59e0b',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('statuses', 'font_color')) {
                    $row['font_color'] = '#ffffff';
                }
                DB::table('statuses')->insert($row);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_status_transitions');

        if (Schema::hasTable('statuses')) {
            DB::table('statuses')->whereRaw('LOWER(TRIM(name)) = ?', ['on hold'])->delete();
        }
    }
};
