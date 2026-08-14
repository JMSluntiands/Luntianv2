<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_requests')) {
            Schema::create('job_requests', function (Blueprint $table) {
                $table->id();
                $table->string('client_code', 20)->index();
                $table->string('job_request_id', 50)->unique();
                $table->string('job_request_type');
            });
        }

        $this->ensureClientsExist();
        $this->seedLegacyRows();
        $this->mirrorFromLbs('EL01', 'EA_EL_');
        $this->mirrorFromLbs('LT01', 'EA_LT_');
        $this->mirrorFromLbs('GA01', 'EA_GA_');
    }

    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }

    private function ensureClientsExist(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        $clients = [
            ['client_code' => 'LBS01', 'client_name' => 'LBS', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'BPH01', 'client_name' => 'BPH', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'B1001', 'client_name' => 'B1', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'BLQ01', 'client_name' => 'BLUINQ', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'EL01', 'client_name' => 'Efficient Living Account', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'LT01', 'client_name' => 'Luntian Account', 'client_email' => 'admin@luntiands.com'],
            ['client_code' => 'GA01', 'client_name' => 'Generic Assessment Account', 'client_email' => 'admin@generalassembly.com'],
        ];

        foreach ($clients as $row) {
            if (! DB::table('clients')->where('client_code', $row['client_code'])->exists()) {
                DB::table('clients')->insert($row);
            }
        }
    }

    private function seedLegacyRows(): void
    {
        $rows = [
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_1SNatHERS', 'job_request_type' => '1S NatHERS Base Model - 1S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_2SNatHERS', 'job_request_type' => '2S NatHERS Base Model - 2S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_3SNatHERS', 'job_request_type' => '3S NatHERS Base Model - 2S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_1SDB', 'job_request_type' => '1S DB Base Model- 1S Design Builder Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_2SDB', 'job_request_type' => '2S DB Base Model- 2S Design Builder Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_3SDB', 'job_request_type' => '3S DB Base Model- 3S Design Builder Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_1SLoft', 'job_request_type' => '1S Loft Base Mode - 1S Loft FR5 Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_1SDB+FR5', 'job_request_type' => '1S DB + Base Model - 1S Design Builder + FR5 Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_2SDB+FR5', 'job_request_type' => '2S DB + Base Model - 2S Design Builder + FR5 Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_3SDB+FR5', 'job_request_type' => '3S DB + Base Model - 3S Design Builder + FR5 Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_C2_SINGLE', 'job_request_type' => 'Class 2 Unit (Single)'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_C2_BATCH', 'job_request_type' => 'Class 2 Unit (Batch)'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_SD3', 'job_request_type' => 'Shading Diagram (3)'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_SD6', 'job_request_type' => 'Shading Diagram (6)'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_1SPrelim', 'job_request_type' => '1S NatHERS Prelim EA - 1S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_2SPrelim', 'job_request_type' => '2S NatHERS Prelim EA - 2S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_3SPrelim', 'job_request_type' => '3S NatHERS Prelim EA -3S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_1SCert', 'job_request_type' => '1S NatHERS EA Cert - 1S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_2SCert', 'job_request_type' => '2S NatHERS EA Cert - 2S NatHERS Model'],
            ['client_code' => 'LBS01', 'job_request_id' => 'EA_LBS_3SCert', 'job_request_type' => '3S NatHERS EA Cert - 3S NatHERS Model'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_Query', 'job_request_type' => 'Just a query'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_1SPrelim', 'job_request_type' => '1S Prelim EA'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_1SPrelimRev', 'job_request_type' => '1S Prelim EA Revision'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_DKPrelim', 'job_request_type' => 'Dual Key Prelim EA'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_1SCert', 'job_request_type' => '1S NatHERS EA Cert'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_FeasibEA', 'job_request_type' => 'Feasibility EA'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_DR', 'job_request_type' => 'Display Review'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_TempRev', 'job_request_type' => 'Template Revision'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_1SEACert', 'job_request_type' => 'EA Certification'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_1SEACertRev', 'job_request_type' => 'EA Certification Revision'],
            ['client_code' => 'B1001', 'job_request_id' => 'EA_B1_1SPerfSol', 'job_request_type' => 'Performance Solution Report'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_Query', 'job_request_type' => 'Just a query'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_1SPrelim', 'job_request_type' => 'Working Dwgs EA'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_1SPrelimRev', 'job_request_type' => 'Working Dwgs EA Revision'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_BALReport', 'job_request_type' => 'Jobs with added BAL Report'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_FeasibEA', 'job_request_type' => 'Feasibility Energy Assessment'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_1SEACert', 'job_request_type' => 'EA Certification'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_1SEACertRev', 'job_request_type' => 'EA Certification Revision'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_BALAssess', 'job_request_type' => 'BAL Site Assessment'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_CompCert', 'job_request_type' => 'BAL Compliance Certificate'],
            ['client_code' => 'BPH01', 'job_request_id' => 'EA_BPH_1SPerfSol', 'job_request_type' => 'Performance Solution Report'],
            ['client_code' => 'BLQ01', 'job_request_id' => 'EA_BLUINQ_1SPerfSol', 'job_request_type' => '1S Performance Solution Report'],
            ['client_code' => 'BLQ01', 'job_request_id' => 'EA_BLUINQ_1SPrelim+Cert', 'job_request_type' => 'Prelim EA + Certification'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('job_requests')->where('job_request_id', $row['job_request_id'])->exists();
            if (! $exists) {
                DB::table('job_requests')->insert($row);
            }
        }
    }

    private function mirrorFromLbs(string $clientCode, string $idPrefix): void
    {
        $lbsRows = DB::table('job_requests')->where('client_code', 'LBS01')->get();

        foreach ($lbsRows as $row) {
            $newRequestId = str_replace('EA_LBS_', $idPrefix, (string) $row->job_request_id);
            if ($newRequestId === (string) $row->job_request_id) {
                continue;
            }

            $exists = DB::table('job_requests')
                ->where('job_request_id', $newRequestId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('job_requests')->insert([
                'client_code' => $clientCode,
                'job_request_id' => $newRequestId,
                'job_request_type' => $row->job_request_type,
            ]);
        }
    }
};
