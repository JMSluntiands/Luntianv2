<?php

namespace App\Support;

/**
 * Linear LBS / Efficient Living job status progression (one step per update).
 */
final class LbsJobStatusFlow
{
    /**
     * Strip zero-width / BOM-like characters so DB values still match the flow
     * (e.g. "Processing\u{200B}" would otherwise break array_search).
     */
    public static function normalizeStatusKey(?string $status): string
    {
        $s = trim((string) $status);
        if ($s === '') {
            return '';
        }
        $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s) ?? $s;

        return trim($s);
    }

    /** Normalised lowercase keys in order */
    public const ORDER = [
        'allocated',
        'accepted',
        'processing',
        'for checking',
        'for review',
        'for email confirmation',
        'completed',
    ];

    /** Default labels when resolving from `statuses` master fails */
    private const LABEL_BY_LOWER = [
        'allocated' => 'Allocated',
        'accepted' => 'Accepted',
        'processing' => 'Processing',
        'for checking' => 'For Checking',
        'for review' => 'For Review',
        'for email confirmation' => 'For Email Confirmation',
        'completed' => 'Completed',
    ];

    public static function describeFlow(): string
    {
        $parts = [];
        foreach (self::ORDER as $k) {
            $parts[] = self::LABEL_BY_LOWER[$k] ?? $k;
        }

        return implode(' → ', $parts);
    }

    public static function indexInOrder(?string $status): ?int
    {
        $k = strtolower(self::normalizeStatusKey($status));
        if ($k === '') {
            return null;
        }
        $i = array_search($k, self::ORDER, true);

        return $i === false ? null : (int) $i;
    }

    /**
     * Next step key (lowercase), or null if none (terminal / unknown), or for `Revised` → back to For Checking.
     */
    public static function nextAllowedLower(?string $currentJobStatus): ?string
    {
        $curr = strtolower(self::normalizeStatusKey($currentJobStatus));
        if ($curr === 'revised') {
            return 'for checking';
        }
        $idx = self::indexInOrder($curr);
        if ($idx === null) {
            return null;
        }
        if ($idx >= count(self::ORDER) - 1) {
            return null;
        }

        return self::ORDER[$idx + 1];
    }

    /**
     * True if $to is the same as $from (caller may skip) or exactly one forward step in the workflow.
     * On Hold may be entered/left outside the linear flow (timer pauses while on hold).
     */
    public static function isValidTransition(?string $from, ?string $to): bool
    {
        $fromTrim = self::normalizeStatusKey($from);
        $toTrim = self::normalizeStatusKey($to);
        if ($toTrim === '') {
            return false;
        }
        if (strcasecmp($fromTrim, $toTrim) === 0) {
            return true;
        }

        if (\App\Services\JobActiveTimeService::isHoldStatus($toTrim)) {
            $fromLower = strtolower($fromTrim);

            return ! in_array($fromLower, ['completed', 'cancelled', 'archived'], true);
        }

        if (\App\Services\JobActiveTimeService::isHoldStatus($fromTrim)) {
            // Resume from On Hold to any normal workflow status (or Revised).
            if (strcasecmp($toTrim, 'Revised') === 0) {
                return true;
            }

            return self::indexInOrder($toTrim) !== null;
        }

        $nextLower = self::nextAllowedLower($fromTrim);
        if ($nextLower === null) {
            return false;
        }

        return strcasecmp($toTrim, $nextLower) === 0;
    }

    /**
     * @param iterable<int, object|string> $statuses Status models with `name`, or compatible
     * @return list<string>
     */
    public static function nextAllowedLabels(?string $currentJobStatus, iterable $statuses): array
    {
        $curr = self::normalizeStatusKey($currentJobStatus);
        $labels = [];

        if (\App\Services\JobActiveTimeService::isHoldStatus($curr)) {
            foreach (self::ORDER as $k) {
                $resolved = self::resolveNameFromMaster($k, $statuses);
                $labels[] = $resolved ?? (self::LABEL_BY_LOWER[$k] ?? $k);
            }
            $revised = self::resolveNameFromMaster('revised', $statuses);
            if ($revised !== null) {
                $labels[] = $revised;
            } else {
                $labels[] = 'Revised';
            }

            return array_values(array_unique($labels));
        }

        $nextLower = self::nextAllowedLower($currentJobStatus);
        if ($nextLower !== null) {
            $resolved = self::resolveNameFromMaster($nextLower, $statuses);
            if ($resolved !== null) {
                $labels[] = $resolved;
            } else {
                $fallback = self::LABEL_BY_LOWER[$nextLower] ?? null;
                if ($fallback !== null) {
                    $labels[] = $fallback;
                }
            }
        }

        $currLower = strtolower($curr);
        if ($curr !== '' && ! in_array($currLower, ['completed', 'cancelled', 'archived'], true)) {
            $onHold = self::resolveNameFromMaster('on hold', $statuses)
                ?? self::resolveNameFromMaster('on-hold', $statuses);
            $labels[] = $onHold ?? 'On Hold';
        }

        return array_values(array_unique($labels));
    }

    /**
     * @param iterable<int, object|string> $statuses
     */
    public static function resolveNameFromMaster(string $canonicalLower, iterable $statuses): ?string
    {
        foreach ($statuses as $s) {
            $name = is_object($s) ? (string) ($s->name ?? '') : (string) $s;
            if ($name !== '' && strcasecmp($name, $canonicalLower) === 0) {
                return $name;
            }
        }

        return null;
    }
}
