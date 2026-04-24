<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Anyone who already has BPH Email (bph_client_email.*) gets the same grants for A&M Email (amt_client_email.*).
 */
return new class extends Migration
{
    /** @return array<string, string> */
    private function routeMap(): array
    {
        return [
            'bph_client_email.index' => 'amt_client_email.index',
            'bph_client_email.create' => 'amt_client_email.create',
            'bph_client_email.store' => 'amt_client_email.store',
            'bph_client_email.edit' => 'amt_client_email.edit',
            'bph_client_email.update' => 'amt_client_email.update',
            'bph_client_email.destroy' => 'amt_client_email.destroy',
        ];
    }

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

    public function up(): void
    {
        $map = $this->routeMap();
        $allowed = $this->allowedRouteSet();

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')->select('role', 'branch', 'route_name')->get();
            $chunk = [];
            foreach ($rows as $row) {
                $from = (string) $row->route_name;
                $to = $map[$from] ?? null;
                if ($to === null || ! isset($allowed[$to])) {
                    continue;
                }
                $chunk[] = [
                    'role' => $row->role,
                    'branch' => $row->branch ?? '',
                    'route_name' => $to,
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
                $from = (string) $row->route_name;
                $to = $map[$from] ?? null;
                if ($to === null || ! isset($allowed[$to])) {
                    continue;
                }
                $chunk[] = [
                    'user_id' => (int) $row->user_id,
                    'branch' => $row->branch ?? '',
                    'route_name' => $to,
                ];
            }
            foreach (array_chunk($chunk, 150) as $part) {
                DB::table('user_permissions')->insertOrIgnore($part);
            }
        }
    }

    public function down(): void
    {
        $names = array_values($this->routeMap());
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
