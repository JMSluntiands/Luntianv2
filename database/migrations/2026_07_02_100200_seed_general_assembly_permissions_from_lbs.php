<?php

use App\Services\GeneralAssemblyPermissionMirror;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mirrors existing LBS grants to General Assembly (general_assembly.* / job_view.general_assembly.*).
 */
return new class extends Migration
{
    /** @return array<string, true> */
    private function allowedRouteSet(): array
    {
        $set = [];
        foreach (config('permissions.routes', []) as $group) {
            if (! is_array($group)) {
                continue;
            }
            foreach (array_keys($group) as $name) {
                if (is_string($name) && $name !== '') {
                    $set[$name] = true;
                }
            }
        }

        return $set;
    }

    private function mapRouteNameToGeneralAssembly(string $name): ?string
    {
        return GeneralAssemblyPermissionMirror::mapRouteNameToGeneralAssembly($name);
    }

    /** @return list<string> */
    private function generalAssemblyRouteNames(): array
    {
        $allowed = $this->allowedRouteSet();

        return array_values(array_filter(array_keys($allowed), static fn ($r) => is_string($r) && (str_starts_with($r, 'general_assembly.') || str_starts_with($r, 'job_view.general_assembly.'))));
    }

    public function up(): void
    {
        $allowed = $this->allowedRouteSet();

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')->select('role', 'branch', 'route_name')->get();
            $chunk = [];
            foreach ($rows as $row) {
                $mapped = $this->mapRouteNameToGeneralAssembly((string) $row->route_name);
                if ($mapped === null || ! isset($allowed[$mapped])) {
                    continue;
                }
                $chunk[] = [
                    'role' => $row->role,
                    'branch' => $row->branch ?? '',
                    'route_name' => $mapped,
                ];
            }
            foreach (array_chunk($chunk, 150) as $part) {
                DB::table('role_permissions')->insertOrIgnore($part);
            }
        }

        if (Schema::hasTable('user_permissions')) {
            $rows = DB::table('user_permissions')->select('user_id', 'branch', 'route_name')->get();
            $chunk = [];
            foreach ($rows as $row) {
                $mapped = $this->mapRouteNameToGeneralAssembly((string) $row->route_name);
                if ($mapped === null || ! isset($allowed[$mapped])) {
                    continue;
                }
                $chunk[] = [
                    'user_id' => (int) $row->user_id,
                    'branch' => $row->branch ?? '',
                    'route_name' => $mapped,
                ];
            }
            foreach (array_chunk($chunk, 150) as $part) {
                DB::table('user_permissions')->insertOrIgnore($part);
            }
        }
    }

    public function down(): void
    {
        $names = $this->generalAssemblyRouteNames();
        if ($names === []) {
            return;
        }
        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->whereIn('route_name', $names)->delete();
        }
        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('route_name', $names)->delete();
        }
    }
};
