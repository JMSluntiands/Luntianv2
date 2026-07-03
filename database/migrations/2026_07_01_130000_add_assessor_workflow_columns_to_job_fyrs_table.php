<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_fyrs')) {
            return;
        }

        Schema::table('job_fyrs', function (Blueprint $table) {
            if (! Schema::hasColumn('job_fyrs', 'estate')) {
                $table->string('estate', 255)->nullable()->after('address');
            }
            if (! Schema::hasColumn('job_fyrs', 'house_type')) {
                $table->string('house_type', 255)->nullable()->after('estate');
            }
            if (! Schema::hasColumn('job_fyrs', 'facade')) {
                $table->string('facade', 255)->nullable()->after('house_type');
            }
            if (! Schema::hasColumn('job_fyrs', 'garage')) {
                $table->string('garage', 50)->nullable()->after('facade');
            }
            if (! Schema::hasColumn('job_fyrs', 'tasks')) {
                $table->json('tasks')->nullable()->after('garage');
            }
            if (! Schema::hasColumn('job_fyrs', 'stage')) {
                $table->string('stage', 100)->nullable()->after('tasks');
            }
            if (! Schema::hasColumn('job_fyrs', 'basix_number')) {
                $table->string('basix_number', 100)->nullable()->after('climate_zone');
            }
            if (! Schema::hasColumn('job_fyrs', 'storeys')) {
                $table->string('storeys', 20)->nullable()->after('basix_number');
            }
            if (! Schema::hasColumn('job_fyrs', 'due_date')) {
                $table->date('due_date')->nullable()->after('storeys');
            }
            if (! Schema::hasColumn('job_fyrs', 'est_completion_certification')) {
                $table->date('est_completion_certification')->nullable()->after('due_date');
            }
            if (! Schema::hasColumn('job_fyrs', 'est_completion_basix')) {
                $table->date('est_completion_basix')->nullable()->after('est_completion_certification');
            }
            if (! Schema::hasColumn('job_fyrs', 'feedback_bers')) {
                $table->string('feedback_bers', 500)->nullable()->after('est_completion_basix');
            }
            if (! Schema::hasColumn('job_fyrs', 'feedback_basix')) {
                $table->string('feedback_basix', 500)->nullable()->after('feedback_bers');
            }
            if (! Schema::hasColumn('job_fyrs', 'feedback_commitments_form')) {
                $table->string('feedback_commitments_form', 500)->nullable()->after('feedback_basix');
            }
            if (! Schema::hasColumn('job_fyrs', 'basix_note')) {
                $table->text('basix_note')->nullable()->after('feedback_commitments_form');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_fyrs')) {
            return;
        }

        Schema::table('job_fyrs', function (Blueprint $table) {
            foreach ([
                'estate',
                'house_type',
                'facade',
                'garage',
                'tasks',
                'stage',
                'basix_number',
                'storeys',
                'due_date',
                'est_completion_certification',
                'est_completion_basix',
                'feedback_bers',
                'feedback_basix',
                'feedback_commitments_form',
                'basix_note',
            ] as $column) {
                if (Schema::hasColumn('job_fyrs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
