<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CARD_SUFFIXES = [
        'client_details',
        'job_details',
        'notes',
        'complexity',
        'plans',
        'documents',
        'checker_uploads',
        'run_comments',
        'comments',
        'activity',
    ];

    /**
     * @return array<string, list<string>>
     */
    private function routeCopyMap(): array
    {
        $map = [];
        foreach (self::CARD_SUFFIXES as $suffix) {
            $map['job_view.bph.card.' . $suffix] = ['job_view.bluinq.card.' . $suffix];
            $map['csp.view'][] = 'job_view.csp.card.' . $suffix;
        }

        return $map;
    }

    private function copyForUserPermissions(array $copyMap): void
    {
        if (!Schema::hasTable('user_permissions')) {
            return;
        }

        $rows = DB::table('user_permissions')->select('user_id', 'branch', 'route_name')->get();
        $payload = [];

        foreach ($rows as $row) {
            $source = (string) $row->route_name;
            if (empty($copyMap[$source])) {
                continue;
            }
            foreach ($copyMap[$source] as $target) {
                $payload[] = [
                    'user_id' => (int) $row->user_id,
                    'branch' => (string) $row->branch,
                    'route_name' => $target,
                ];
            }
        }

        foreach (array_chunk($payload, 300) as $chunk) {
            DB::table('user_permissions')->insertOrIgnore($chunk);
        }
    }

    private function copyForRolePermissions(array $copyMap): void
    {
        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        $rows = DB::table('role_permissions')->select('role', 'branch', 'route_name')->get();
        $payload = [];

        foreach ($rows as $row) {
            $source = (string) $row->route_name;
            if (empty($copyMap[$source])) {
                continue;
            }
            foreach ($copyMap[$source] as $target) {
                $payload[] = [
                    'role' => (string) $row->role,
                    'branch' => (string) $row->branch,
                    'route_name' => $target,
                ];
            }
        }

        foreach (array_chunk($payload, 300) as $chunk) {
            DB::table('role_permissions')->insertOrIgnore($chunk);
        }
    }

    public function up(): void
    {
        $copyMap = $this->routeCopyMap();
        $this->copyForUserPermissions($copyMap);
        $this->copyForRolePermissions($copyMap);
    }

    public function down(): void
    {
        // Intentionally left non-destructive.
    }
};
