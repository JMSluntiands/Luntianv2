<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mirrors existing Bluinq / BPH grants to A&M (amt.* routes) so Staff/Branch (role_permissions)
 * and per-user overrides (user_permissions) pick up the new product line without
 * re-clicking every checkbox in Permission settings.
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

    private function mapRouteNameToAmt(string $name): ?string
    {
        if (str_starts_with($name, 'bluinq.')) {
            return 'amt.'.substr($name, strlen('bluinq.'));
        }
        if (str_starts_with($name, 'job_view.bluinq.')) {
            return str_replace('job_view.bluinq.', 'job_view.amt.', $name);
        }
        if (preg_match('/^bph\.(add|list|completed|review|mailbox|trash|store|view|update)$/', $name, $m)) {
            return 'amt.'.$m[1];
        }
        if (preg_match('/^bph\.job\.(sendSlack|sendSubmissionEmail)$/', $name, $m)) {
            return 'amt.job.'.$m[1];
        }
        if (str_starts_with($name, 'job_view.bph.card.') && ! str_contains($name, 'bph_additional')) {
            return str_replace('job_view.bph.card.', 'job_view.amt.card.', $name);
        }

        return null;
    }

    /** @return list<string> */
    private function amtRouteNames(): array
    {
        $allowed = $this->allowedRouteSet();

        return array_values(array_filter(array_keys($allowed), static fn ($r) => is_string($r) && (str_starts_with($r, 'amt.') || str_starts_with($r, 'job_view.amt.'))));
    }

    public function up(): void
    {
        $allowed = $this->allowedRouteSet();

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')->select('role', 'branch', 'route_name')->get();
            $chunk = [];
            foreach ($rows as $row) {
                $mapped = $this->mapRouteNameToAmt((string) $row->route_name);
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
                $mapped = $this->mapRouteNameToAmt((string) $row->route_name);
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
        $names = $this->amtRouteNames();
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
