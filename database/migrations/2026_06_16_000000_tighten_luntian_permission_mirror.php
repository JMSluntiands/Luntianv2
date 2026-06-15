<?php

use App\Services\LuntianPermissionMirror;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Luntian access must not inherit LBS-only permissions (forms submitted, accept form, etc.).
 * Drops stray Luntian grants outside the Luntian permission module and strips LBS forms
 * permissions from roles/users that have Luntian list but not LBS list.
 */
return new class extends Migration
{
    /** @var list<string> */
    private const LBS_FORMS_ROUTES = [
        'lbs.list.formsSubmitted',
        'lbs.job.acceptForm',
    ];

    public function up(): void
    {
        $allowed = LuntianPermissionMirror::luntianModuleRouteSet();
        $allowedNames = array_keys($allowed);

        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')
                ->where(static function ($q): void {
                    $q->where('route_name', 'like', 'luntian.%')
                        ->orWhere('route_name', 'like', 'job_view.luntian.%');
                })
                ->whereNotIn('route_name', $allowedNames)
                ->delete();

            $this->stripLbsFormsFromLuntianOnlyGrants('role_permissions', 'role');
        }

        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')
                ->where(static function ($q): void {
                    $q->where('route_name', 'like', 'luntian.%')
                        ->orWhere('route_name', 'like', 'job_view.luntian.%');
                })
                ->whereNotIn('route_name', $allowedNames)
                ->delete();

            $this->stripLbsFormsFromLuntianOnlyGrants('user_permissions', 'user_id');
        }
    }

    /**
     * @param  'role_permissions'|'user_permissions'  $table
     * @param  'role'|'user_id'  $subjectColumn
     */
    private function stripLbsFormsFromLuntianOnlyGrants(string $table, string $subjectColumn): void
    {
        $rows = DB::table($table)->select($subjectColumn, 'branch', 'route_name')->get();
        $bySubjectBranch = $rows->groupBy(static fn ($r) => (string) $r->{$subjectColumn}."\x1e".(string) ($r->branch ?? ''));

        foreach ($bySubjectBranch as $bucket) {
            $routeNames = $bucket->pluck('route_name')->map(static fn ($n) => (string) $n)->all();
            if (! in_array('luntian.list', $routeNames, true) || in_array('lbs.list', $routeNames, true)) {
                continue;
            }

            $first = $bucket->first();
            $query = DB::table($table)
                ->where($subjectColumn, $first->{$subjectColumn})
                ->where('branch', $first->branch ?? '')
                ->whereIn('route_name', self::LBS_FORMS_ROUTES);

            $query->delete();
        }
    }

    public function down(): void
    {
        // Non-reversible: removed grants cannot be reconstructed reliably.
    }
};
