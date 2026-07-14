<?php

namespace App\Services;

use App\Models\RolePermission;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Dashboard job counts (Philippine calendar):
 * - total: **completed + processing + pending + encoded today** (non-Processing, non-Completed).
 * - completed: **completed today** (completion_date).
 * - processing: status **Processing** (any date).
 * - pending: **non-completed backlog** from **previous dates** (before today's reset), excluding **Processing**.
 * - encoded today (in total only): **log_date today**, status not Processing or Completed.
 */
class DashboardJobStatsService
{
    private const TZ = 'Asia/Manila';

    /** @var array{client?: string, status?: string, staff?: string} */
    private static array $chartFilters = [];

    /** @return array{0:string,1:string,2:string} start, end, Y-m-d */
    private static function dayBoundsManila(?string $date = null): array
    {
        $day = $date
            ? Carbon::parse($date, self::TZ)->startOfDay()
            : Carbon::now(self::TZ)->startOfDay();
        $start = $day->format('Y-m-d H:i:s');
        $end = $day->copy()->endOfDay()->format('Y-m-d H:i:s');

        return [$start, $end, $day->toDateString()];
    }

    /** jobs.log_date calendar day = today (Manila); varchar/datetime safe. */
    private static function whereJobsLogDateToday($q, string $date): void
    {
        $q->whereRaw("LEFT(NULLIF(TRIM(log_date), ''), 10) = ?", [$date]);
    }

    /** jobs.log_date calendar day is not today (Manila); null/empty treated as not today. */
    private static function whereJobsLogDateNotToday($q, string $date): void
    {
        $q->whereRaw("(NULLIF(TRIM(log_date), '') IS NULL OR LEFT(NULLIF(TRIM(log_date), ''), 10) != ?)", [$date]);
    }

    /**
     * Same reference logged today already has Processing ù do not also count older status rows in encoded_today.
     */
    private static function applyExcludeStaleEncodedTodayWhenProcessingSibling(
        $q,
        string $table,
        string $date,
        string $refColumn = 'reference',
        string $statusColumn = 'job_status',
        string $logDateColumn = 'log_date'
    ): void {
        $q->whereNotExists(function ($sub) use ($table, $date, $refColumn, $statusColumn, $logDateColumn) {
            $sub->from("{$table} as __proc_sibling")
                ->whereColumn("__proc_sibling.{$refColumn}", "{$table}.{$refColumn}")
                ->whereRaw("LEFT(NULLIF(TRIM(__proc_sibling.{$logDateColumn}), ''), 10) = ?", [$date])
                ->whereRaw('LOWER(TRIM(__proc_sibling.'.$statusColumn.')) = ?', ['processing']);
        });
    }

    private static function applyExcludeStaleEncodedTodayWhenProcessingSiblingBph(
        $q,
        string $table,
        string $start,
        string $end
    ): void {
        $q->whereNotExists(function ($sub) use ($table, $start, $end) {
            $sub->from("{$table} as __proc_sibling")
                ->whereColumn('__proc_sibling.reference', "{$table}.reference")
                ->whereBetween('__proc_sibling.created_at', [$start, $end])
                ->whereRaw('LOWER(TRIM(__proc_sibling.status)) = ?', ['processing']);
        });
    }

    private static function applyLbsPipelineExclusions($q, string $productLine): void
    {
        if ($productLine === 'lbs') {
            $q->where(function ($w) {
                $w->whereNull('updated_by')
                    ->orWhereRaw("UPPER(TRIM(updated_by)) != ?", ['FORMS']);
            });
        }
    }

    private static function applyJobsTableDayFilter($q, string $bucket, string $start, string $end, string $date): void
    {
        if ($bucket === 'completed') {
            $q->whereBetween('completion_date', [$start, $end]);

            return;
        }

        if ($bucket === 'total') {
            $q->where(function ($w) use ($start, $end, $date) {
                $w->where(function ($logged) use ($date) {
                    self::whereJobsLogDateToday($logged, $date);
                    $logged->whereRaw("LOWER(TRIM(job_status)) != ?", ['completed']);
                })->orWhere(function ($completed) use ($start, $end) {
                    $completed->whereRaw('LOWER(TRIM(job_status)) = ?', ['completed'])
                        ->whereBetween('completion_date', [$start, $end]);
                });
            });

            return;
        }

        if ($bucket === 'pending') {
            self::whereJobsLogDateNotToday($q, $date);

            return;
        }

        if ($bucket === 'encoded_today') {
            self::whereJobsLogDateToday($q, $date);

            return;
        }

        if ($bucket === 'processing') {
            return;
        }

        // legacy buckets that still use log_date today
        self::whereJobsLogDateToday($q, $date);
    }

    private static function whereJobBphLoggedToday($q, string $start, string $end): void
    {
        $q->whereBetween('created_at', [$start, $end]);
    }

    private static function whereJobBphLoggedNotToday($q, string $start, string $end): void
    {
        $q->where(function ($w) use ($start, $end) {
            $w->whereNull('created_at')
                ->orWhere('created_at', '<', $start)
                ->orWhere('created_at', '>', $end);
        });
    }

    private static function applyJobBphDayFilter($q, string $bucket, string $start, string $end, string $date): void
    {
        if ($bucket === 'completed') {
            $q->where('date', $date);

            return;
        }

        if ($bucket === 'total') {
            $q->where(function ($w) use ($start, $end, $date) {
                $w->where(function ($logged) use ($start, $end) {
                    self::whereJobBphLoggedToday($logged, $start, $end);
                    $logged->whereRaw("LOWER(TRIM(status)) != ?", ['completed']);
                })->orWhere(function ($completed) use ($date) {
                    $completed->whereRaw('LOWER(TRIM(status)) = ?', ['completed'])
                        ->where('date', $date);
                });
            });

            return;
        }

        if ($bucket === 'pending') {
            self::whereJobBphLoggedNotToday($q, $start, $end);

            return;
        }

        if ($bucket === 'encoded_today') {
            self::whereJobBphLoggedToday($q, $start, $end);

            return;
        }

        if ($bucket === 'processing') {
            return;
        }

        self::whereJobBphLoggedToday($q, $start, $end);
    }

    /**
     * @return array{
     *   total: array<string,int>,
     *   completed: array<string,int>,
     *   processing: array<string,int>,
     *   pending: array<string,int>
     * }
     */
    public static function fetch(): array
    {
        $labels = RolePermission::dashboardStatCardLabels();

        $out = [
            'total' => array_fill_keys($labels, 0),
            'completed' => array_fill_keys($labels, 0),
            'processing' => array_fill_keys($labels, 0),
            'pending' => array_fill_keys($labels, 0),
        ];

        try {
            foreach ($labels as $label) {
                $out['completed'][$label] = self::countJobsBranchBucket($label, 'completed');
                $out['processing'][$label] = self::countJobsBranchBucket($label, 'processing');
                $out['pending'][$label] = self::countJobsBranchBucket($label, 'pending');
                $encodedToday = self::countJobsBranchBucket($label, 'encoded_today');
                $out['total'][$label] = $out['completed'][$label]
                    + $out['processing'][$label]
                    + $out['pending'][$label]
                    + $encodedToday;
            }
        } catch (Throwable) {
            foreach ($labels as $label) {
                $out['total'][$label] = 0;
                $out['completed'][$label] = 0;
                $out['processing'][$label] = 0;
                $out['pending'][$label] = 0;
            }
        }

        return $out;
    }

    /**
     * Job status chart: per dashboard stat branch (LBS, LUNTIAN, ù) with status breakdown.
     * Uses the same totals as the Total Jobs card (completed + processing + pending + encoded today).
     *
     * @return array{
     *   date: string,
     *   scope: string,
     *   branches: list<array{label: string, total: int, statuses: list<array{label: string, count: int, color: string|null, fontColor: string}>}>,
     *   filterOptions?: array{clients: list<array{value: string, label: string}>, statuses: list<array{value: string, label: string}>, staff: list<array{value: string, label: string}>},
     *   filters?: array{client: string, status: string, staff: string}
     * }
     */
    public static function fetchStatusChart(?string $date = null, array $filters = []): array
    {
        self::$chartFilters = [
            'client' => trim((string) ($filters['client'] ?? '')),
            'status' => trim((string) ($filters['status'] ?? '')),
            'staff' => trim((string) ($filters['staff'] ?? '')),
        ];

        try {
            [, , $dateStr] = self::dayBoundsManila($date);

            $statusMeta = Schema::hasTable('statuses')
                ? DB::table('statuses')->orderBy('id')->get(['name', 'color', 'font_color'])
                : collect();

            if ($statusMeta->isEmpty()) {
                $statusMeta = collect([
                    (object) ['name' => 'Pending', 'color' => null, 'font_color' => null],
                    (object) ['name' => 'Allocated', 'color' => null, 'font_color' => null],
                    (object) ['name' => 'Processing', 'color' => null, 'font_color' => null],
                    (object) ['name' => 'For Checking', 'color' => null, 'font_color' => null],
                ]);
            }

            $metaByKey = self::chartStatusMetaByKey($statusMeta);
            $statusOrder = self::chartStatusNameOrder($statusMeta);
            $branchLabels = self::chartBranchLabels();

            $branches = [];
            foreach ($branchLabels as $branchLabel) {
                $row = self::buildBranchChartRow($branchLabel, $dateStr, $statusOrder, $metaByKey);
                if ($row !== null) {
                    $branches[] = $row;
                }
            }

            usort($branches, static fn (array $a, array $b): int => ($b['total'] <=> $a['total']) ?: strcasecmp((string) $a['label'], (string) $b['label']));

            if (self::chartClientFilter() === '' && count($branchLabels) > 1) {
                $allRow = self::buildAllBranchChartRow($branchLabels, $dateStr, $statusOrder, $metaByKey);
                if ($allRow !== null) {
                    array_unshift($branches, $allRow);
                }
            }

            return [
                'date' => $dateStr,
                'scope' => 'dashboard_total_by_branch',
                'branches' => $branches,
                'filterOptions' => self::chartFilterOptions($statusOrder),
                'filters' => [
                    'client' => self::chartClientFilter(),
                    'status' => self::chartStatusFilter(),
                    'staff' => self::chartStaffFilter(),
                ],
            ];
        } catch (Throwable) {
            return [
                'date' => $date ?? Carbon::now(self::TZ)->toDateString(),
                'scope' => 'dashboard_total_by_branch',
                'branches' => [],
                'filterOptions' => ['clients' => [], 'statuses' => [], 'staff' => []],
                'filters' => ['client' => '', 'status' => '', 'staff' => ''],
            ];
        } finally {
            self::$chartFilters = [];
        }
    }

    private static function chartClientFilter(): string
    {
        return trim((string) (self::$chartFilters['client'] ?? ''));
    }

    private static function chartStatusFilter(): string
    {
        return trim((string) (self::$chartFilters['status'] ?? ''));
    }

    private static function chartStaffFilter(): string
    {
        return trim((string) (self::$chartFilters['staff'] ?? ''));
    }

    /** @return list<string> */
    private static function chartBranchLabels(): array
    {
        $only = self::branchStatLabelExclusive();
        $labels = $only ? [$only] : RolePermission::dashboardStatCardLabels();
        $client = self::chartClientFilter();
        if ($client === '') {
            return $labels;
        }

        return array_values(array_filter(
            $labels,
            static fn (string $label): bool => strcasecmp($label, $client) === 0
        ));
    }

    /**
     * @return array{clients: list<array{value: string, label: string}>, statuses: list<array{value: string, label: string}>, staff: list<array{value: string, label: string}>}
     */
    private static function chartFilterOptions(array $statusOrder): array
    {
        $only = self::branchStatLabelExclusive();
        $clientLabels = $only ? [$only] : RolePermission::dashboardStatCardLabels();

        $clients = [];
        foreach ($clientLabels as $label) {
            $clients[] = ['value' => $label, 'label' => $label];
        }

        $statuses = [];
        foreach ($statusOrder as $name) {
            if (self::isChartExcludedStatus($name)) {
                continue;
            }
            $statuses[] = ['value' => $name, 'label' => $name];
        }

        $staff = self::chartStaffFilterOptions();

        return ['clients' => $clients, 'statuses' => $statuses, 'staff' => $staff];
    }

    /** @return list<array{value: string, label: string}> */
    private static function chartStaffFilterOptions(): array
    {
        $staffByCode = [];

        foreach (self::chartBranchLabels() as $branchLabel) {
            $module = self::chartAssignmentModuleForBranchLabel($branchLabel);
            if ($module === null) {
                continue;
            }

            foreach (User::assignmentUsersForSelect($module, 'staff') as $user) {
                $code = strtoupper(trim((string) $user->unique_code));
                if ($code === '' || isset($staffByCode[$code])) {
                    continue;
                }

                $name = trim((string) ($user->fullname ?? $user->username ?? ''));
                $staffByCode[$code] = [
                    'value' => $code,
                    'label' => $name !== '' ? "{$code} ù {$name}" : $code,
                ];
            }
        }

        ksort($staffByCode);

        return array_values($staffByCode);
    }

    private static function chartAssignmentModuleForBranchLabel(string $branchLabel): ?string
    {
        return match (strtoupper(trim($branchLabel))) {
            'LBS' => 'lbs',
            'GENERAL ASSEMBLY' => 'general_assembly',
            'GENERIC ASSESSMENT' => 'general_assembly',
            'LUNTIAN' => 'luntian',
            'EFFICIENT LIVING' => 'efficient_living',
            'BPH' => 'bph',
            'BLUINQ' => 'bluinq',
            'A&M' => 'amt',
            'FYRS ENERGY WISE' => 'fyrs',
            'CSP' => 'csp',
            'NH' => 'nh',
            'LC HOME BUILDER' => 'lc_home_builder',
            'LEADING ENERGY' => 'leading_energy',
            default => null,
        };
    }

    private static function applyChartStaffFilter($q, string $tableKind): void
    {
        $staff = strtoupper(trim(self::chartStaffFilter()));
        if ($staff === '') {
            return;
        }

        if (in_array($tableKind, ['jobs', 'job_general_assembly'], true)) {
            $q->where(function ($w) use ($staff) {
                $w->whereRaw("UPPER(TRIM(COALESCE(staff_id, ''))) = ?", [$staff])
                    ->orWhereRaw("UPPER(TRIM(COALESCE(checker_id, ''))) = ?", [$staff]);
            });

            return;
        }

        $q->where(function ($w) use ($staff) {
            $w->whereRaw("UPPER(TRIM(COALESCE(assigned, ''))) = ?", [$staff])
                ->orWhereRaw("UPPER(TRIM(COALESCE(checked, ''))) = ?", [$staff]);
        });
    }

    /** @param \Illuminate\Support\Collection<int, object> $statusMeta */
    private static function chartStatusMetaByKey($statusMeta): array
    {
        $byKey = [];
        foreach ($statusMeta as $status) {
            $name = trim((string) ($status->name ?? ''));
            if ($name === '') {
                continue;
            }
            $byKey[mb_strtolower($name)] = $status;
        }

        return $byKey;
    }

    /**
     * Statuses table order first, then any pipeline status seen in job tables (live DB may be missing rows in statuses).
     *
     * @param \Illuminate\Support\Collection<int, object> $statusMeta
     * @return list<string>
     */
    private static function chartStatusNameOrder($statusMeta): array
    {
        $names = [];
        $seen = [];

        foreach ($statusMeta as $status) {
            $name = trim((string) ($status->name ?? ''));
            if ($name === '') {
                continue;
            }
            $key = mb_strtolower($name);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $names[] = $name;
        }

        foreach (self::distinctPipelineStatusNames() as $name) {
            $key = mb_strtolower($name);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $names[] = $name;
        }

        return $names;
    }

    /** @return list<string> */
    private static function distinctPipelineStatusNames(): array
    {
        $names = [];

        $collect = static function (string $table, string $column) use (&$names): void {
            if (! Schema::hasTable($table)) {
                return;
            }
            $rows = DB::table($table)
                ->whereNotNull($column)
                ->whereRaw("TRIM({$column}) != ''")
                ->distinct()
                ->pluck($column);
            foreach ($rows as $value) {
                $value = trim((string) $value);
                if ($value !== '') {
                    $names[] = $value;
                }
            }
        };

        $collect('jobs', 'job_status');
        $collect('job_general_assembly', 'job_status');
        foreach (['job_bph', 'job_amt', 'job_fyrs', 'job_csp', 'job_nh', 'job_lc_home_builder', 'job_leading_energy'] as $table) {
            $collect($table, 'status');
        }

        return array_values(array_unique($names));
    }

    /**
     * @param  array<string, object>  $metaByKey
     * @return list<array{label: string, count: int, color: string|null, fontColor: string}>
     */
    private static function chartStatusRowsForBranch(
        string $branchLabel,
        string $dateStr,
        array $statusOrder,
        array $metaByKey
    ): array {
        $rows = [];

        $statusNames = $statusOrder;
        $statusFilter = self::chartStatusFilter();
        if ($statusFilter !== '') {
            $statusNames = [$statusFilter];
        }

        foreach ($statusNames as $name) {
            if (self::isChartExcludedStatus($name)) {
                continue;
            }
            $count = self::countBranchStatusDashboardTotal($branchLabel, $name, $dateStr);
            if ($count <= 0) {
                continue;
            }
            $rows[] = self::formatChartStatusRow($name, $count, $metaByKey[mb_strtolower($name)] ?? null);
        }

        $cardTotal = self::countBranchDashboardTotal($branchLabel, $dateStr);
        $statusSum = array_sum(array_column($rows, 'count'));
        if ($cardTotal > $statusSum) {
            $rows[] = self::formatChartStatusRow('Other', $cardTotal - $statusSum, null);
        }

        return $rows;
    }

    /**
     * @param  array<string, object>  $metaByKey
     * @return array{label: string, total: int, statuses: list<array{label: string, count: int, color: string|null, fontColor: string}>}|null
     */
    private static function buildBranchChartRow(
        string $branchLabel,
        string $dateStr,
        array $statusOrder,
        array $metaByKey
    ): ?array {
        $cardTotal = self::countBranchDashboardTotal($branchLabel, $dateStr);
        if ($cardTotal <= 0) {
            return null;
        }

        $statusRows = self::chartStatusRowsForBranch($branchLabel, $dateStr, $statusOrder, $metaByKey);
        if ($statusRows === []) {
            return null;
        }

        return [
            'label' => $branchLabel,
            'total' => $cardTotal,
            'statuses' => $statusRows,
        ];
    }

    /**
     * @param  list<string>  $branchLabels
     * @param  array<string, object>  $metaByKey
     * @return array{label: string, total: int, statuses: list<array{label: string, count: int, color: string|null, fontColor: string}>}|null
     */
    private static function buildAllBranchChartRow(
        array $branchLabels,
        string $dateStr,
        array $statusOrder,
        array $metaByKey
    ): ?array {
        $cardTotal = 0;
        foreach ($branchLabels as $branchLabel) {
            $cardTotal += self::countBranchDashboardTotal($branchLabel, $dateStr);
        }
        if ($cardTotal <= 0) {
            return null;
        }

        $statusRows = [];
        $statusNames = $statusOrder;
        $statusFilter = self::chartStatusFilter();
        if ($statusFilter !== '') {
            $statusNames = [$statusFilter];
        }
        foreach ($statusNames as $name) {
            if (self::isChartExcludedStatus($name)) {
                continue;
            }
            $count = 0;
            foreach ($branchLabels as $branchLabel) {
                $count += self::countBranchStatusDashboardTotal($branchLabel, $name, $dateStr);
            }
            if ($count <= 0) {
                continue;
            }
            $statusRows[] = self::formatChartStatusRow($name, $count, $metaByKey[mb_strtolower($name)] ?? null);
        }

        $statusSum = array_sum(array_column($statusRows, 'count'));
        if ($cardTotal > $statusSum) {
            $statusRows[] = self::formatChartStatusRow('Other', $cardTotal - $statusSum, null);
        }

        return [
            'label' => 'All',
            'total' => $cardTotal,
            'statuses' => $statusRows,
        ];
    }

    private static function formatChartStatusRow(string $name, int $count, ?object $meta): array
    {
        return [
            'label' => $name,
            'count' => $count,
            'color' => $meta !== null && $meta->color !== null && trim((string) $meta->color) !== ''
                ? trim((string) $meta->color)
                : null,
            'fontColor' => Status::resolveFontColor($meta->font_color ?? null),
        ];
    }

    private static function isChartExcludedStatus(string $name): bool
    {
        return in_array(mb_strtolower(trim($name)), ['archived', 'archive', 'cancelled', 'deleted'], true);
    }

    private static function applyExcludeDashboardTerminalStatuses($q, string $column): void
    {
        $q->whereRaw(
            "LOWER(TRIM({$column})) NOT IN ('archived', 'archive', 'cancelled', 'deleted')"
        );
    }

    /** Same branch total as the Total Jobs dashboard card. */
    private static function countBranchDashboardTotal(string $branchLabel, ?string $date = null): int
    {
        $statusFilter = self::chartStatusFilter();
        if ($statusFilter !== '') {
            return self::countBranchStatusDashboardTotal($branchLabel, $statusFilter, $date);
        }

        return self::countJobsBranchBucket($branchLabel, 'completed', $date)
            + self::countJobsBranchBucket($branchLabel, 'processing', $date)
            + self::countJobsBranchBucket($branchLabel, 'pending', $date)
            + self::countJobsBranchBucket($branchLabel, 'encoded_today', $date);
    }

    /** Per-status count within the Total Jobs card buckets for one branch. */
    private static function countBranchStatusDashboardTotal(string $branchLabel, string $statusName, ?string $date = null): int
    {
        $only = self::branchStatLabelExclusive();
        if ($only !== null && strcasecmp((string) $branchLabel, (string) $only) !== 0) {
            return 0;
        }

        $total = 0;
        foreach (['completed', 'processing', 'pending', 'encoded_today'] as $bucket) {
            $total += self::countJobsBranchBucket($branchLabel, $bucket, $date, $statusName);
        }

        return $total;
    }

    /** Live per-status count for one Job Management vertical (matches sidebar/list queries). */
    private static function countBranchStatusJobManagement(string $branchLabel, string $statusName): int
    {
        $only = self::branchStatLabelExclusive();
        if ($only !== null && strcasecmp((string) $branchLabel, (string) $only) !== 0) {
            return 0;
        }

        return match ($branchLabel) {
            'LBS' => self::countJobsTableLiveStatus($statusName, 'lbs'),
            'GENERAL ASSEMBLY' => self::countGeneralAssemblyLiveStatus($statusName),
            'GENERIC ASSESSMENT' => self::countGeneralAssemblyLiveStatus($statusName),
            'LUNTIAN' => self::countJobsTableLiveStatus($statusName, 'luntian'),
            'EFFICIENT LIVING' => self::countJobsTableLiveStatus($statusName, 'efficient_living'),
            'BPH' => self::countJobBphLiveStatus($statusName, 'bph'),
            'BLUINQ' => self::countJobBphLiveStatus($statusName, 'bluinq'),
            'A&M' => self::countJobBphLiveStatus($statusName, 'amt'),
            'FYRS ENERGY WISE' => self::countJobBphLiveStatus($statusName, 'fyrs'),
            default => 0,
        };
    }

    private static function countJobsTableLiveStatus(string $statusName, string $productLine): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        $q = DB::table('jobs')->where('reference', 'like', 'JOBS%');

        if ($productLine === 'efficient_living') {
            $q->whereRaw("job_request_id LIKE 'EA\_EL\_%'");
        } elseif ($productLine === 'luntian') {
            JobCountsScope::applyLuntianJobsScope($q, '');
        } else {
            JobCountsScope::applyLbsStandardJobsScope($q, '');
        }

        self::applyLbsPipelineExclusions($q, $productLine);
        $q->whereRaw('LOWER(TRIM(job_status)) = ?', [mb_strtolower(trim($statusName))]);
        JobCountsScope::applyJobsTableAssignment($q);

        return (int) $q->count();
    }

    private static function countGeneralAssemblyLiveStatus(string $statusName): int
    {
        if (! Schema::hasTable('job_general_assembly')) {
            return 0;
        }

        $q = DB::table('job_general_assembly')->where('reference', 'like', 'JOBS%');
        self::applyLbsPipelineExclusions($q, 'lbs');
        $q->whereRaw('LOWER(TRIM(job_status)) = ?', [mb_strtolower(trim($statusName))]);
        JobCountsScope::applyJobsTableAssignment($q);

        return (int) $q->count();
    }

    private static function countJobBphLiveStatus(string $statusName, string $which): int
    {
        $table = 'job_bph';
        if ($which === 'amt' && Schema::hasTable('job_amt')) {
            $table = 'job_amt';
        } elseif ($which === 'fyrs' && Schema::hasTable('job_fyrs')) {
            $table = 'job_fyrs';
        }

        if (! Schema::hasTable($table)) {
            return 0;
        }

        $q = DB::table($table);

        if ($which === 'bluinq') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['bluinq01']);
        } elseif ($which === 'amt' && $table === 'job_bph') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['amt01']);
        } elseif ($which === 'fyrs' && $table === 'job_bph') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['fyrs01']);
        } elseif ($which === 'bph') {
            $q->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01']);
        }

        JobCountsScope::applyJobBphBranchVerticalScope($q);
        $q->whereRaw('LOWER(TRIM(status)) = ?', [mb_strtolower(trim($statusName))]);
        JobCountsScope::applyJobBphAssignment($q);

        return (int) $q->count();
    }

    /**
     * Branch accounts: only aggregate stats for the product line tied to users.branch (see RolePermission::mapBranchStringToDashboardStatLabel).
     */
    private static function branchStatLabelExclusive(): ?string
    {
        $r = strtolower(trim((string) session('user_role', '')));
        if ($r !== 'branch' || ! session()->has('user_id')) {
            return null;
        }
        $ub = RolePermission::normalizeBranch((string) session('user_branch', ''));
        if ($ub === '') {
            return null;
        }

        return RolePermission::mapBranchStringToDashboardStatLabel($ub) ?? $ub;
    }

    private static function countJobsBranchBucket(string $branchLabel, string $bucket, ?string $date = null, ?string $statusName = null): int
    {
        $only = self::branchStatLabelExclusive();
        if ($only !== null && strcasecmp((string) $branchLabel, (string) $only) !== 0) {
            return 0;
        }

        return match ($branchLabel) {
            'LBS' => self::countJobsTable($bucket, 'lbs', $date, $statusName),
            'GENERAL ASSEMBLY' => self::countGeneralAssemblyTable($bucket, $date, $statusName),
            'GENERIC ASSESSMENT' => self::countGeneralAssemblyTable($bucket, $date, $statusName),
            'LUNTIAN' => self::countJobsTable($bucket, 'luntian', $date, $statusName),
            'EFFICIENT LIVING' => self::countJobsTable($bucket, 'efficient_living', $date, $statusName),
            'BPH' => self::countJobBph($bucket, 'bph', $date, $statusName),
            'BLUINQ' => self::countJobBph($bucket, 'bluinq', $date, $statusName),
            'A&M' => self::countJobBph($bucket, 'amt', $date, $statusName),
            'FYRS ENERGY WISE' => self::countJobBph($bucket, 'fyrs', $date, $statusName),
            default => 0,
        };
    }

    private static function countJobsTable(string $bucket, string $productLine = 'lbs', ?string $date = null, ?string $statusName = null): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        [$start, $end, $dateStr] = self::dayBoundsManila($date);

        $q = DB::table('jobs')->where('reference', 'like', 'JOBS%');

        if ($productLine === 'efficient_living') {
            $q->whereRaw("job_request_id LIKE 'EA\_EL\_%'");
        } elseif ($productLine === 'luntian') {
            JobCountsScope::applyLuntianJobsScope($q, '');
        } else {
            JobCountsScope::applyLbsStandardJobsScope($q, '');
        }

        self::applyLbsPipelineExclusions($q, $productLine);

        self::applyJobsTableDayFilter($q, $bucket, $start, $end, $dateStr);

        self::applyExcludeDashboardTerminalStatuses($q, 'job_status');

        JobCountsScope::applyJobsTableAssignment($q);

        self::applyChartStaffFilter($q, 'jobs');

        switch ($bucket) {
            case 'total':
                break;
            case 'completed':
                $q->whereRaw('LOWER(TRIM(job_status)) = ?', ['completed']);
                break;
            case 'processing':
                $q->whereRaw('LOWER(TRIM(job_status)) = ?', ['processing']);
                break;
            case 'pending':
                $q->whereRaw("LOWER(TRIM(job_status)) NOT IN ('completed', 'processing')");
                break;
            case 'encoded_today':
                $q->whereRaw("LOWER(TRIM(job_status)) NOT IN ('completed', 'processing')");
                self::applyExcludeStaleEncodedTodayWhenProcessingSibling($q, 'jobs', $dateStr);
                break;
        }

        if ($statusName !== null && $statusName !== '') {
            $q->whereRaw('LOWER(TRIM(job_status)) = ?', [mb_strtolower(trim($statusName))]);
        }

        return (int) $q->count();
    }

    private static function countGeneralAssemblyTable(string $bucket, ?string $date = null, ?string $statusName = null): int
    {
        if (! Schema::hasTable('job_general_assembly')) {
            return 0;
        }

        [$start, $end, $dateStr] = self::dayBoundsManila($date);

        $q = DB::table('job_general_assembly')->where('reference', 'like', 'JOBS%');

        self::applyLbsPipelineExclusions($q, 'lbs');

        self::applyJobsTableDayFilter($q, $bucket, $start, $end, $dateStr);

        self::applyExcludeDashboardTerminalStatuses($q, 'job_status');

        JobCountsScope::applyJobsTableAssignment($q);

        self::applyChartStaffFilter($q, 'job_general_assembly');

        switch ($bucket) {
            case 'total':
                break;
            case 'completed':
                $q->whereRaw('LOWER(TRIM(job_status)) = ?', ['completed']);
                break;
            case 'processing':
                $q->whereRaw('LOWER(TRIM(job_status)) = ?', ['processing']);
                break;
            case 'pending':
                $q->whereRaw("LOWER(TRIM(job_status)) NOT IN ('completed', 'processing')");
                break;
            case 'encoded_today':
                $q->whereRaw("LOWER(TRIM(job_status)) NOT IN ('completed', 'processing')");
                self::applyExcludeStaleEncodedTodayWhenProcessingSibling($q, 'job_general_assembly', $dateStr);
                break;
        }

        if ($statusName !== null && $statusName !== '') {
            $q->whereRaw('LOWER(TRIM(job_status)) = ?', [mb_strtolower(trim($statusName))]);
        }

        return (int) $q->count();
    }

    private static function countJobBph(string $bucket, string $which, ?string $date = null, ?string $statusName = null): int
    {
        $table = 'job_bph';
        if ($which === 'amt' && Schema::hasTable('job_amt')) {
            $table = 'job_amt';
        } elseif ($which === 'fyrs' && Schema::hasTable('job_fyrs')) {
            $table = 'job_fyrs';
        }

        if (! Schema::hasTable($table)) {
            return 0;
        }

        [$start, $end, $dateStr] = self::dayBoundsManila($date);

        $q = DB::table($table);

        if ($which === 'bluinq') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['bluinq01']);
        } elseif ($which === 'amt' && $table === 'job_bph') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['amt01']);
        } elseif ($which === 'fyrs' && $table === 'job_bph') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['fyrs01']);
        } elseif ($which === 'bph') {
            $q->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01']);
        }

        self::applyJobBphDayFilter($q, $bucket, $start, $end, $dateStr);

        self::applyExcludeDashboardTerminalStatuses($q, 'status');

        JobCountsScope::applyJobBphAssignment($q);

        self::applyChartStaffFilter($q, 'job_bph');

        switch ($bucket) {
            case 'total':
                break;
            case 'completed':
                $q->whereRaw('LOWER(TRIM(status)) = ?', ['completed']);
                break;
            case 'processing':
                $q->whereRaw('LOWER(TRIM(status)) = ?', ['processing']);
                break;
            case 'pending':
                $q->whereRaw("LOWER(TRIM(status)) NOT IN ('completed', 'processing')");
                break;
            case 'encoded_today':
                $q->whereRaw("LOWER(TRIM(status)) NOT IN ('completed', 'processing')");
                self::applyExcludeStaleEncodedTodayWhenProcessingSiblingBph($q, $table, $start, $end);
                break;
        }

        if ($statusName !== null && $statusName !== '') {
            $q->whereRaw('LOWER(TRIM(status)) = ?', [mb_strtolower(trim($statusName))]);
        }

        return (int) $q->count();
    }
}
