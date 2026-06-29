<?php

namespace App\Services;

use App\Models\RolePermission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Dashboard job counts (Philippine calendar):
 * - total: **completed + processing + pending** (sum of the three cards).
 * - completed / processing: **as of today** (log_date or completion_date).
 * - pending: **non-completed backlog** from **previous dates** (not logged today).
 */
class DashboardJobStatsService
{
    private const TZ = 'Asia/Manila';

    /** @return array{0:string,1:string,2:string} start, end, Y-m-d */
    private static function dayBoundsManila(): array
    {
        $start = Carbon::now(self::TZ)->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::now(self::TZ)->endOfDay()->format('Y-m-d H:i:s');
        $date = Carbon::now(self::TZ)->toDateString();

        return [$start, $end, $date];
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

        // processing: strict log_date today only
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
                $out['total'][$label] = $out['completed'][$label]
                    + $out['processing'][$label]
                    + $out['pending'][$label];
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

    private static function countJobsBranchBucket(string $branchLabel, string $bucket): int
    {
        $only = self::branchStatLabelExclusive();
        if ($only !== null && strcasecmp((string) $branchLabel, (string) $only) !== 0) {
            return 0;
        }

        return match ($branchLabel) {
            'LBS' => self::countJobsTable($bucket, 'lbs'),
            'LUNTIAN' => self::countJobsTable($bucket, 'luntian'),
            'EFFICIENT LIVING' => self::countJobsTable($bucket, 'efficient_living'),
            'BPH' => self::countJobBph($bucket, 'bph'),
            'BLUINQ' => self::countJobBph($bucket, 'bluinq'),
            'A&M' => self::countJobBph($bucket, 'amt'),
            'FYRS ENERGY WISE' => self::countJobBph($bucket, 'fyrs'),
            default => 0,
        };
    }

    private static function countJobsTable(string $bucket, string $productLine = 'lbs'): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        [$start, $end, $date] = self::dayBoundsManila();

        $q = DB::table('jobs')->where('reference', 'like', 'JOBS%');

        if ($productLine === 'efficient_living') {
            $q->whereRaw("job_request_id LIKE 'EA\_EL\_%'");
        } elseif ($productLine === 'luntian') {
            JobCountsScope::applyLuntianJobsScope($q, '');
        } else {
            JobCountsScope::applyLbsStandardJobsScope($q, '');
        }

        self::applyLbsPipelineExclusions($q, $productLine);

        self::applyJobsTableDayFilter($q, $bucket, $start, $end, $date);

        $q->whereRaw("LOWER(TRIM(job_status)) != ?", ['archived']);

        JobCountsScope::applyJobsTableAssignment($q);

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
                $q->whereRaw("LOWER(TRIM(job_status)) != ?", ['completed']);
                break;
        }

        return (int) $q->count();
    }

    private static function countJobBph(string $bucket, string $which): int
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

        [$start, $end, $date] = self::dayBoundsManila();

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

        self::applyJobBphDayFilter($q, $bucket, $start, $end, $date);

        $q->whereRaw("LOWER(TRIM(status)) != ?", ['archived']);

        JobCountsScope::applyJobBphAssignment($q);

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
                $q->whereRaw("LOWER(TRIM(status)) != ?", ['completed']);
                break;
        }

        return (int) $q->count();
    }
}
