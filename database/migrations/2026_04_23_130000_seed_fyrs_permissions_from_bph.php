<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mirrors existing BPH grants to Fyrs Energy Wise (fyrs.* / job_view.fyrs.*) so Staff/Branch
 * pick up the new product line without re-clicking every checkbox in Permission settings.
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

    private function mapRouteNameToFyrs(string $name): ?string
    {
        if (str_starts_with($name, 'job_view.bph.')) {
            return str_replace('job_view.bph.', 'job_view.fyrs.', $name);
        }
        if (preg_match('/^bph\.(add|list|completed|review|mailbox|trash|store|view|update)$/', $name, $m)) {
            return 'fyrs.'.$m[1];
        }
        if (preg_match('/^bph\.job\.(.+)$/', $name, $m)) {
            return 'fyrs.job.'.$m[1];
        }

        return null;
    }

    /** @return list<string> */
    private function fyrsRouteNames(): array
    {
        $allowed = $this->allowedRouteSet();

        return array_values(array_filter(array_keys($allowed), static fn ($r) => is_string($r) && (str_starts_with($r, 'fyrs.') || str_starts_with($r, 'job_view.fyrs.'))));
    }

    public function up(): void
    {
        $allowed = $this->allowedRouteSet();

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')->select('role', 'branch', 'route_name')->get();
            $chunk = [];
            foreach ($rows as $row) {
                $mapped = $this->mapRouteNameToFyrs((string) $row->route_name);
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
                $mapped = $this->mapRouteNameToFyrs((string) $row->route_name);
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
        $names = $this->fyrsRouteNames();
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
