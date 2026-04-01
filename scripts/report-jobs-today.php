<?php

/**
 * One-off report: LBS pipeline jobs vs Philippine "today".
 * Run: php scripts/report-jobs-today.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\DashboardJobStatsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tz = 'Asia/Manila';
$start = Carbon::now($tz)->startOfDay()->format('Y-m-d H:i:s');
$end = Carbon::now($tz)->endOfDay()->format('Y-m-d H:i:s');
$date = Carbon::now($tz)->toDateString();

echo "=== Philippine today: {$date} ===\n";
echo "Window: {$start} .. {$end}\n\n";

if (! Schema::hasTable('jobs')) {
    echo "No `jobs` table.\n";
    exit(1);
}

$stats = DashboardJobStatsService::fetch();
echo "Dashboard service (LBS column — same as cards):\n";
foreach (['total', 'completed', 'processing', 'pending'] as $b) {
    echo sprintf("  %-12s %d\n", $b . ':', (int) ($stats[$b]['LBS'] ?? 0));
}

$ref = new ReflectionClass(DashboardJobStatsService::class);
$method = $ref->getMethod('countJobsTable');
$method->setAccessible(true);
echo "\nDirect countJobsTable(LBS, bucket) via reflection:\n";
foreach (['total', 'completed', 'processing', 'pending'] as $b) {
    echo sprintf("  %-12s %d\n", $b . ':', (int) $method->invoke(null, $b, false));
}

$lbsBase = DB::table('jobs')
    ->where('reference', 'like', 'JOBS%')
    ->where(function ($w) {
        $w->whereRaw("job_request_id NOT LIKE 'EA\\_EL\\_%'")
            ->orWhereNull('job_request_id')
            ->orWhere('job_request_id', '');
    });

echo "\n--- LBS pipeline: rows with last_update in today's window (any status) ---\n";
$byStatus = (clone $lbsBase)
    ->whereBetween('last_update', [$start, $end])
    ->selectRaw('job_status, COUNT(*) as c')
    ->groupBy('job_status')
    ->orderByDesc('c')
    ->get();
foreach ($byStatus as $row) {
    echo sprintf("  %-40s %d\n", (string) $row->job_status, (int) $row->c);
}
echo '  TOTAL: ' . (clone $lbsBase)->whereBetween('last_update', [$start, $end])->count() . "\n";

echo "\n--- LBS pipeline: created today (log_date prefix = {$date}) ---\n";
$createdToday = (clone $lbsBase)
    ->whereRaw('SUBSTRING(NULLIF(TRIM(log_date), \'\'), 1, 10) = ?', [$date])
    ->whereRaw("LOWER(TRIM(job_status)) != ?", ['archived'])
    ->count();
echo "  count: {$createdToday}\n";

echo "\n--- LBS pipeline: completed today (status completed + completion_date or fallback last_update) ---\n";
$completed = (clone $lbsBase)
    ->whereRaw('LOWER(TRIM(job_status)) = ?', ['completed'])
    ->where(function ($w) use ($start, $end) {
        $w->whereBetween('completion_date', [$start, $end])
            ->orWhere(function ($w2) use ($start, $end) {
                $w2->whereNull('completion_date')
                    ->whereBetween('last_update', [$start, $end]);
            });
    })
    ->count();
echo "  count (full service logic): {$completed}\n";

$c2 = (clone $lbsBase)
    ->whereRaw('LOWER(TRIM(job_status)) = ?', ['completed'])
    ->whereBetween('last_update', [$start, $end])
    ->count();
echo "  completed + last_update today only: {$c2}\n";

$cProc = (clone $lbsBase)
    ->whereBetween('last_update', [$start, $end])
    ->whereRaw("LOWER(TRIM(job_status)) NOT IN ('for review', 'for email confirmation', 'completed', 'archived')")
    ->whereNotNull('job_status')
    ->whereRaw('TRIM(job_status) != ?', [''])
    ->count();
echo "  processing (definition): {$cProc}\n";

$cPen = (clone $lbsBase)
    ->whereBetween('last_update', [$start, $end])
    ->whereRaw("LOWER(TRIM(job_status)) IN ('for review', 'for email confirmation')")
    ->count();
echo "  pending (definition): {$cPen}\n";

echo "\nSQL (copy-paste check):\n";
echo DB::table('jobs')
    ->where('reference', 'like', 'JOBS%')
    ->where(function ($w) {
        $w->whereRaw("job_request_id NOT LIKE 'EA\\_EL\\_%'")
            ->orWhereNull('job_request_id')
            ->orWhere('job_request_id', '');
    })
    ->whereRaw('LOWER(TRIM(job_status)) = ?', ['completed'])
    ->whereBetween('last_update', [$start, $end])
    ->toSql() . "\n";

echo "\nDone.\n";
