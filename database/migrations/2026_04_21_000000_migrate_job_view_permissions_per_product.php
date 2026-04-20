<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PRODUCTS = ['lbs', 'bph', 'efficient_living'];

    private const CARD_SUFFIXES = [
        'client_details',
        'job_details',
        'notes',
        'complexity',
        'plans',
        'documents',
        'run_comments',
        'comments',
        'activity',
    ];

    private const BUTTON_SUFFIXES = [
        'archive_job',
        'edit.client_details',
        'edit.job_details',
        'edit.assignment',
        'edit.notes',
        'edit.complexity',
        'files.add',
        'files.delete',
        'comments.run.send',
        'comments.job.send',
    ];

    /** @return list<string> */
    private function oldRouteNamesToDelete(): array
    {
        $out = ['job_view.edit.assigned'];
        foreach (self::CARD_SUFFIXES as $s) {
            $out[] = 'job_view.card.' . $s;
        }
        $out[] = 'job_view.card.checker_uploads';
        $out[] = 'job_view.card.bph_additional';
        foreach (self::BUTTON_SUFFIXES as $s) {
            $out[] = 'job_view.button.' . $s;
        }

        return $out;
    }

    /**
     * @param  array<string, true>  $s
     * @return list<string>
     */
    private function newRouteNamesFromOldSet(array $s): array
    {
        $names = [];

        $add = static function (string $name) use (&$names): void {
            $names[$name] = true;
        };

        if (!empty($s['job_view.edit.assigned'])) {
            foreach (self::PRODUCTS as $p) {
                $add('job_view.' . $p . '.edit_assigned');
            }
        }

        foreach (self::CARD_SUFFIXES as $suffix) {
            $old = 'job_view.card.' . $suffix;
            if (!empty($s[$old])) {
                foreach (self::PRODUCTS as $p) {
                    $add('job_view.' . $p . '.card.' . $suffix);
                }
            }
        }

        if (!empty($s['job_view.card.bph_additional'])) {
            $add('job_view.bph.card.bph_additional');
        }

        foreach (self::PRODUCTS as $p) {
            $oldGlobalChecker = !empty($s['job_view.card.checker_uploads']);
            $oldMod = !empty($s['job_view.' . $p . '.card.checker_uploads']);
            if ($oldGlobalChecker || $oldMod) {
                $add('job_view.' . $p . '.card.checker_uploads');
            }
        }

        foreach (self::BUTTON_SUFFIXES as $suffix) {
            $old = 'job_view.button.' . $suffix;
            if (!empty($s[$old])) {
                foreach (self::PRODUCTS as $p) {
                    $add('job_view.' . $p . '.button.' . $suffix);
                }
            }
        }

        return array_keys($names);
    }

    public function up(): void
    {
        $oldNames = $this->oldRouteNamesToDelete();

        if (Schema::hasTable('user_permissions')) {
            $rows = DB::table('user_permissions')->select('user_id', 'branch', 'route_name')->get();
            $byUserBranch = $rows->groupBy(fn ($r) => (int) $r->user_id . "\x1e" . (string) $r->branch);

            foreach ($byUserBranch as $bucket) {
                $first = $bucket->first();
                $userId = (int) $first->user_id;
                $branch = (string) $first->branch;
                $s = [];
                foreach ($bucket as $r) {
                    $s[(string) $r->route_name] = true;
                }
                $newNames = $this->newRouteNamesFromOldSet($s);
                if ($newNames === []) {
                    continue;
                }
                $payload = [];
                foreach ($newNames as $rn) {
                    $payload[] = ['user_id' => $userId, 'branch' => $branch, 'route_name' => $rn];
                }
                foreach (array_chunk($payload, 200) as $chunk) {
                    DB::table('user_permissions')->insertOrIgnore($chunk);
                }
            }

            DB::table('user_permissions')->whereIn('route_name', $oldNames)->delete();
        }

        if (Schema::hasTable('role_permissions')) {
            $rows = DB::table('role_permissions')->select('role', 'branch', 'route_name')->get();
            $byRoleBranch = $rows->groupBy(fn ($r) => (string) $r->role . "\x1e" . (string) $r->branch);

            foreach ($byRoleBranch as $bucket) {
                $first = $bucket->first();
                $role = (string) $first->role;
                $branch = (string) $first->branch;
                $s = [];
                foreach ($bucket as $r) {
                    $s[(string) $r->route_name] = true;
                }
                $newNames = $this->newRouteNamesFromOldSet($s);
                if ($newNames === []) {
                    continue;
                }
                $payload = [];
                foreach ($newNames as $rn) {
                    $payload[] = ['role' => $role, 'branch' => $branch, 'route_name' => $rn];
                }
                foreach (array_chunk($payload, 200) as $chunk) {
                    DB::table('role_permissions')->insertOrIgnore($chunk);
                }
            }

            DB::table('role_permissions')->whereIn('route_name', $oldNames)->delete();
        }
    }

    public function down(): void
    {
        // Non-reversible: old keys were removed; restoring exact matrix is ambiguous.
    }
};
