<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$stats = App\Services\DashboardJobStatsService::fetch();
$chart = App\Services\DashboardJobStatsService::fetchStatusChart();

echo "TOTAL JOBS CARD:\n";
$sum = 0;
foreach ($stats['total'] as $k => $v) {
    echo "  {$k}: {$v}\n";
    $sum += $v;
}
echo "  SUM: {$sum}\n\n";

echo "CHART:\n";
$chartSum = 0;
foreach ($chart['branches'] as $b) {
    echo "  {$b['label']}: {$b['total']}\n";
    $segSum = 0;
    foreach ($b['statuses'] as $s) {
        echo "    - {$s['label']}: {$s['count']}\n";
        $segSum += (int) $s['count'];
    }
    if ($segSum !== (int) $b['total']) {
        echo "    !! segment sum {$segSum} != total {$b['total']}\n";
    }
    if (($b['label'] ?? '') === 'All') {
        $chartSum = (int) $b['total'];
    }
}
echo "\nAll card sum: {$sum}\n";
echo "All chart total: {$chartSum}\n";
if ($sum !== $chartSum) {
    echo "!! MISMATCH card sum vs chart All total\n";
}
