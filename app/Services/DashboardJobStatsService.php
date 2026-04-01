<?php

namespace App\Services;

use App\Models\RolePermission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Dashboard job counts: all buckets use jobs **created / logged today** (Philippine calendar).
 * Sub-counts split that cohort by current status (case-insensitive).
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
                $out['total'][$label] = self::countJobsBranchBucket($label, 'total');
                $out['completed'][$label] = self::countJobsBranchBucket($label, 'completed');
                $out['processing'][$label] = self::countJobsBranchBucket($label, 'processing');
                $out['pending'][$label] = self::countJobsBranchBucket($label, 'pending');
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

    private static function countJobsBranchBucket(string $branchLabel, string $bucket): int
    {
        return match ($branchLabel) {
            'LBS' => self::countJobsTable($bucket, false),
            'EFFICIENT LIVING' => self::countJobsTable($bucket, true),
            'BPH' => self::countJobBph($bucket, 'bph'),
            'BLUINQ' => self::countJobBph($bucket, 'bluinq'),
            default => 0,
        };
    }

    /** Base: JOBS%, branch split; every bucket restricted to log_date calendar day = today (Manila). */
    private static function countJobsTable(string $bucket, bool $efficientLiving): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        [, , $date] = self::dayBoundsManila();

        $q = DB::table('jobs')->where('reference', 'like', 'JOBS%');

        if ($efficientLiving) {
            $q->whereRaw("job_request_id LIKE 'EA\_EL\_%'");
        } else {
            $q->where(function ($w) {
                $w->whereRaw("job_request_id NOT LIKE 'EA\_EL\_%'")
                    ->orWhereNull('job_request_id')
                    ->orWhere('job_request_id', '');
            });
        }

        $q->whereRaw('SUBSTRING(NULLIF(TRIM(log_date), \'\'), 1, 10) = ?', [$date])
            ->whereRaw("LOWER(TRIM(job_status)) != ?", ['archived']);

        switch ($bucket) {
            case 'total':
                break;
            case 'completed':
                $q->whereRaw('LOWER(TRIM(job_status)) = ?', ['completed']);
                break;
            case 'processing':
                $q->whereRaw("LOWER(TRIM(job_status)) NOT IN ('for review', 'for email confirmation', 'completed', 'archived')")
                    ->whereNotNull('job_status')
                    ->whereRaw('TRIM(job_status) != ?', ['']);
                break;
            case 'pending':
                $q->whereRaw("LOWER(TRIM(job_status)) IN ('for review', 'for email confirmation')");
                break;
        }

        return (int) $q->count();
    }

    /** Every bucket: created_at today (Manila window); then status filter for sub-cards. */
    private static function countJobBph(string $bucket, string $which): int
    {
        if (! Schema::hasTable('job_bph')) {
            return 0;
        }

        [$start, $end] = self::dayBoundsManila();

        $q = DB::table('job_bph');

        if ($which === 'bluinq') {
            $q->whereRaw('LOWER(TRIM(client_code)) = ?', ['bluinq01']);
        } else {
            $q->whereRaw("LOWER(TRIM(COALESCE(client_code, ''))) != ?", ['bluinq01']);
        }

        $q->whereBetween('created_at', [$start, $end])
            ->whereRaw("LOWER(TRIM(status)) != ?", ['archived']);

        switch ($bucket) {
            case 'total':
                break;
            case 'completed':
                $q->whereRaw('LOWER(TRIM(status)) = ?', ['completed']);
                break;
            case 'processing':
                $q->whereRaw("LOWER(TRIM(status)) NOT IN ('for review', 'for email confirmation', 'completed', 'archived')")
                    ->whereNotNull('status')
                    ->whereRaw('TRIM(status) != ?', ['']);
                break;
            case 'pending':
                $q->whereRaw("LOWER(TRIM(status)) IN ('for review', 'for email confirmation')");
                break;
        }

        return (int) $q->count();
    }
}
