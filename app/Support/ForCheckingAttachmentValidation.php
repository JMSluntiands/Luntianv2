<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ForCheckingAttachmentValidation
{
    public const MESSAGE = 'Attach at least one file before moving this job to For Checking.';

    public static function isForCheckingStatus(?string $status): bool
    {
        return strtolower(trim((string) $status)) === 'for checking';
    }

    public static function jobHasAttachment(object $job): bool
    {
        foreach (['upload_files', 'upload_project_files', 'plans_files', 'docs_files'] as $column) {
            if (! property_exists($job, $column) && ! isset($job->{$column})) {
                continue;
            }
            if (self::valueHasFiles($job->{$column} ?? null)) {
                return true;
            }
        }

        return false;
    }

    public static function jsonErrorIfForCheckingWithoutAttachment(
        object $job,
        string $newStatus,
        ?string $currentStatus = null
    ): ?JsonResponse {
        if (! self::isForCheckingStatus($newStatus)) {
            return null;
        }

        $current = $currentStatus ?? (string) ($job->job_status ?? $job->status ?? '');
        if (self::isForCheckingStatus($current)) {
            return null;
        }

        if (self::jobHasAttachment($job)) {
            return null;
        }

        return response()->json([
            'status' => 'error',
            'message' => self::MESSAGE,
        ], 422);
    }

    private static function valueHasFiles(mixed $raw): bool
    {
        if ($raw === null || $raw === '' || $raw === '[]' || $raw === 'null') {
            return false;
        }
        if (is_array($raw)) {
            return self::arrayHasFiles($raw);
        }
        if (! is_string($raw)) {
            return false;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return self::arrayHasFiles($decoded);
        }

        return trim($raw) !== '';
    }

    /**
     * @param  list<mixed>|array<string, mixed>  $items
     */
    private static function arrayHasFiles(array $items): bool
    {
        foreach ($items as $item) {
            if (is_string($item) && trim($item) !== '') {
                return true;
            }
            if (is_array($item)) {
                $name = trim((string) ($item['name'] ?? $item['path'] ?? $item['filename'] ?? $item['file'] ?? ''));
                if ($name !== '') {
                    return true;
                }
            }
        }

        return false;
    }
}
