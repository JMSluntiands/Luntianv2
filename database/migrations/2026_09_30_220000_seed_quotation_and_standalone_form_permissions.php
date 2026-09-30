<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->copyGrants('general_assembly.list', ['general_assembly.list.quotation']);
        $this->copyGrants('general_assembly.job.acceptForm', ['general_assembly.job.declineForm']);
        $this->copyGrants(
            ['settings.jotform_config', 'settings.email_config', 'settings.slack_config', 'settings.notifications', 'settings.permission'],
            ['settings.standalone_form', 'settings.standalone_form.toggle', 'settings.standalone_form.options']
        );
    }

    public function down(): void
    {
        $routes = [
            'general_assembly.list.quotation',
            'general_assembly.job.declineForm',
            'settings.standalone_form',
            'settings.standalone_form.toggle',
            'settings.standalone_form.options',
        ];

        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->whereIn('route_name', $routes)->delete();
        }
        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('route_name', $routes)->delete();
        }
    }

    /**
     * @param  string|list<string>  $sources
     * @param  list<string>  $targets
     */
    private function copyGrants(string|array $sources, array $targets): void
    {
        $sources = (array) $sources;

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')
                ->whereIn('route_name', $sources)
                ->get(['role', 'branch']);

            $pairs = [];
            foreach ($rows as $row) {
                $branch = (string) ($row->branch ?? '');
                $pairs[strtolower(trim((string) $row->role)).'|'.$branch] = [
                    'role' => $row->role,
                    'branch' => $branch,
                ];
            }

            foreach ($pairs as $pair) {
                foreach ($targets as $route) {
                    $exists = DB::table('role_permissions')
                        ->where('role', $pair['role'])
                        ->where('branch', $pair['branch'])
                        ->where('route_name', $route)
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                    DB::table('role_permissions')->insert([
                        'role' => $pair['role'],
                        'branch' => $pair['branch'],
                        'route_name' => $route,
                    ]);
                }
            }
        }

        if (! Schema::hasTable('user_permissions')) {
            return;
        }

        $userIds = DB::table('user_permissions')
            ->whereIn('route_name', $sources)
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $branches = DB::table('user_permissions')
                ->where('user_id', $userId)
                ->whereIn('route_name', $sources)
                ->pluck('branch')
                ->unique()
                ->values();

            foreach ($branches as $branch) {
                foreach ($targets as $route) {
                    $exists = DB::table('user_permissions')
                        ->where('user_id', $userId)
                        ->where('branch', (string) $branch)
                        ->where('route_name', $route)
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                    DB::table('user_permissions')->insert([
                        'user_id' => $userId,
                        'branch' => (string) $branch,
                        'route_name' => $route,
                    ]);
                }
            }
        }
    }
};
