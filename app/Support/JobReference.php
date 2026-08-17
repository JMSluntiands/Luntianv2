<?php

namespace App\Support;

class JobReference
{
    /**
     * Display Luntian job numbers as JOBYYMMDD (e.g. JOBS20260721104026 → JOB260721).
     */
    public static function luntianDisplay(?string $jobReferenceNo, ?string $internalReference = null): string
    {
        $value = trim((string) ($jobReferenceNo ?: $internalReference ?: ''));
        if ($value === '') {
            return '—';
        }

        if (preg_match('/^JOBS(\d{4})(\d{2})(\d{2})/i', $value, $m)) {
            return 'JOB'.substr($m[1], -2).$m[2].$m[3];
        }

        if (preg_match('/^JOB(\d{6})(?:\D|$)/i', $value, $m) && stripos($value, 'JOBS') !== 0) {
            return 'JOB'.$m[1];
        }

        return $value;
    }

    public static function luntianForDate(\DateTimeInterface $date): string
    {
        return 'JOB'.\Carbon\Carbon::parse($date)->timezone('Asia/Manila')->format('ymd');
    }
}
