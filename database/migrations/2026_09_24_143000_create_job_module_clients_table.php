<?php

use App\Models\JobModuleClient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_module_clients')) {
            Schema::create('job_module_clients', function (Blueprint $table) {
                $table->id();
                $table->string('module', 50)->unique();
                $table->string('client_code', 10);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('clients')) {
            foreach (JobModuleClient::defaultCodes() as $module => $code) {
                $resolved = JobModuleClient::defaultCodeFor($module);
                if ($resolved === '') {
                    continue;
                }
                if (! DB::table('clients')->whereRaw('UPPER(TRIM(client_code)) = ?', [strtoupper($resolved)])->exists()) {
                    continue;
                }
                $exists = DB::table('job_module_clients')->where('module', $module)->exists();
                if ($exists) {
                    continue;
                }
                DB::table('job_module_clients')->insert([
                    'module' => $module,
                    'client_code' => $resolved,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->seedPermissionFromJobRequest('job_module_client.index');
        $this->seedPermissionFromJobRequest('job_module_client.update');
    }

    public function down(): void
    {
        Schema::dropIfExists('job_module_clients');
        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->whereIn('route_name', [
                'job_module_client.index',
                'job_module_client.update',
            ])->delete();
        }
        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('route_name', [
                'job_module_client.index',
                'job_module_client.update',
            ])->delete();
        }
    }

    private function seedPermissionFromJobRequest(string $newRoute): void
    {
        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')->where('route_name', 'job_request.index')->get();
            foreach ($rows as $row) {
                $exists = DB::table('role_permissions')
                    ->where('role', $row->role)
                    ->where('branch', $row->branch ?? '')
                    ->where('route_name', $newRoute)
                    ->exists();
                if ($exists) {
                    continue;
                }
                DB::table('role_permissions')->insert([
                    'role' => $row->role,
                    'branch' => $row->branch ?? '',
                    'route_name' => $newRoute,
                ]);
            }
        }

        if (Schema::hasTable('user_permissions')) {
            $rows = DB::table('user_permissions')->where('route_name', 'job_request.index')->get();
            foreach ($rows as $row) {
                $exists = DB::table('user_permissions')
                    ->where('user_id', $row->user_id)
                    ->where('branch', $row->branch ?? '')
                    ->where('route_name', $newRoute)
                    ->exists();
                if ($exists) {
                    continue;
                }
                DB::table('user_permissions')->insert([
                    'user_id' => $row->user_id,
                    'branch' => $row->branch ?? '',
                    'route_name' => $newRoute,
                ]);
            }
        }
    }
};
