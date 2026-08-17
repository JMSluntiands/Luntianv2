<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\JobActiveTimeService;

class ReportsController extends Controller
{
    /**
     * All job pipelines normalized into one row shape for reporting.
     *
     * String columns are forced to utf8mb4_unicode_ci so UNION ALL works across tables
     * that were migrated with different collations.
     */
    private static function unionAllJobsSql(string $utf8u): string
    {
        $parts = [];

        if (Schema::hasTable('jobs')) {
            $parts[] = "
                SELECT
                    j.job_id AS job_pk,
                    CONVERT('jobs' USING utf8mb4) COLLATE {$utf8u} AS source_table,
                    j.completion_date AS completion_date,
                    j.log_date AS started_at,
                    CONVERT(COALESCE(j.staff_id, j.checker_id) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                    CONVERT(COALESCE(j.staff_id, '') USING utf8mb4) COLLATE {$utf8u} AS staff_code,
                    CONVERT(COALESCE(j.checker_id, '') USING utf8mb4) COLLATE {$utf8u} AS checker_code,
                    CONVERT(COALESCE(j.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                    COALESCE(NULLIF(j.units, 0), j.plan_complexity, 0) AS units,
                    CONVERT(COALESCE(ca.client_account_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                    CASE
                        WHEN CONVERT(j.job_request_id USING utf8mb4) COLLATE {$utf8u} LIKE 'EA\\_EL\\_%' THEN CONVERT('Efficient Living' USING utf8mb4) COLLATE {$utf8u}
                        WHEN CONVERT(j.job_request_id USING utf8mb4) COLLATE {$utf8u} LIKE 'EA\\_LT\\_%' THEN CONVERT('LUNTIAN' USING utf8mb4) COLLATE {$utf8u}
                        ELSE CONVERT('LBS' USING utf8mb4) COLLATE {$utf8u}
                    END AS job_system
                FROM jobs j
                LEFT JOIN client_accounts ca ON ca.client_account_id = j.client_account_id
                WHERE j.reference LIKE 'JOBS%'
            ";
        }

        $bphFamily = [
            'job_bph' => null, // CASE for BluInq below
            'job_amt' => 'A&M',
            'job_fyrs' => 'FYRS ENERGYWISE',
            'job_csp' => 'CSP',
            'job_nh' => 'NH',
            'job_lc_home_builder' => 'LC Home Builder',
            'job_leading_energy' => 'Leading Energy',
        ];

        foreach ($bphFamily as $table => $fixedLabel) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $alias = substr($table, -3) === 'bph' ? 'b' : substr(str_replace('job_', '', $table), 0, 1);
            if ($table === 'job_bph') {
                $systemExpr = "
                    CASE
                        WHEN LOWER(TRIM(CONVERT({$alias}.client_code USING utf8mb4) COLLATE {$utf8u})) = 'bluinq01' THEN CONVERT('BluInq' USING utf8mb4) COLLATE {$utf8u}
                        ELSE CONVERT('BPH' USING utf8mb4) COLLATE {$utf8u}
                    END
                ";
                $alias = 'b';
            } elseif ($table === 'job_amt') {
                $alias = 'a';
                $systemExpr = "CONVERT('A&M' USING utf8mb4) COLLATE {$utf8u}";
            } elseif ($table === 'job_fyrs') {
                $alias = 'f';
                $systemExpr = "CONVERT('FYRS ENERGYWISE' USING utf8mb4) COLLATE {$utf8u}";
            } elseif ($table === 'job_csp') {
                $alias = 'c';
                $systemExpr = "CONVERT('CSP' USING utf8mb4) COLLATE {$utf8u}";
            } elseif ($table === 'job_nh') {
                $alias = 'n';
                $systemExpr = "CONVERT('NH' USING utf8mb4) COLLATE {$utf8u}";
            } elseif ($table === 'job_lc_home_builder') {
                $alias = 'l';
                $systemExpr = "CONVERT('LC Home Builder' USING utf8mb4) COLLATE {$utf8u}";
            } else {
                $alias = 'e';
                $systemExpr = "CONVERT('Leading Energy' USING utf8mb4) COLLATE {$utf8u}";
            }

            $startedCol = Schema::hasColumn($table, 'created_at') ? "{$alias}.created_at" : "{$alias}.date";

            $staffCol = Schema::hasColumn($table, 'assigned') ? "{$alias}.assigned" : 'NULL';
            $checkerCol = Schema::hasColumn($table, 'checked')
                ? "{$alias}.checked"
                : (Schema::hasColumn($table, 'checker') ? "{$alias}.checker" : 'NULL');

            $parts[] = "
                SELECT
                    {$alias}.id AS job_pk,
                    CONVERT('{$table}' USING utf8mb4) COLLATE {$utf8u} AS source_table,
                    {$alias}.date AS completion_date,
                    {$startedCol} AS started_at,
                    CONVERT(COALESCE({$staffCol}, {$checkerCol}) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                    CONVERT(COALESCE({$staffCol}, '') USING utf8mb4) COLLATE {$utf8u} AS staff_code,
                    CONVERT(COALESCE({$checkerCol}, '') USING utf8mb4) COLLATE {$utf8u} AS checker_code,
                    CONVERT(COALESCE({$alias}.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                    COALESCE({$alias}.units, 0) AS units,
                    CONVERT(COALESCE({$alias}.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                    {$systemExpr} AS job_system
                FROM {$table} {$alias}
            ";
        }

        if (empty($parts)) {
            return "
                SELECT
                    0 AS job_pk,
                    CONVERT('' USING utf8mb4) COLLATE {$utf8u} AS source_table,
                    NULL AS completion_date,
                    NULL AS started_at,
                    CONVERT('' USING utf8mb4) COLLATE {$utf8u} AS user_code,
                    CONVERT('' USING utf8mb4) COLLATE {$utf8u} AS staff_code,
                    CONVERT('' USING utf8mb4) COLLATE {$utf8u} AS checker_code,
                    CONVERT('' USING utf8mb4) COLLATE {$utf8u} AS job_type,
                    0 AS units,
                    CONVERT('' USING utf8mb4) COLLATE {$utf8u} AS client_label,
                    CONVERT('' USING utf8mb4) COLLATE {$utf8u} AS job_system
                WHERE 1=0
            ";
        }

        return implode("\nUNION ALL\n", $parts);
    }

    private static function appendClientSelectUnionPart(array &$parts, string $table, string $column, string $utf8u): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        $parts[] = "
            SELECT CONVERT({$column} USING utf8mb4) COLLATE {$utf8u} AS client_label
            FROM {$table}
            WHERE {$column} IS NOT NULL
        ";
    }

    /**
     * @return array{client: string, staff: string, dateFrom: string, dateTo: string, entries: int, filterClientRaw: string, filterStaffRaw: string}
     */
    private static function parseReportFilters(Request $request): array
    {
        $entries = (int) $request->query('entries', 200);
        if ($entries <= 0) {
            $entries = 25;
        }
        if ($entries > 200) {
            $entries = 200;
        }

        $filterClientRaw = trim((string) $request->query('client', 'all'));
        $client = strtolower($filterClientRaw);
        $client = ($client === '' || $client === 'all') ? '' : $filterClientRaw;

        $filterStaffRaw = trim((string) $request->query('staff', 'all'));
        $staffKey = strtolower($filterStaffRaw);
        $staff = ($staffKey === '' || $staffKey === 'all') ? '' : $filterStaffRaw;

        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));

        if ($dateFrom === '' && $dateTo === '') {
            $today = Carbon::today()->toDateString();
            $dateFrom = $today;
            $dateTo = $today;
        }

        return [
            'client' => $client,
            'staff' => $staff,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'entries' => $entries,
            'filterClientRaw' => $filterClientRaw,
            'filterStaffRaw' => $filterStaffRaw,
        ];
    }

    /**
     * @param  array{client: string, staff: string, dateFrom: string, dateTo: string}  $filters
     * @return array{0: string, 1: list<mixed>}
     */
    private static function buildWhereClause(array $filters): array
    {
        $where = 'u.completion_date IS NOT NULL';
        $filterParams = [];

        if ($filters['client'] !== '') {
            $where .= ' AND LOWER(TRIM(u.client_label)) = LOWER(?)';
            $filterParams[] = $filters['client'];
        }
        if ($filters['staff'] !== '') {
            $where .= ' AND LOWER(TRIM(u.user_code)) = LOWER(?)';
            $filterParams[] = $filters['staff'];
        }
        if ($filters['dateFrom'] !== '') {
            $where .= ' AND DATE(u.completion_date) >= ?';
            $filterParams[] = $filters['dateFrom'];
        }
        if ($filters['dateTo'] !== '') {
            $where .= ' AND DATE(u.completion_date) <= ?';
            $filterParams[] = $filters['dateTo'];
        }

        return [$where, $filterParams];
    }

    /**
     * @return array{client: string, staff: string, checker: string, jobType: string, dateFrom: string, dateTo: string}
     */
    private static function parseChartFilters(Request $request): array
    {
        $normalize = static function (string $raw): string {
            $raw = trim($raw);
            $key = strtolower($raw);

            return ($raw === '' || $key === 'all') ? '' : $raw;
        };

        $dateFrom = trim((string) $request->query('chart_date_from', $request->query('date_from', '')));
        $dateTo = trim((string) $request->query('chart_date_to', $request->query('date_to', '')));
        if ($dateFrom === '' && $dateTo === '') {
            $today = Carbon::today()->toDateString();
            $dateFrom = $today;
            $dateTo = $today;
        }

        return [
            'client' => $normalize((string) $request->query('chart_client', '')),
            'staff' => $normalize((string) $request->query('chart_staff', '')),
            'checker' => $normalize((string) $request->query('chart_checker', '')),
            'jobType' => $normalize((string) $request->query('chart_job_type', '')),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }

    /**
     * @param  array{client: string, staff: string, checker: string, jobType: string, dateFrom: string, dateTo: string}  $filters
     * @return array{0: string, 1: list<mixed>}
     */
    private static function buildChartWhereClause(array $filters): array
    {
        $where = 'u.completion_date IS NOT NULL';
        $params = [];

        if ($filters['client'] !== '') {
            $where .= ' AND LOWER(TRIM(u.client_label)) = LOWER(?)';
            $params[] = $filters['client'];
        }
        if ($filters['staff'] !== '') {
            $where .= ' AND LOWER(TRIM(u.staff_code)) = LOWER(?)';
            $params[] = $filters['staff'];
        }
        if ($filters['checker'] !== '') {
            $where .= ' AND LOWER(TRIM(u.checker_code)) = LOWER(?)';
            $params[] = $filters['checker'];
        }
        if ($filters['jobType'] !== '') {
            $where .= ' AND LOWER(TRIM(u.job_type)) = LOWER(?)';
            $params[] = $filters['jobType'];
        }
        if ($filters['dateFrom'] !== '') {
            $where .= ' AND DATE(u.completion_date) >= ?';
            $params[] = $filters['dateFrom'];
        }
        if ($filters['dateTo'] !== '') {
            $where .= ' AND DATE(u.completion_date) <= ?';
            $params[] = $filters['dateTo'];
        }

        return [$where, $params];
    }

    /**
     * @param  array{client: string, staff: string, checker: string, jobType: string, dateFrom: string, dateTo: string}  $filters
     * @return array{labels: list<string>, units: list<int>, jobs: list<int>, title: string}
     */
    private static function buildChartPayload(array $filters): array
    {
        $utf8u = 'utf8mb4_unicode_ci';
        $union = self::unionAllJobsSql($utf8u);
        [$where, $params] = self::buildChartWhereClause($filters);

        $sql = "
            SELECT
                DATE(u.completion_date) AS day_key,
                COUNT(*) AS job_count,
                SUM(COALESCE(u.units, 0)) AS units_sum
            FROM ({$union}) u
            WHERE {$where}
            GROUP BY DATE(u.completion_date)
            ORDER BY DATE(u.completion_date) ASC
        ";

        $byDay = [];
        try {
            foreach (DB::select($sql, $params) as $row) {
                $key = (string) ($row->day_key ?? '');
                if ($key === '') {
                    continue;
                }
                $byDay[$key] = [
                    'jobs' => (int) ($row->job_count ?? 0),
                    'units' => (int) ($row->units_sum ?? 0),
                ];
            }
        } catch (\Throwable $e) {
            $byDay = [];
        }

        $labels = [];
        $units = [];
        $jobs = [];

        try {
            $from = $filters['dateFrom'] !== '' ? Carbon::parse($filters['dateFrom'])->startOfDay() : null;
            $to = $filters['dateTo'] !== '' ? Carbon::parse($filters['dateTo'])->startOfDay() : $from;
        } catch (\Throwable $e) {
            $from = null;
            $to = null;
        }

        if ($from && $to && $from->lte($to) && $from->diffInDays($to) <= 90) {
            $cursor = $from->copy();
            while ($cursor->lte($to)) {
                $key = $cursor->toDateString();
                $labels[] = $cursor->format('M j');
                $units[] = (int) ($byDay[$key]['units'] ?? 0);
                $jobs[] = (int) ($byDay[$key]['jobs'] ?? 0);
                $cursor->addDay();
            }
        } else {
            foreach ($byDay as $key => $vals) {
                try {
                    $labels[] = Carbon::parse($key)->format('M j');
                } catch (\Throwable $e) {
                    $labels[] = $key;
                }
                $units[] = (int) ($vals['units'] ?? 0);
                $jobs[] = (int) ($vals['jobs'] ?? 0);
            }
        }

        return [
            'labels' => $labels,
            'units' => $units,
            'jobs' => $jobs,
            'title' => 'Units by day',
        ];
    }

    /**
     * @return list<string>
     */
    private static function distinctUnionColumn(string $column): array
    {
        $utf8u = 'utf8mb4_unicode_ci';
        $union = self::unionAllJobsSql($utf8u);
        try {
            $sql = "
                SELECT t.val AS val
                FROM (
                    SELECT DISTINCT TRIM(u.{$column}) AS val
                    FROM ({$union}) u
                    WHERE u.{$column} IS NOT NULL AND TRIM(u.{$column}) <> ''
                ) t
                WHERE t.val IS NOT NULL AND t.val <> ''
                ORDER BY t.val ASC
            ";

            return collect(DB::select($sql))
                ->pluck('val')
                ->map(fn ($v) => is_string($v) ? trim($v) : '')
                ->filter(fn ($v) => $v !== '')
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * @param  array{client: string, staff: string, dateFrom: string, dateTo: string}  $filters
     * @return \Illuminate\Support\Collection<int, object{completion_date: mixed, user_code: mixed, job_type: mixed, units: int, time_spent_seconds: int, time_spent: string}>
     */
    private static function fetchGroupedRows(array $filters, ?int $limit = null): \Illuminate\Support\Collection
    {
        $utf8u = 'utf8mb4_unicode_ci';
        $union = self::unionAllJobsSql($utf8u);
        [$where, $filterParams] = self::buildWhereClause($filters);

        $sqlJobs = "
            SELECT
                u.job_pk,
                u.source_table,
                DATE(u.completion_date) AS completion_date,
                u.completion_date AS completion_at,
                u.started_at,
                NULLIF(TRIM(u.user_code), '') AS user_code,
                u.job_type AS job_type,
                COALESCE(u.units, 0) AS unit_val
            FROM ({$union}) u
            WHERE {$where}
            ORDER BY DATE(u.completion_date) DESC, u.user_code ASC, u.job_type ASC
        ";

        $jobRows = DB::select($sqlJobs, $filterParams);
        $groups = [];

        foreach ($jobRows as $job) {
            $dateKey = (string) ($job->completion_date ?? '');
            $userKey = (string) ($job->user_code ?? '');
            $typeKey = (string) ($job->job_type ?? '');
            $groupKey = $dateKey."\0".$userKey."\0".$typeKey;

            $start = null;
            $end = null;
            try {
                if (! empty($job->started_at)) {
                    $start = Carbon::parse($job->started_at);
                }
            } catch (\Throwable $e) {
                $start = null;
            }
            try {
                if (! empty($job->completion_at)) {
                    $end = Carbon::parse($job->completion_at);
                    // Date-only completion stamps land at 00:00 — use end of day so duration is positive
                    if ($end->format('H:i:s') === '00:00:00') {
                        $end = $end->copy()->endOfDay();
                    }
                }
            } catch (\Throwable $e) {
                $end = null;
            }

            $secs = JobActiveTimeService::activeSeconds(
                (string) ($job->source_table ?? ''),
                (int) ($job->job_pk ?? 0),
                $start,
                $end
            );

            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'completion_date' => $job->completion_date,
                    'user_code' => $job->user_code,
                    'job_type' => $job->job_type,
                    'units' => 0,
                    'time_spent_seconds' => 0,
                ];
            }
            $groups[$groupKey]['units'] += (int) ($job->unit_val ?? 0);
            $groups[$groupKey]['time_spent_seconds'] += (int) ($secs ?? 0);
        }

        $collection = collect(array_values($groups))->map(function (array $g) {
            $secs = (int) ($g['time_spent_seconds'] ?? 0);

            return (object) [
                'completion_date' => $g['completion_date'],
                'user_code' => $g['user_code'],
                'job_type' => $g['job_type'],
                'units' => (int) ($g['units'] ?? 0),
                'time_spent_seconds' => $secs,
                'time_spent' => JobActiveTimeService::formatDuration($secs),
            ];
        });

        if ($limit !== null && $limit > 0) {
            $collection = $collection->take($limit)->values();
        }

        return $collection;
    }

    public function index(Request $request)
    {
        $utf8u = 'utf8mb4_unicode_ci';
        $filters = self::parseReportFilters($request);
        $union = self::unionAllJobsSql($utf8u);
        [$where, $filterParams] = self::buildWhereClause($filters);

        $rows = self::fetchGroupedRows($filters, $filters['entries']);

        $sqlSummary = "
            SELECT
                u.job_system AS job_system,
                COUNT(*) AS job_count,
                SUM(COALESCE(u.units, 0)) AS units_sum
            FROM ({$union}) u
            WHERE {$where}
            GROUP BY u.job_system
            ORDER BY u.job_system ASC
        ";
        $summaryRows = collect(DB::select($sqlSummary, $filterParams))->map(function ($r) {
            return (object) [
                'job_system' => trim((string) ($r->job_system ?? '')),
                'job_count' => (int) ($r->job_count ?? 0),
                'units_sum' => (int) ($r->units_sum ?? 0),
            ];
        });

        $totalJobsInFilter = (int) $summaryRows->sum('job_count');
        $totalUnitsInFilter = (int) $summaryRows->sum('units_sum');

        $clientLabels = collect();
        try {
            $clientUnionParts = [];
            if (Schema::hasTable('jobs')) {
                $clientUnionParts[] = "
                    SELECT CONVERT(ca.client_account_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM jobs j
                    LEFT JOIN client_accounts ca ON ca.client_account_id = j.client_account_id
                    WHERE j.reference LIKE 'JOBS%' AND ca.client_account_name IS NOT NULL
                ";
            }
            self::appendClientSelectUnionPart($clientUnionParts, 'job_bph', 'client_name', $utf8u);
            self::appendClientSelectUnionPart($clientUnionParts, 'job_amt', 'client_name', $utf8u);
            self::appendClientSelectUnionPart($clientUnionParts, 'job_fyrs', 'client_name', $utf8u);
            self::appendClientSelectUnionPart($clientUnionParts, 'job_csp', 'client_name', $utf8u);
            self::appendClientSelectUnionPart($clientUnionParts, 'job_nh', 'client_name', $utf8u);
            self::appendClientSelectUnionPart($clientUnionParts, 'job_lc_home_builder', 'client_name', $utf8u);
            self::appendClientSelectUnionPart($clientUnionParts, 'job_leading_energy', 'client_name', $utf8u);

            if (!empty($clientUnionParts)) {
                $clientUnionSql = implode("\nUNION\n", $clientUnionParts);
                $clientLabels = collect(DB::select("
                    SELECT client_label
                    FROM ({$clientUnionSql}) x
                    GROUP BY client_label
                    ORDER BY client_label ASC
                "));
            }
        } catch (\Throwable $e) {
            $clientLabels = collect();
        }

        $clientOptions = $clientLabels
            ->pluck('client_label')
            ->filter(fn ($v) => is_string($v) && trim($v) !== '')
            ->values()
            ->all();

        $staffOptions = [];
        try {
            $staffSql = "
                SELECT t.user_code AS user_code
                FROM (
                    SELECT DISTINCT TRIM(u.user_code) AS user_code
                    FROM ({$union}) u
                    WHERE u.user_code IS NOT NULL AND TRIM(u.user_code) <> ''
                ) t
                WHERE t.user_code IS NOT NULL AND t.user_code <> ''
                ORDER BY t.user_code ASC
            ";
            $staffOptions = collect(DB::select($staffSql))
                ->pluck('user_code')
                ->map(fn ($v) => is_string($v) ? trim($v) : '')
                ->filter(fn ($v) => $v !== '')
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            $staffOptions = [];
        }

        $summaryByLabel = $summaryRows->keyBy('job_system');
        $chartFilters = self::parseChartFilters($request);
        $checkerOptions = self::distinctUnionColumn('checker_code');
        $jobTypeOptions = self::distinctUnionColumn('job_type');

        return view('reports.index', [
            'sidebar_active' => 'reports',
            'rows' => $rows,
            'recordsCount' => $rows->count(),
            'clientOptions' => $clientOptions,
            'summaryByLabel' => $summaryByLabel,
            'totalJobsInFilter' => $totalJobsInFilter,
            'totalUnitsInFilter' => $totalUnitsInFilter,
            'filterDateFrom' => $filters['dateFrom'],
            'filterDateTo' => $filters['dateTo'],
            'filterEntries' => $filters['entries'],
            'filterClient' => $filters['filterClientRaw'] === '' ? 'all' : $filters['filterClientRaw'],
            'staffOptions' => $staffOptions,
            'filterStaff' => $filters['filterStaffRaw'] === '' ? 'all' : $filters['filterStaffRaw'],
            'checkerOptions' => $checkerOptions,
            'jobTypeOptions' => $jobTypeOptions,
            'chartPayload' => self::buildChartPayload($chartFilters),
            'chartFilterClient' => $chartFilters['client'] === '' ? 'all' : $chartFilters['client'],
            'chartFilterStaff' => $chartFilters['staff'] === '' ? 'all' : $chartFilters['staff'],
            'chartFilterChecker' => $chartFilters['checker'] === '' ? 'all' : $chartFilters['checker'],
            'chartFilterJobType' => $chartFilters['jobType'] === '' ? 'all' : $chartFilters['jobType'],
            'chartDateFrom' => $chartFilters['dateFrom'],
            'chartDateTo' => $chartFilters['dateTo'],
        ]);
    }

    public function chart(Request $request)
    {
        $filters = self::parseChartFilters($request);

        return response()->json(self::buildChartPayload($filters));
    }

    /**
     * Download the filtered report rows as an Excel-compatible CSV.
     */
    public function exportExcel(Request $request)
    {
        $filters = self::parseReportFilters($request);
        $rows = self::fetchGroupedRows($filters, 50000);

        $filename = 'reports-' . now('Asia/Manila')->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($rows) {
            echo "\xEF\xBB\xBF";
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<thead><tr>';
            foreach (['Date Completion', 'User', 'Job Type', 'Total Units', 'Time Spent'] as $header) {
                echo '<th>' . htmlspecialchars($header, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($rows as $row) {
                $cd = $row->completion_date ?? null;
                $cdText = $cd ? Carbon::parse($cd)->format('M d, Y') : '';
                $user = ($row->user_code !== null && trim((string) $row->user_code) !== '') ? (string) $row->user_code : '';
                $jobType = (string) ($row->job_type ?? '');
                $units = (int) ($row->units ?? 0);
                $timeSpent = (string) ($row->time_spent ?? JobActiveTimeService::formatDuration((int) ($row->time_spent_seconds ?? 0)));

                echo '<tr>';
                echo '<td>' . htmlspecialchars($cdText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($user, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($jobType, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
                echo '<td>' . $units . '</td>';
                echo '<td>' . htmlspecialchars($timeSpent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }
}
