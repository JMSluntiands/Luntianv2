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
     * @param iterable<int, object|string> $statuses Status models with `name`, or compatible
     * @return list<string>
     */
    public static function nextAllowedLabels(?string $currentJobStatus, iterable $statuses): array
    {
        $nextLower = self::nextAllowedLower($currentJobStatus);
        if ($nextLower === null) {
            return [];
        }
        $resolved = self::resolveNameFromMaster($nextLower, $statuses);
        if ($resolved !== null) {
            return [$resolved];
        }
        $fallback = self::LABEL_BY_LOWER[$nextLower] ?? null;

        return $fallback !== null ? [$fallback] : [];
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

    /**
     * True if $to is the same as $from (caller may skip) or exactly one forward step in the workflow.
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
        $nextLower = self::nextAllowedLower($fromTrim);
        if ($nextLower === null) {
            return false;
        }

        return strcasecmp($toTrim, $nextLower) === 0;
    }
}
