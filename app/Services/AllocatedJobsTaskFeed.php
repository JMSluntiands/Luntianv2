<?php

namespace App\Services;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Collects Allocated jobs across all Job Management modules for Task Management.
 * Only client + reference (plus module / assignee code for display) are returned.
 */
class AllocatedJobsTaskFeed
{
    /**
     * @return Collection<int, object{
     *   key: string,
     *   module: string,
     *   client: string,
     *   reference: string,
     *   assignee_code: string|null,
     *   assignee_user_id: int|null,
     *   sort_at: string|null
     * }>
     */
    public static function all(): Collection
    {
        $rows = collect();

        $rows = $rows
            ->concat(self::lbsStyleAllocated(
                module: 'LBS',
                scope: static function ($q): void {
                    JobCountsScope::applyLbsStandardJobsScope($q, '');
                }
            ))
            ->concat(self::lbsStyleAllocated(
                module: 'EFFICIENT LIVING',
                scope: static function ($q): void {
                    $q->whereRaw("job_request_id LIKE 'EA\\_EL\\_%'");
                }
            ))
            ->concat(self::lbsStyleAllocated(
                module: 'LUNTIAN',
                scope: static function ($q): void {
                    JobCountsScope::applyLuntianJobsScope($q, '');
                }
            ));

        if (Schema::hasTable('job_general_assembly')) {
            $rows = $rows->concat(self::generalAssemblyAllocated());
        }

        $rows = $rows
            ->concat(self::bphStyleAllocated(
                module: 'BPH',
                table: 'job_bph',
                extra: static function ($q): void {
                    $q->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01']);
                }
            ))
            ->concat(self::bphStyleAllocated(
                module: 'BLUINQ',
                table: 'job_bph',
                extra: static function ($q): void {
                    $q->where('client_code', 'BLUINQ01');
                }
            ));

        $amtTable = Schema::hasTable('job_amt') ? 'job_amt' : 'job_bph';
        $rows = $rows->concat(self::bphStyleAllocated(
            module: 'A&M',
            table: $amtTable,
            extra: $amtTable === 'job_bph'
                ? static function ($q): void {
                    $q->where('client_code', 'AMT01');
                }
                : null
        ));

        $fyrsTable = Schema::hasTable('job_fyrs') ? 'job_fyrs' : 'job_bph';
        $rows = $rows->concat(self::bphStyleAllocated(
            module: 'FYRS ENERGY WISE',
            table: $fyrsTable,
            extra: $fyrsTable === 'job_bph'
                ? static function ($q): void {
                    $q->where('client_code', 'FYRS01');
                }
                : null
        ));

        foreach ([
            'CSP' => 'job_csp',
            'NH' => 'job_nh',
            'LC HOME BUILDER' => 'job_lc_home_builder',
            'LEADING ENERGY' => 'job_leading_energy',
        ] as $module => $table) {
            if (Schema::hasTable($table)) {
                $rows = $rows->concat(self::bphStyleAllocated(module: $module, table: $table));
            }
        }

        $codeToUserId = self::assigneeCodeMap();

        return $rows
            ->map(function (object $row) use ($codeToUserId) {
                $code = $row->assignee_code !== null ? strtoupper(trim((string) $row->assignee_code)) : '';
                $row->assignee_code = $code !== '' ? $code : null;
                $row->assignee_user_id = ($code !== '' && isset($codeToUserId[$code]))
                    ? $codeToUserId[$code]
                    : null;

                return $row;
            })
            ->sortByDesc(fn (object $row) => $row->sort_at ?? '')
            ->values();
    }

    /**
     * @return array<string, int>
     */
    private static function assigneeCodeMap(): array
    {
        return User::query()
            ->whereNotNull('unique_code')
            ->where('unique_code', '!=', '')
            ->get(['id', 'unique_code'])
            ->mapWithKeys(function (User $user) {
                $code = strtoupper(trim((string) $user->unique_code));

                return $code !== '' ? [$code => (int) $user->id] : [];
            })
            ->all();
    }

    /**
     * @param  callable(\Illuminate\Database\Query\Builder): void  $scope
     * @return Collection<int, object>
     */
    private static function lbsStyleAllocated(string $module, callable $scope): Collection
    {
        if (! Schema::hasTable('jobs')) {
            return collect();
        }

        $q = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->where('j.reference', 'like', 'JOBS%')
            ->where('j.job_status', 'Allocated');
        $scope($q);
        JobCountsScope::applyJobsTableAssignment($q, 'j.staff_id', 'j.checker_id');

        $rows = $q->get([
            'j.job_id',
            'j.reference',
            'j.job_reference_no',
            'j.client_code',
            'j.staff_id',
            'j.last_update',
            'j.log_date',
            'ca.client_account_name',
        ]);

        return $rows->map(function ($row) use ($module) {
            $client = trim((string) ($row->client_account_name ?? ''));
            if ($client === '') {
                $client = trim((string) ($row->client_code ?? ''));
            }
            $reference = trim((string) ($row->job_reference_no ?? ''));
            if ($reference === '') {
                $reference = trim((string) ($row->reference ?? ''));
            }

            return (object) [
                'key' => 'lbs:'.$module.':'.(int) $row->job_id,
                'module' => $module,
                'client' => $client !== '' ? $client : '—',
                'reference' => $reference !== '' ? $reference : '—',
                'assignee_code' => $row->staff_id,
                'assignee_user_id' => null,
                'sort_at' => (string) ($row->last_update ?? $row->log_date ?? ''),
            ];
        });
    }

    /**
     * @return Collection<int, object>
     */
    private static function generalAssemblyAllocated(): Collection
    {
        $q = DB::table('job_general_assembly as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->where('j.reference', 'like', 'JOB%')
            ->where('j.job_status', 'Allocated');
        JobCountsScope::applyJobsTableAssignment($q, 'j.staff_id', 'j.checker_id');

        $rows = $q->get([
            'j.job_id',
            'j.reference',
            'j.job_reference_no',
            'j.client_code',
            'j.staff_id',
            'j.last_update',
            'j.log_date',
            'ca.client_account_name',
        ]);

        $module = RolePermission::GENERIC_ASSESSMENT_NAME;

        return $rows->map(function ($row) use ($module) {
            $client = trim((string) ($row->client_account_name ?? ''));
            if ($client === '') {
                $client = trim((string) ($row->client_code ?? ''));
            }
            $reference = trim((string) ($row->job_reference_no ?? ''));
            if ($reference === '') {
                $reference = trim((string) ($row->reference ?? ''));
            }

            return (object) [
                'key' => 'ga:'.(int) $row->job_id,
                'module' => $module,
                'client' => $client !== '' ? $client : '—',
                'reference' => $reference !== '' ? $reference : '—',
                'assignee_code' => $row->staff_id,
                'assignee_user_id' => null,
                'sort_at' => (string) ($row->last_update ?? $row->log_date ?? ''),
            ];
        });
    }

    /**
     * @param  (callable(\Illuminate\Database\Query\Builder): void)|null  $extra
     * @return Collection<int, object>
     */
    private static function bphStyleAllocated(string $module, string $table, ?callable $extra = null): Collection
    {
        if (! Schema::hasTable($table)) {
            return collect();
        }

        $q = DB::table($table)
            ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = 'allocated'");
        if ($extra) {
            $extra($q);
        }
        JobCountsScope::applyJobBphAssignment($q);

        $select = ['id', 'reference', 'job_number', 'client_name', 'client_code', 'assigned', 'updated_at', 'created_at'];
        $cols = Schema::getColumnListing($table);
        $select = array_values(array_filter($select, fn (string $c) => in_array($c, $cols, true)));

        $rows = $q->get($select);

        return $rows->map(function ($row) use ($module, $table) {
            $client = trim((string) ($row->client_name ?? ''));
            if ($client === '') {
                $client = trim((string) ($row->client_code ?? ''));
            }
            $reference = trim((string) ($row->job_number ?? ''));
            if ($reference === '') {
                $reference = trim((string) ($row->reference ?? ''));
            }

            return (object) [
                'key' => $table.':'.(int) $row->id,
                'module' => $module,
                'client' => $client !== '' ? $client : '—',
                'reference' => $reference !== '' ? $reference : '—',
                'assignee_code' => $row->assigned ?? null,
                'assignee_user_id' => null,
                'sort_at' => (string) ($row->updated_at ?? $row->created_at ?? ''),
            ];
        });
    }
}
