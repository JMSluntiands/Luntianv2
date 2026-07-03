<?php

namespace App\Support;

/**
 * Fyrs Energy Wise assessor workflow: after creation (Allocated), staff may set Processing or Completed.
 */
final class FyrsJobStatusFlow
{
    private const LABEL_BY_LOWER = [
        'processing' => 'Processing',
        'completed' => 'Completed',
    ];

    /**
     * @param iterable<int, object|string> $statuses
     * @return list<string>
     */
    public static function allowedLabels(?string $currentJobStatus, iterable $statuses): array
    {
        $curr = strtolower(LbsJobStatusFlow::normalizeStatusKey($currentJobStatus));
        $allowedLower = match ($curr) {
            'allocated' => ['processing', 'completed'],
            'processing' => ['completed'],
            default => [],
        };

        $labels = [];
        foreach ($allowedLower as $lower) {
            $resolved = LbsJobStatusFlow::resolveNameFromMaster($lower, $statuses);
            if ($resolved !== null) {
                $labels[] = $resolved;
            } elseif (isset(self::LABEL_BY_LOWER[$lower])) {
                $labels[] = self::LABEL_BY_LOWER[$lower];
            }
        }

        return $labels;
    }

    public static function isValidTransition(?string $from, ?string $to): bool
    {
        $fromTrim = LbsJobStatusFlow::normalizeStatusKey($from);
        $toTrim = LbsJobStatusFlow::normalizeStatusKey($to);
        if ($toTrim === '') {
            return false;
        }
        if (strcasecmp($fromTrim, $toTrim) === 0) {
            return true;
        }

        $fromLower = strtolower($fromTrim);
        $toLower = strtolower($toTrim);
        $allowedLower = match ($fromLower) {
            'allocated' => ['processing', 'completed'],
            'processing' => ['completed'],
            default => [],
        };

        return in_array($toLower, $allowedLower, true);
    }
}
