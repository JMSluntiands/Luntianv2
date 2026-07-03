<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolve on-disk folders for job file uploads/downloads (LBS family + BPH family).
 */
final class JobUploadFolder
{
    public static function lbsFolderName(object $job, int $jobId): string
    {
        foreach (self::lbsFolderCandidates($job, $jobId) as $folder) {
            return $folder;
        }

        return 'job_' . $jobId;
    }

    /**
     * @return list<string>
     */
    public static function lbsFolderCandidates(object $job, int $jobId): array
    {
        $candidates = [];

        foreach (['job_reference_no', 'client_reference_no', 'reference'] as $field) {
            $value = trim((string) ($job->{$field} ?? ''));
            if ($value !== '' && ! in_array($value, $candidates, true)) {
                $candidates[] = $value;
            }
        }

        $reference = trim((string) ($job->reference ?? ''));
        if ($reference !== '') {
            $stripped = preg_replace('/-1$/', '', $reference) ?? $reference;
            if ($stripped !== '' && ! in_array($stripped, $candidates, true)) {
                $candidates[] = $stripped;
            }
        }

        $fallback = 'job_' . $jobId;
        if (! in_array($fallback, $candidates, true)) {
            $candidates[] = $fallback;
        }

        return $candidates;
    }

    public static function lbsStoragePath(object $job, int $jobId, string $fileName): ?string
    {
        foreach (self::lbsFolderCandidates($job, $jobId) as $folder) {
            $path = 'lbs-documents/' . $folder . '/' . $fileName;
            if (Storage::disk('local')->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    public static function gaStoragePath(object $job, int $jobId, string $fileName): ?string
    {
        foreach (self::lbsFolderCandidates($job, $jobId) as $folder) {
            $path = 'ga-documents/' . $folder . '/' . $fileName;
            if (Storage::disk('local')->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    public static function bphFolderName(object $job, int $jobId): string
    {
        $reference = trim((string) ($job->reference ?? ''));
        if ($reference !== '') {
            return preg_replace('/[^A-Za-z0-9\-\_]/', '_', $reference) ?: ('job_' . $jobId);
        }

        return 'job_' . $jobId;
    }

    public static function bphStoragePath(string $storageBase, object $job, int $jobId, string $fileName): ?string
    {
        $folder = self::bphFolderName($job, $jobId);
        $path = rtrim($storageBase, '/') . '/' . $folder . '/' . $fileName;
        if (Storage::disk('local')->exists($path)) {
            return $path;
        }

        return null;
    }
}
