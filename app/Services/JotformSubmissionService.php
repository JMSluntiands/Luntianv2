<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ClientAccount;
use App\Models\Compliance;
use App\Models\JotformConfig;
use App\Models\JobRequest;
use App\Models\Priority;
use App\Models\User;
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

        $referenceNo = $this->mappedValue($submissionFields, (string) ($config->map_reference_no ?? 'lbsRef'));
        $clientReference = $this->mappedValue($submissionFields, (string) ($config->map_client_reference ?? 'clientRef'));
        $jobAddress = $this->mappedValue($submissionFields, (string) ($config->map_job_address ?? 'jobAddress'));
        $notes = $this->mappedValue($submissionFields, (string) ($config->map_notes ?? 'notes'));

        $complianceText = $this->mappedValue($submissionFields, (string) ($config->map_compliance ?? 'nccCompliance'));
        $clientText = $this->mappedValue($submissionFields, (string) ($config->map_client ?? 'accountClient'));
        $priorityText = $this->mappedValue($submissionFields, (string) ($config->map_priority ?? 'priority'));
        $jobTypeText = $this->mappedValue($submissionFields, (string) ($config->map_job_type ?? 'jobType'));
        $assignedTo = $this->mappedValue($submissionFields, (string) ($config->map_assigned_to ?? 'staffInitials'));
        $checkedBy = $this->mappedValue($submissionFields, (string) ($config->map_checked_by ?? 'checkerInitials'));
        $jobStatus = $this->mappedValue($submissionFields, (string) ($config->map_job_status ?? 'jobStatus'));

        $compliance = $this->resolveCompliance($complianceText);
        if (! $compliance) {
            throw new \InvalidArgumentException('Compliance from JotForm is missing or not recognized: '.($complianceText ?: '(empty)'));
        }

        $client = $this->resolveClientAccount($clientText);
        if (! $client) {
            throw new \InvalidArgumentException('Client from JotForm is missing or not recognized: '.($clientText ?: '(empty)'));
        }

        $jobRequest = $this->resolveJobRequest($jobTypeText);
        if (! $jobRequest) {
            throw new \InvalidArgumentException('Job type from JotForm is missing or not recognized: '.($jobTypeText ?: '(empty)'));
        }

        if ($priorityText === '') {
            throw new \InvalidArgumentException('Priority from JotForm is missing.');
        }

        if ($assignedTo === '') {
            throw new \InvalidArgumentException('Assigned to (staff initials) from JotForm is missing.');
        }

        if ($checkedBy === '') {
            throw new \InvalidArgumentException('Checked by (checker initials) from JotForm is missing.');
        }

        $assignedTo = strtoupper(trim($assignedTo));
        $checkedBy = strtoupper(trim($checkedBy));

        if ($jobAddress === '') {
            $jobAddress = 'Submitted via JotForm';
        }

        if ($jobStatus === '') {
            $jobStatus = 'Allocated';
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
            throw new \RuntimeException('No valid client code for this job. Check client account and job type in JotForm submission.');
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
            $submissionFields,
            (string) ($config->map_upload_plans ?? 'uploadPlans'),
            $uploadFolderName
        );
        $docNames = $this->downloadMappedFiles(
            $submissionFields,
            (string) ($config->map_upload_documents ?? 'uploadDocuments'),
            $uploadFolderName
        );

        $updatedBy = $config->queue_in_forms_submitted ? 'FORMS' : null;

        $jobId = DB::table('jobs')->insertGetId([
            'reference' => $referenceValue,
            'log_date' => $now->format('Y-m-d H:i:s'),
            'client_code' => $clientCodeForJob,
            'job_reference_no' => $jobReferenceNo,
            'client_reference_no' => $clientReference !== '' ? $clientReference : null,
            'staff_id' => $assignedTo,
            'checker_id' => $checkedBy,
            'ncc_compliance' => $compliance->column ?? null,
            'job_request_id' => $jobRequest->job_request_id ?? (string) $jobRequest->id,
            'address_client' => $jobAddress,
            'job_type' => $jobRequest->job_request_type ?? $jobTypeText,
            'priority' => $priorityText,
            'plan_complexity' => null,
            'notes' => $notes !== '' ? $notes : null,
            'upload_files' => json_encode($planNames),
            'upload_project_files' => json_encode($docNames),
            'updated_by' => $updatedBy,
            'job_status' => $jobStatus,
            'dwelling' => '',
            'client_account_id' => $client->client_account_id,
            'completion_date' => null,
            'units' => 0,
        ]);

        ActivityLog::create([
            'job_id' => (int) $jobId,
            'activity_date' => $now->format('Y-m-d H:i:s'),
            'activity_type' => 'JotForm submission',
            'activity_description' => $updatedBy === 'FORMS'
                ? 'Job created from JotForm into Forms Submitted Jobs.'
                : 'Job created from JotForm into main LBS list.',
            'updated_by' => 'JotForm',
        ]);

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

        if (! empty($submission['rawRequest']) && is_string($submission['rawRequest'])) {
            parse_str($submission['rawRequest'], $parsed);
            if (is_array($parsed)) {
                $fields = array_merge($fields, $parsed);
            }
        }

        foreach ($submission as $key => $value) {
            if (in_array($key, ['rawRequest', 'formID', 'submissionID', 'ip', 'secret'], true)) {
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
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        return JobRequest::query()
            ->where('client_code', 'LBS01')
            ->where(function ($query) use ($text) {
                $query->where('job_request_type', $text)
                    ->orWhere('job_request_type', 'like', $text.'%')
                    ->orWhere('job_request_type', 'like', '%'.$text.'%');
            })
            ->orderByRaw('LENGTH(job_request_type) ASC')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $submission
     * @return list<string>
     */
    private function downloadMappedFiles(array $submission, string $jotformKey, string $uploadFolderName): array
    {
        if ($jotformKey === '') {
            return [];
        }

        $urls = $this->urlsFromField($submission, $jotformKey);
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
            if (strcasecmp((string) $key, $jotformKey) !== 0) {
                continue;
            }
            $urls = array_merge($urls, $this->collectUrls($value));
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

    /**
     * @param  array<string, mixed>  $submission
     */
    private function mappedValue(array $submission, string $jotformKey): string
    {
        if ($jotformKey === '') {
            return '';
        }

        if (array_key_exists($jotformKey, $submission)) {
            $value = $submission[$jotformKey];
            if (is_array($value)) {
                return trim(implode(', ', array_map('strval', $value)));
            }

            return trim((string) $value);
        }

        foreach ($submission as $key => $value) {
            if (strcasecmp((string) $key, $jotformKey) === 0) {
                if (is_array($value)) {
                    return trim(implode(', ', array_map('strval', $value)));
                }

                return trim((string) $value);
            }
        }

        return '';
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
