<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Dedicated job tables for A&M and Fyrs Energy Wise (previously rows in `job_bph`).
     * Copies matching rows, moves on-disk folders from `bph-documents` when present, then removes legacy rows.
     */
    public function up(): void
    {
        foreach (['job_amt', 'job_fyrs'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                continue;
            }
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('reference', 50);
                $table->string('client_code', 50);
                $table->string('urgent', 10)->default('NO');
                $table->string('job_type', 100);
                $table->string('ncc', 255)->default('2019');
                $table->string('job_number', 6);
                $table->string('client_name', 255);
                $table->string('contact_email', 255);
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->string('assigned', 50)->nullable();
                $table->string('checked', 50)->nullable();
                $table->longText('plans_files')->nullable();
                $table->longText('docs_files')->nullable();
                $table->string('status', 50)->default('Allocated');
                $table->date('date')->nullable();
                $table->text('address')->nullable();
                $table->string('climate_zone', 100)->nullable();
                $table->text('compliance_summary_description')->nullable();
                $table->string('spec_client_no', 100)->nullable();
                $table->string('spec_lbs_no', 100)->nullable();
                $table->text('spec_plans')->nullable();
                $table->text('spec_insulation')->nullable();
                $table->text('spec_glazing')->nullable();
                $table->text('spec_sealing')->nullable();
                $table->text('spec_services')->nullable();
                $table->text('spec_additional')->nullable();
                $table->string('spec_print_merge_file', 255)->nullable();
                $table->integer('units')->default(0);
            });
        }

        if (! Schema::hasTable('job_bph')) {
            return;
        }

        $this->migrateClientRows('amt01', 'job_amt', 'amt-documents');
        $this->migrateClientRows('fyrs01', 'job_fyrs', 'fyrs-documents');
    }

    public function down(): void
    {
        if (Schema::hasTable('job_bph')) {
            if (Schema::hasTable('job_amt')) {
                foreach (DB::table('job_amt')->orderBy('id')->get() as $row) {
                    DB::table('job_bph')->insert((array) $row);
                }
            }
            if (Schema::hasTable('job_fyrs')) {
                foreach (DB::table('job_fyrs')->orderBy('id')->get() as $row) {
                    DB::table('job_bph')->insert((array) $row);
                }
            }
        }

        Schema::dropIfExists('job_fyrs');
        Schema::dropIfExists('job_amt');
    }

    private function migrateClientRows(string $clientNorm, string $targetTable, string $destStorageBase): void
    {
        $ids = DB::table('job_bph')->whereRaw('LOWER(TRIM(client_code)) = ?', [$clientNorm])->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }
        $rows = DB::table('job_bph')->whereIn('id', $ids)->get();
        foreach ($rows as $row) {
            $payload = (array) $row;
            if (! array_key_exists('spec_print_merge_file', $payload) && Schema::hasColumn($targetTable, 'spec_print_merge_file')) {
                $payload['spec_print_merge_file'] = null;
            }
            DB::table($targetTable)->insert($payload);
            $this->copyJobFolderToStorage($row, $destStorageBase);
        }
        DB::table('job_bph')->whereIn('id', $ids)->delete();
    }

    private function copyJobFolderToStorage(object $row, string $destStorageBase): void
    {
        $id = (int) ($row->id ?? 0);
        $ref = (string) ($row->reference ?? '');
        $seg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $ref);
        if ($seg === '') {
            $seg = 'job_'.$id;
        }
        $fromRel = 'bph-documents/'.$seg;
        $toRel = $destStorageBase.'/'.$seg;
        $disk = Storage::disk('local');
        if (! $disk->exists($fromRel)) {
            return;
        }
        if ($disk->exists($toRel)) {
            return;
        }
        File::ensureDirectoryExists(dirname($disk->path($toRel)));
        File::copyDirectory($disk->path($fromRel), $disk->path($toRel));
    }
};
