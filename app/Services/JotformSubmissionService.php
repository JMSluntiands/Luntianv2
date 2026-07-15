<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ClientAccount;
use App\Models\Compliance;
use App\Models\JotformConfig;
use App\Models\JobRequest;
use App\Models\Priority;
use App\Support\JobUploadFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JotformSubmissionService
{
    /**
     * @return array{job_id: int, reference: string}
     */
    public function createLbsJobFromSubmission(JotformConfig $config, array $submissionFields, ?string $formId = null): array
    {
        if ($config->jotform_form_id && $formId !== null && (string) $config->jotform_form_id !== (string) $formId) {
            throw new \InvalidArgumentException('Submission form ID does not match configured JotForm.');
        }

        $referenceNo = $this->mappedValue($config, $submissionFields, 'map_reference_no', 'lbsRef');
        $clientReference = $this->mappedValue($config, $submissionFields, 'map_client_reference', 'clientRef');
        $jobAddress = $this->mappedValue($config, $submissionFields, 'map_job_address', 'jobAddress');
        $notes = $this->mappedValue($config, $submissionFields, 'map_notes', 'notes');
        $complianceText = $this->mappedValue($config, $submissionFields, 'map_compliance', 'nccCompliance');
        $clientText = $this->mappedValue($config, $submissionFields, 'map_client', 'accountClient');
        $priorityText = $this->mappedValue($config, $submissionFields, 'map_priority', 'priority');
        $jobTypeText = $this->mappedValue($config, $submissionFields, 'map_job_type', 'jobType');
        $assignedTo = $this->mappedValue($config, $submissionFields, 'map_assigned_to', 'staffInitials');
        $checkedBy = $this->mappedValue($config, $submissionFields, 'map_checked_by', 'checkerInitials');
        $jobStatus = $this->mappedValue($config, $submissionFields, 'map_job_status', 'jobStatus');

        $compliance = $complianceText !== '' ? $this->resolveCompliance($complianceText) : null;
        $client = $clientText !== '' ? $this->resolveClientAccount($clientText) : null;
        $jobRequest = $jobTypeText !== '' ? $this->resolveJobRequest($jobTypeText) : null;

        if (! $jobRequest) {
            if ($jobTypeText !== '') {
                Log::channel('jotform')->warning('[JotForm] Job type not matched — falling back to default', [
                    'job_type_text' => $jobTypeText,
                ]);
            }
            $jobRequest = $this->resolveDefaultJobRequest($config);
        }

        if ($priorityText === '') {
            $priorityText = $this->resolveDefaultPriorityText($config);
        }

        $assignedTo = $assignedTo !== '' ? strtoupper(trim($assignedTo)) : null;
        $checkedBy = $checkedBy !== '' ? strtoupper(trim($checkedBy)) : null;

        if ($checkedBy === null && $assignedTo !== null) {
            $checkedBy = $assignedTo;
        }

        if ($jobAddress === '') {
            $jobAddress = null;
        }

        $now = now('Asia/Manila');
        $referenceValue = $referenceNo !== '' ? $referenceNo : ('JOBS'.$now->format('YmdHis'));
        if (stripos($referenceValue, 'JOBS') !== 0) {
            $referenceValue = 'JOBS-'.$referenceValue;
        }
        $referenceValue = $referenceValue.'-1';

        $clientCodeForJob = $this->resolveJobsClientCode(
            trim((string) ($client->client_code ?? '')),
            trim((string) ($jobRequest->client_code ?? ''))
        );
        if ($clientCodeForJob === '') {
            $clientCodeForJob = 'LBS01';
        }
        if (! DB::table('clients')->where('client_code', $clientCodeForJob)->exists()) {
            $clientCodeForJob = 'LBS01';
        }

        $jobReferenceNo = $referenceNo !== '' ? $referenceNo : preg_replace('/-1$/', '', $referenceValue);
        $uploadFolderName = JobUploadFolder::lbsFolderName((object) [
            'job_reference_no' => $jobReferenceNo,
            'client_reference_no' => $clientReference,
            'reference' => $referenceValue,
        ], 0);
        if ($uploadFolderName === 'job_0') {
            $uploadFolderName = preg_replace('/-1$/', '', $referenceValue) ?: ('AUTO_'.$now->format('YmdHis'));
        }

        $planNames = $this->downloadMappedFiles(
            $config,
            $submissionFields,
            'map_upload_plans',
            'uploadPlans',
            $uploadFolderName
        );
        $docNames = $this->downloadMappedFiles(
            $config,
            $submissionFields,
            'map_upload_documents',
            'uploadDocuments',
            $uploadFolderName
        );

        $this->logJotformResolvedJobType($jobTypeText, $jobRequest);

        $jobRequestId = (string) ($jobRequest->job_request_id ?? $jobRequest->id ?? '');
        if ($jobRequestId === '') {
            $jobRequestId = 'EA_LBS_1SDB';
        }

        $jobId = DB::table('jobs')->insertGetId([
            'reference' => $referenceValue,
            'log_date' => $now->format('Y-m-d H:i:s'),
            'client_code' => $clientCodeForJob,
            'job_reference_no' => $jobReferenceNo,
            'client_reference_no' => $clientReference !== '' ? $clientReference : null,
            'staff_id' => $assignedTo,
            'checker_id' => $checkedBy,
            'ncc_compliance' => $compliance->column ?? null,
            'job_request_id' => $jobRequestId,
            'address_client' => $jobAddress,
            'job_type' => $jobRequest->job_request_type ?? ($jobTypeText !== '' ? $jobTypeText : null),
            'priority' => $priorityText,
            'plan_complexity' => null,
            'notes' => $notes !== '' ? $notes : null,
            'upload_files' => json_encode($planNames),
            'upload_project_files' => json_encode($docNames),
            'updated_by' => null,
            'job_status' => $jobStatus !== '' ? $jobStatus : 'Allocated',
            'dwelling' => '',
            'client_account_id' => $client->client_account_id ?? null,
            'completion_date' => null,
            'units' => 0,
        ]);

        try {
            ActivityLog::create([
                'job_id' => (int) $jobId,
                'activity_date' => $now->format('Y-m-d H:i:s'),
                'activity_type' => 'JotForm submission',
                'activity_description' => 'Job created from JotForm into main LBS list.',
                'updated_by' => 'JotForm',
            ]);
        } catch (\Throwable $e) {
            Log::warning('JotForm activity log skipped', [
                'job_id' => $jobId,
                'message' => $e->getMessage(),
            ]);
        }

        return [
            'job_id' => (int) $jobId,
            'reference' => $referenceValue,
        ];
    }

    /**
     * @param  array<string, mixed>  $submission
     */
    public function parseSubmissionPayload(array $submission): array
    {
        $fields = [];

        if (! empty($submission['rawRequest'])) {
            $rawRequest = $submission['rawRequest'];
            $parsed = [];

            if (is_array($rawRequest)) {
                $parsed = $rawRequest;
            } elseif (is_string($rawRequest)) {
                $raw = trim($rawRequest);

                if (str_starts_with($raw, '{') || str_starts_with($raw, '[')) {
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        $parsed = $decoded;
                    }
                }

                if ($parsed === []) {
                    parse_str($raw, $parsed);
                }
            }

            if (is_array($parsed) && $parsed !== []) {
                $fields = array_merge($fields, $parsed);
            }
        }

        $skipKeys = [
            'rawRequest', 'formID', 'formId', 'form_id', 'submissionID', 'submissionId', 'submission_id',
            'ip', 'secret', 'temp_upload', 'temp_upload_folder', 'event', 'documentID',
        ];

        foreach ($submission as $key => $value) {
            if (in_array($key, $skipKeys, true)) {
                continue;
            }
            if (is_array($value)) {
                $fields[(string) $key] = $value;
            } elseif (is_scalar($value) || $value === null) {
                $fields[(string) $key] = $value === null ? '' : (string) $value;
            }
        }

        return $fields;
    }

    private function logJotformResolvedJobType(string $jobTypeText, ?JobRequest $jobRequest): void
    {
        Log::channel('jotform')->info('[JotForm] Job type resolved', [
            'from_form' => $jobTypeText,
            'job_request_id' => $jobRequest->job_request_id ?? null,
            'job_request_type' => $jobRequest->job_request_type ?? null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $submission
     */
    private function mappedValue(JotformConfig $config, array $submission, string $configKey, string $fallbackKey): string
    {
        $mapKey = trim((string) ($config->{$configKey} ?? ''));
        if ($mapKey !== '') {
            $fromMap = $this->valueFromSubmission($submission, $mapKey);
            if ($fromMap !== '') {
                return $fromMap;
            }
        }

        $fallbackKey = trim($fallbackKey);
        if ($fallbackKey === '' || strcasecmp($fallbackKey, $mapKey) === 0) {
            return '';
        }

        return $this->valueFromSubmission($submission, $fallbackKey);
    }

    /**
     * @param  array<string, mixed>  $submission
     */
    private function valueFromSubmission(array $submission, string $jotformKey): string
    {
        if ($jotformKey === '') {
            return '';
        }

        foreach ($submission as $key => $value) {
            if (! $this->fieldKeyMatches((string) $key, $jotformKey)) {
                continue;
            }

            return $this->normalizeSubmissionScalar($value);
        }

        return '';
    }

    private function normalizeSubmissionScalar(mixed $value): string
    {
        if (is_array($value)) {
            // JotForm dropdown/radio often sends ["Selected Label"] or nested answer arrays.
            $parts = [];
            foreach ($value as $item) {
                $normalized = $this->normalizeSubmissionScalar($item);
                if ($normalized !== '' && strcasecmp($normalized, 'other') !== 0) {
                    $parts[] = $normalized;
                }
            }

            return trim(implode(' ', $parts));
        }

        return trim((string) $value);
    }

    private function resolveDefaultJobRequest(JotformConfig $config): ?JobRequest
    {
        if ($config->default_job_request_id) {
            $fromConfig = JobRequest::query()->find($config->default_job_request_id);
            if ($fromConfig) {
                return $fromConfig;
            }
        }

        return JobRequest::query()
            ->where('client_code', 'LBS01')
            ->orderBy('id')
            ->first();
    }

    private function resolveDefaultPriorityText(JotformConfig $config): string
    {
        if ($config->default_priority_id) {
            $priority = Priority::query()->find($config->default_priority_id);
            if ($priority && trim((string) ($priority->name ?? '')) !== '') {
                return trim((string) $priority->name);
            }
        }

        return 'Standard (2 days)';
    }

    private function resolveCompliance(string $text): ?Compliance
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        $exact = Compliance::query()->where('column', $text)->first();
        if ($exact) {
            return $exact;
        }

        $normalized = strtolower(preg_replace('/\s+/', ' ', $text) ?? $text);
        if ($normalized === '2019') {
            return Compliance::query()->where('column', 'like', '%2019%')->first();
        }
        if (str_contains($normalized, '2022') || str_contains($normalized, 'woh')) {
            return Compliance::query()->where('column', 'like', '%2022%')->first();
        }

        return Compliance::query()
            ->whereRaw('LOWER(`column`) LIKE ?', ['%'.$normalized.'%'])
            ->first();
    }

    private function resolveClientAccount(string $text): ?ClientAccount
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        $exact = ClientAccount::query()->where('client_account_name', $text)->first();
        if ($exact) {
            return $exact;
        }

        return ClientAccount::query()
            ->where('client_account_name', 'like', $text)
            ->orderByRaw('LENGTH(client_account_name) ASC')
            ->first();
    }

    private function resolveJobRequest(string $text): ?JobRequest
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        if ($text === '') {
            return null;
        }

        $base = JobRequest::query()->where('client_code', 'LBS01');
        $catalog = (clone $base)->get();

        // Prefer exact / case-insensitive matches on type or job_request_id.
        $exact = $catalog->first(function (JobRequest $row) use ($text) {
            return trim((string) ($row->job_request_type ?? '')) === $text
                || trim((string) ($row->job_request_id ?? '')) === $text;
        });
        if ($exact) {
            return $exact;
        }

        $lower = mb_strtolower($text);
        $ciExact = $catalog->first(function (JobRequest $row) use ($lower) {
            return mb_strtolower(trim((string) ($row->job_request_type ?? ''))) === $lower
                || mb_strtolower(trim((string) ($row->job_request_id ?? ''))) === $lower;
        });
        if ($ciExact) {
            return $ciExact;
        }

        // JotForm vs DB often differ only by hyphen spacing ("Model - 1S" vs "Model- 1S").
        $normalizedNeedle = $this->normalizeJobTypeKey($text);
        $normalizedExact = $catalog->first(function (JobRequest $row) use ($normalizedNeedle) {
            return $this->normalizeJobTypeKey((string) ($row->job_request_type ?? '')) === $normalizedNeedle
                || $this->normalizeJobTypeKey((string) ($row->job_request_id ?? '')) === $normalizedNeedle;
        });
        if ($normalizedExact) {
            return $normalizedExact;
        }

        // Prefix / contains on normalized keys — closest length wins (not shortest).
        $normalizedMatches = $catalog->filter(function (JobRequest $row) use ($normalizedNeedle) {
            $typeKey = $this->normalizeJobTypeKey((string) ($row->job_request_type ?? ''));
            if ($typeKey === '') {
                return false;
            }

            return str_starts_with($typeKey, $normalizedNeedle)
                || str_contains($typeKey, $normalizedNeedle);
        });

        return $this->bestJobRequestMatch($normalizedMatches, $text);
    }

    /**
     * Collapse whitespace and unify dashes so JotForm labels can match DB labels.
     */
    private function normalizeJobTypeKey(string $text): string
    {
        $text = mb_strtolower(trim($text));
        if ($text === '') {
            return '';
        }

        // en-dash / em-dash / minus → hyphen
        $text = preg_replace('/[\x{2013}\x{2014}\x{2212}\-]+/u', '-', $text) ?? $text;
        // "Model - 1S" and "Model- 1S" → "model-1s"
        $text = preg_replace('/\s*-\s*/', '-', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, JobRequest>  $matches
     */
    private function bestJobRequestMatch($matches, string $text): ?JobRequest
    {
        if ($matches->isEmpty()) {
            return null;
        }

        $needleKey = $this->normalizeJobTypeKey($text);
        $needleLen = mb_strlen($needleKey);

        return $matches
            ->sortBy(function (JobRequest $row) use ($needleLen) {
                $typeKey = $this->normalizeJobTypeKey((string) ($row->job_request_type ?? ''));

                return abs(mb_strlen($typeKey) - $needleLen);
            })
            ->values()
            ->first();
    }

    /**
     * @param  array<string, mixed>  $submission
     * @return list<string>
     */
    private function downloadMappedFiles(
        JotformConfig $config,
        array $submission,
        string $configKey,
        string $fallbackKey,
        string $uploadFolderName
    ): array {
        $mapKey = trim((string) ($config->{$configKey} ?? ''));
        if ($mapKey === '') {
            return [];
        }

        $urls = $this->urlsFromField($submission, $mapKey !== '' ? $mapKey : $fallbackKey);
        $saved = [];

        foreach ($urls as $url) {
            $fileName = $this->downloadJotformFile($url, $uploadFolderName);
            if ($fileName !== null) {
                $saved[] = $fileName;
            }
        }

        return $saved;
    }

    /**
     * @param  array<string, mixed>  $submission
     * @return list<string>
     */
    private function urlsFromField(array $submission, string $jotformKey): array
    {
        $urls = [];

        foreach ($submission as $key => $value) {
            if ($this->fieldKeyMatches((string) $key, $jotformKey)) {
                $urls = array_merge($urls, $this->collectUrls($value));
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * @return list<string>
     */
    private function collectUrls(mixed $value): array
    {
        if (is_array($value)) {
            $urls = [];
            foreach ($value as $item) {
                $urls = array_merge($urls, $this->collectUrls($item));
            }

            return $urls;
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $value = trim($value);
        if ($this->isJotformUploadUrl($value)) {
            return [$value];
        }

        if (str_starts_with($value, '[')) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $this->collectUrls($decoded);
            }
        }

        if (str_contains($value, ',')) {
            $urls = [];
            foreach (explode(',', $value) as $part) {
                $urls = array_merge($urls, $this->collectUrls(trim($part)));
            }

            return $urls;
        }

        return [];
    }

    private function isJotformUploadUrl(string $value): bool
    {
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }

        return (bool) preg_match('#^https?://([a-z0-9.-]*\.)?jotform\.com/uploads/#i', $value);
    }

    private function downloadJotformFile(string $url, string $uploadFolderName): ?string
    {
        try {
            $response = Http::timeout(90)->get($url);
            if (! $response->successful()) {
                Log::warning('JotForm file download failed', ['url' => $url, 'status' => $response->status()]);

                return null;
            }

            $path = parse_url($url, PHP_URL_PATH) ?: '';
            $original = basename($path) ?: 'jotform_upload.bin';
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original) ?: 'jotform_upload.bin';
            $storagePath = 'lbs-documents/'.$uploadFolderName.'/'.$safeName;

            Storage::disk('local')->put($storagePath, $response->body());

            return $safeName;
        } catch (\Throwable $e) {
            Log::warning('JotForm file download error', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function fieldKeyMatches(string $fieldKey, string $jotformKey): bool
    {
        if (strcasecmp($fieldKey, $jotformKey) === 0) {
            return true;
        }

        $fieldKey = preg_replace('/\[\d*\]$/', '', $fieldKey) ?? $fieldKey;

        // JotForm often sends q116_lbsRef116 / q113_clientRef113 (unique name + question id suffix).
        return (bool) preg_match('/(^|_)'.preg_quote($jotformKey, '/').'(\d*)$/i', $fieldKey);
    }

    private function resolveJobsClientCode(string $fromAccount, string $fromJobRequest): string
    {
        if ($fromAccount !== '' && DB::table('clients')->where('client_code', $fromAccount)->exists()) {
            return $fromAccount;
        }
        if ($fromJobRequest !== '' && DB::table('clients')->where('client_code', $fromJobRequest)->exists()) {
            return $fromJobRequest;
        }

        return '';
    }
}
