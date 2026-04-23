<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        return "
            SELECT
                j.completion_date AS completion_date,
                CONVERT(COALESCE(j.staff_id, j.checker_id) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(j.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(NULLIF(j.units, 0), j.plan_complexity, 0) AS units,
                CONVERT(COALESCE(ca.client_account_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CASE
                    WHEN CONVERT(j.job_request_id USING utf8mb4) COLLATE {$utf8u} LIKE 'EA\\_EL\\_%' THEN CONVERT('EFFICIENT LIVING' USING utf8mb4) COLLATE {$utf8u}
                    ELSE CONVERT('LBS' USING utf8mb4) COLLATE {$utf8u}
                END AS job_system
            FROM jobs j
            LEFT JOIN client_accounts ca ON ca.client_account_id = j.client_account_id
            WHERE j.reference LIKE 'JOBS%'

            UNION ALL

            SELECT
                b.date AS completion_date,
                CONVERT(COALESCE(b.assigned, b.checked) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(b.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(b.units, 0) AS units,
                CONVERT(COALESCE(b.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CASE
                    WHEN LOWER(TRIM(CONVERT(b.client_code USING utf8mb4) COLLATE {$utf8u})) = 'bluinq01' THEN CONVERT('BLUINQ' USING utf8mb4) COLLATE {$utf8u}
                    ELSE CONVERT('BPH' USING utf8mb4) COLLATE {$utf8u}
                END AS job_system
            FROM job_bph b

            UNION ALL

            SELECT
                a.date AS completion_date,
                CONVERT(COALESCE(a.assigned, a.checked) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(a.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(a.units, 0) AS units,
                CONVERT(COALESCE(a.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CONVERT('A&M' USING utf8mb4) COLLATE {$utf8u} AS job_system
            FROM job_amt a

            UNION ALL

            SELECT
                f.date AS completion_date,
                CONVERT(COALESCE(f.assigned, f.checked) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(f.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(f.units, 0) AS units,
                CONVERT(COALESCE(f.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CONVERT('FYRS ENERGY WISE' USING utf8mb4) COLLATE {$utf8u} AS job_system
            FROM job_fyrs f

            UNION ALL

            SELECT
                c.date AS completion_date,
                CONVERT(COALESCE(c.assigned, c.checked) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(c.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(c.units, 0) AS units,
                CONVERT(COALESCE(c.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CONVERT('CSP' USING utf8mb4) COLLATE {$utf8u} AS job_system
            FROM job_csp c

            UNION ALL

            SELECT
                n.date AS completion_date,
                CONVERT(COALESCE(n.assigned, n.checked) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(n.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(n.units, 0) AS units,
                CONVERT(COALESCE(n.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CONVERT('NH' USING utf8mb4) COLLATE {$utf8u} AS job_system
            FROM job_nh n

            UNION ALL

            SELECT
                l.date AS completion_date,
                CONVERT(COALESCE(l.assigned, l.checked) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(l.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(l.units, 0) AS units,
                CONVERT(COALESCE(l.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CONVERT('LC HOME BUILDER' USING utf8mb4) COLLATE {$utf8u} AS job_system
            FROM job_lc_home_builder l

            UNION ALL

            SELECT
                e.date AS completion_date,
                CONVERT(COALESCE(e.assigned, e.checked) USING utf8mb4) COLLATE {$utf8u} AS user_code,
                CONVERT(COALESCE(e.job_type, '') USING utf8mb4) COLLATE {$utf8u} AS job_type,
                COALESCE(e.units, 0) AS units,
                CONVERT(COALESCE(e.client_name, '') USING utf8mb4) COLLATE {$utf8u} AS client_label,
                CONVERT('LEADING ENERGY' USING utf8mb4) COLLATE {$utf8u} AS job_system
            FROM job_leading_energy e
        ";
    }

    public function index(Request $request)
    {
        $utf8u = 'utf8mb4_unicode_ci';

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

        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));

        if ($dateFrom === '' && $dateTo === '') {
            $today = Carbon::today()->toDateString();
            $dateFrom = $today;
            $dateTo = $today;
        }

        $union = self::unionAllJobsSql($utf8u);

        $where = 'u.completion_date IS NOT NULL';
        $filterParams = [];

        if ($client !== '') {
            $where .= ' AND LOWER(TRIM(u.client_label)) = LOWER(?)';
            $filterParams[] = $client;
        }
        if ($dateFrom !== '') {
            $where .= ' AND DATE(u.completion_date) >= ?';
            $filterParams[] = $dateFrom;
        }
        if ($dateTo !== '') {
            $where .= ' AND DATE(u.completion_date) <= ?';
            $filterParams[] = $dateTo;
        }

        // One row per calendar completion day + distinct job type; units summed across all matching jobs.
        $sqlGrouped = "
            SELECT
                g.completion_date,
                g.user_code,
                g.job_type,
                g.units
            FROM (
                SELECT
                    DATE(u.completion_date) AS completion_date,
                    GROUP_CONCAT(DISTINCT NULLIF(TRIM(u.user_code), '') ORDER BY u.user_code SEPARATOR ', ') AS user_code,
                    u.job_type AS job_type,
                    SUM(COALESCE(u.units, 0)) AS units
                FROM ({$union}) u
                WHERE {$where}
                GROUP BY DATE(u.completion_date), u.job_type
            ) g
            ORDER BY g.completion_date DESC, g.job_type ASC
            LIMIT ?
        ";

        $groupedParams = array_merge($filterParams, [$entries]);
        $rows = collect(DB::select($sqlGrouped, $groupedParams))->map(function ($r) {
            return (object) [
                'completion_date' => $r->completion_date,
                'user_code' => $r->user_code,
                'job_type' => $r->job_type,
                'units' => (int) ($r->units ?? 0),
            ];
        });

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
            $clientLabels = collect(DB::select("
                SELECT client_label FROM (
                    SELECT CONVERT(ca.client_account_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM jobs j
                    LEFT JOIN client_accounts ca ON ca.client_account_id = j.client_account_id
                    WHERE j.reference LIKE 'JOBS%' AND ca.client_account_name IS NOT NULL

                    UNION

                    SELECT CONVERT(b.client_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM job_bph b
                    WHERE b.client_name IS NOT NULL

                    UNION

                    SELECT CONVERT(a.client_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM job_amt a
                    WHERE a.client_name IS NOT NULL

                    UNION

                    SELECT CONVERT(f.client_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM job_fyrs f
                    WHERE f.client_name IS NOT NULL

                    UNION

                    SELECT CONVERT(c.client_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM job_csp c
                    WHERE c.client_name IS NOT NULL

                    UNION

                    SELECT CONVERT(n.client_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM job_nh n
                    WHERE n.client_name IS NOT NULL

                    UNION

                    SELECT CONVERT(l.client_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM job_lc_home_builder l
                    WHERE l.client_name IS NOT NULL

                    UNION

                    SELECT CONVERT(e.client_name USING utf8mb4) COLLATE {$utf8u} AS client_label
                    FROM job_leading_energy e
                    WHERE e.client_name IS NOT NULL
                ) x
                GROUP BY client_label
                ORDER BY client_label ASC
            "));
        } catch (\Throwable $e) {
            $clientLabels = collect();
        }

        $clientOptions = $clientLabels
            ->pluck('client_label')
            ->filter(fn ($v) => is_string($v) && trim($v) !== '')
            ->values()
            ->all();

        $summaryByLabel = $summaryRows->keyBy('job_system');

        return view('reports.index', [
            'sidebar_active' => 'reports',
            'rows' => $rows,
            'recordsCount' => $rows->count(),
            'clientOptions' => $clientOptions,
            'summaryByLabel' => $summaryByLabel,
            'totalJobsInFilter' => $totalJobsInFilter,
            'totalUnitsInFilter' => $totalUnitsInFilter,
            'filterDateFrom' => $dateFrom,
            'filterDateTo' => $dateTo,
            'filterEntries' => $entries,
            'filterClient' => $filterClientRaw === '' ? 'all' : $filterClientRaw,
        ]);
    }
}
