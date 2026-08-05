<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Active work time from Allocated → Completed, pausing while status is On Hold.
 */
class JobActiveTimeService
{
    public static function isHoldStatus(?string $status): bool
    {
        $k = strtolower(trim((string) $status));
        $k = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $k) ?? $k;
        $k = preg_replace('/\s+/u', ' ', trim($k)) ?? $k;

        return in_array($k, ['on hold', 'on-hold', 'on_hold', 'hold'], true);
    }

    public static function isAllocatedStatus(?string $status): bool
    {
        return strcasecmp(trim((string) $status), 'Allocated') === 0;
    }

    public static function isCompletedStatus(?string $status): bool
    {
        return strcasecmp(trim((string) $status), 'Completed') === 0;
    }

    public static function record(
        string $sourceTable,
        int $jobId,
        ?string $fromStatus,
        string $toStatus,
        ?Carbon $changedAt = null,
        ?string $changedBy = null
    ): void {
        if ($jobId <= 0 || $sourceTable === '' || ! Schema::hasTable('job_status_transitions')) {
            return;
        }

        $from = $fromStatus !== null ? trim($fromStatus) : null;
        $to = trim($toStatus);
        if ($to === '' || ($from !== null && strcasecmp((string) $from, $to) === 0)) {
            return;
        }

        try {
            DB::table('job_status_transitions')->insert([
                'source_table' => $sourceTable,
                'job_id' => $jobId,
                'from_status' => $from !== '' ? $from : null,
                'to_status' => $to,
                'changed_at' => ($changedAt ?? now('Asia/Manila'))->format('Y-m-d H:i:s'),
                'changed_by' => $changedBy,
            ]);
        } catch (\Throwable $e) {
            // Reporting must not break job updates
        }
    }

    /**
     * Active seconds from first Allocated (or fallback start) until Completed (or fallback end),
     * excluding On Hold intervals.
     */
    public static function activeSeconds(
        string $sourceTable,
        int $jobId,
        ?Carbon $fallbackStart,
        ?Carbon $fallbackEnd
    ): ?int {
        if ($fallbackEnd === null) {
            return null;
        }

        $events = self::timelineEvents($sourceTable, $jobId);
        $start = null;
        $end = null;
        $holdRanges = [];
        $holdOpen = null;

        foreach ($events as $ev) {
            $at = $ev['at'];
            $to = $ev['to'];

            if ($start === null && self::isAllocatedStatus($to)) {
                $start = $at->copy();
            }
            if (self::isHoldStatus($to)) {
                if ($holdOpen === null) {
                    $holdOpen = $at->copy();
                }
            } elseif ($holdOpen !== null) {
                $holdRanges[] = [$holdOpen->copy(), $at->copy()];
                $holdOpen = null;
            }
            if (self::isCompletedStatus($to)) {
                $end = $at->copy();
            }
        }

        if ($start === null) {
            $start = $fallbackStart?->copy();
        }
        if ($end === null) {
            $end = $fallbackEnd->copy();
        }
        if ($start === null || $end === null || $end->lte($start)) {
            return $end !== null && $start !== null ? 0 : null;
        }

        // If still on hold at completion, close hold at end
        if ($holdOpen !== null && $holdOpen->lt($end)) {
            $holdRanges[] = [$holdOpen->copy(), $end->copy()];
        }

        $total = max(0, $start->diffInSeconds($end));
        foreach ($holdRanges as [$hFrom, $hTo]) {
            $from = $hFrom->lt($start) ? $start->copy() : $hFrom->copy();
            $to = $hTo->gt($end) ? $end->copy() : $hTo->copy();
            if ($to->gt($from)) {
                $total -= $from->diffInSeconds($to);
            }
        }

        return (int) max(0, $total);
    }

    /**
     * @return list<array{at: Carbon, from: ?string, to: string}>
     */
    private static function timelineEvents(string $sourceTable, int $jobId): array
    {
        $events = [];

        if (Schema::hasTable('job_status_transitions')) {
            $rows = DB::table('job_status_transitions')
                ->where('source_table', $sourceTable)
                ->where('job_id', $jobId)
                ->orderBy('changed_at')
                ->orderBy('id')
                ->get(['from_status', 'to_status', 'changed_at']);

            foreach ($rows as $row) {
                try {
                    $events[] = [
                        'at' => Carbon::parse($row->changed_at),
                        'from' => $row->from_status !== null ? (string) $row->from_status : null,
                        'to' => (string) $row->to_status,
                    ];
                } catch (\Throwable $e) {
                    // skip bad row
                }
            }
        }

        if ($events === [] && $sourceTable === 'jobs' && Schema::hasTable('activity_log')) {
            $logs = DB::table('activity_log')
                ->where('job_id', $jobId)
                ->orderBy('activity_date')
                ->orderBy('log_id')
                ->get(['activity_date', 'activity_description']);

            foreach ($logs as $log) {
                $parsed = self::parseStatusChangeFromDescription((string) ($log->activity_description ?? ''));
                if ($parsed === null) {
                    continue;
                }
                try {
                    $events[] = [
                        'at' => Carbon::parse($log->activity_date),
                        'from' => $parsed[0],
                        'to' => $parsed[1],
                    ];
                } catch (\Throwable $e) {
                    // skip
                }
            }
        }

        usort($events, static fn ($a, $b) => $a['at']->getTimestamp() <=> $b['at']->getTimestamp());

        return $events;
    }

    /**
     * @return array{0: ?string, 1: string}|null
     */
    private static function parseStatusChangeFromDescription(string $description): ?array
    {
        $plain = html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (preg_match('/Job\s*Status\s*:\s*(.+?)\s*→\s*(.+?)(?:\n|$)/iu', $plain, $m)) {
            return [trim($m[1]), trim($m[2])];
        }
        if (preg_match('/Status\s*:\s*(.+?)\s*→\s*(.+?)(?:\n|$)/iu', $plain, $m)) {
            return [trim($m[1]), trim($m[2])];
        }
        if (preg_match('/Status updated from\s+(.+?)\s+to\s+(.+?)(?:\n|$|\.)/iu', $plain, $m)) {
            return [trim($m[1]), trim($m[2])];
        }

        return null;
    }

    public static function formatDuration(?int $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }
        $seconds = max(0, $seconds);
        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $mins = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            return sprintf('%dd %dh %dm', $days, $hours, $mins);
        }
        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $mins);
        }

        return sprintf('%dm', $mins);
    }
}
