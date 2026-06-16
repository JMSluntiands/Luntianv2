<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailBph;
use App\Models\Compliance;
use App\Models\EmailConfig;
use App\Models\JobRequest;
use App\Models\User;
use App\Services\JobCountsScope;
use App\Services\SlackAssignmentNotifier;
use App\Support\FecUnitsValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class NhJobController extends Controller
{
    private const NH_CLIENT_CODE = 'NH01';

    public function addForm()
    {
        $compliances = Compliance::orderBy('column')->get();
        $jobRequests = JobRequest::orderBy('job_request_type')->get();
        $assignmentStaffUsers = User::assignmentUsersForSelect('nh', 'staff');
        $assignmentCheckerUsers = User::assignmentUsersForSelect('nh', 'checker');
        $bphClientEmails = ClientEmailBph::orderBy('email')->get(['id', 'email']);

        $defaultCompliance = $compliances->first(fn ($c) => $c->column && stripos((string) $c->column, '2019') !== false)
            ?? $compliances->first();
        $defaultJobRequest = $jobRequests->first(fn ($jr) => $jr->job_request_type && stripos((string) $jr->job_request_type, 'query') !== false)
            ?? $jobRequests->first();

        return view('nh.add', [
            'sidebar_active' => 'nh.add',
            'compliances' => $compliances,
            'jobRequests' => $jobRequests,
            'assignmentStaffUsers' => $assignmentStaffUsers,
            'assignmentCheckerUsers' => $assignmentCheckerUsers,
            'bphClientEmails' => $bphClientEmails,
            'defaultComplianceId' => $defaultCompliance?->id,
            'defaultJobRequestId' => $defaultJobRequest?->id,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ncc_compliance'   => ['nullable', 'integer'],
            'job_type_request' => ['nullable', 'integer'],
            'job_number'       => ['required', 'string', 'max:6', 'regex:/^\d{5}B$/i'],
            'client_name'      => ['required', 'string', 'max:255'],
            'contact_email'    => ['required', 'email', 'max:255'],
            'notes'            => ['nullable', 'string'],
            'assigned_to'      => ['required', 'string', 'max:50'],
            'checked_by'       => ['required', 'string', 'max:50'],
            'urgent_job'       => ['nullable'],
        ]);

        if (!Schema::hasTable('job_nh')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database table job_nh is not available. Run migrations.',
            ], 500);
        }

        $compliance = !empty($data['ncc_compliance']) ? Compliance::find($data['ncc_compliance']) : null;
        $jobRequest = !empty($data['job_type_request']) ? JobRequest::find($data['job_type_request']) : null;
        $nccText = $compliance->column ?? '2019';
        $jobTypeText = $jobRequest->job_request_type ?? '—';

        $headerRef = trim((string) $request->input('header_reference', ''));
        $reference = $headerRef !== '' ? $headerRef : ('NH-' . now('Asia/Manila')->format('YmdHis'));
        $reference = substr($reference, 0, 50);

        $now = now('Asia/Manila');
        $urgent = $request->boolean('urgent_job') ? 'YES' : 'NO';
        $jobNum = strtoupper(substr($data['job_number'], 0, 6));
        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $reference) ?: 'nh_upload';

        $planNames = [];
        foreach ((array) $request->file('upload_plans', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $path = 'nh-documents/' . $folderSeg . '/' . $safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $planNames[] = $safeName;
        }

        $docNames = [];
        foreach ((array) $request->file('upload_document', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $path = 'nh-documents/' . $folderSeg . '/' . $safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $docNames[] = $safeName;
        }

        try {
            $nextId = (int) DB::table('job_nh')->max('id') + 1;
            $row = [
                'id' => $nextId,
                'reference' => $reference,
                'client_code' => self::NH_CLIENT_CODE,
                'urgent' => $urgent,
                'job_type' => substr($jobTypeText, 0, 100),
                'ncc' => substr((string) $nccText, 0, 255),
                'job_number' => $jobNum,
                'client_name' => $data['client_name'],
                'contact_email' => $data['contact_email'],
                'notes' => $data['notes'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
                'assigned' => $data['assigned_to'],
                'checked' => $data['checked_by'],
                'plans_files' => json_encode($planNames),
                'docs_files' => json_encode($docNames),
                'status' => 'Allocated',
                'date' => $now->toDateString(),
                'address' => null,
                'climate_zone' => null,
                'compliance_summary_description' => null,
                'spec_client_no' => null,
                'spec_lbs_no' => null,
                'spec_plans' => null,
                'spec_insulation' => null,
                'spec_glazing' => null,
                'spec_sealing' => null,
                'spec_services' => null,
                'spec_additional' => null,
                'units' => 0,
            ];
            if (Schema::hasColumn('job_nh', 'spec_print_merge_file')) {
                $row['spec_print_merge_file'] = null;
            }
            DB::table('job_nh')->insert($row);
            $id = $nextId;
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'NH job created successfully.',
            'job_id' => $id,
        ]);
    }

    public function sendSlackNotification(int $id)
    {
        $job = DB::table('job_nh')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        return response()->json(['status' => 'success']);
    }

    public function sendSubmissionEmail(int $id)
    {
        $job = DB::table('job_nh')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $emailConfig = EmailConfig::where('is_active', true)->first();
        if (!$emailConfig) {
            return response()->json([
                'status' => 'disabled',
                'message' => 'Email sending is disabled.',
            ]);
        }

        $toEmail = trim((string) ($job->contact_email ?? ''));
        if ($toEmail === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'No contact email on this job.',
            ], 422);
        }

        $ref = trim($job->reference ?? '') ?: '—';
        $jobNum = trim($job->job_number ?? '') ?: '—';
        $accountClient = $job->client_name ?? '';
        $nccCompliance = $job->ncc ?? '';
        $jobTypeLabel = $job->job_type ?? '';
        $priorityText = (($job->urgent ?? '') === 'YES') ? 'Urgent' : '—';
        $jobTypeShort = strtoupper(\Illuminate\Support\Str::limit(str_replace('-', '_', \Illuminate\Support\Str::slug($jobTypeLabel)), 12, ''));
        $headerTitle = $ref . '_' . ($jobTypeShort ?: 'NH') . '_' . $jobNum;
        $emailSubject = 'LUNTIAN NH Job Submission: ' . trim($accountClient) . ' LUNTIAN' . $ref . '-' . $jobNum . '-' . $nccCompliance;

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $job->reference ?? '') ?: 'nh_upload';
        $basePath = 'nh-documents/' . $folderSeg . '/';
        $planNames = json_decode($job->plans_files ?? '[]', true) ?: [];
        $docNames = json_decode($job->docs_files ?? '[]', true) ?: [];
        if (!is_array($planNames)) $planNames = [];
        if (!is_array($docNames)) $docNames = [];
        $attachments = [];
        foreach (array_merge($planNames, $docNames) as $fileName) {
            $storagePath = $basePath . $fileName;
            if (Storage::disk('local')->exists($storagePath)) {
                $attachments[] = [
                    'path' => Storage::disk('local')->path($storagePath),
                    'name' => $fileName,
                ];
            }
        }

        try {
            Mail::send('emails.lbs-job-submission', [
                'headerTitle'    => $headerTitle,
                'lbsRef'         => $ref,
                'refLabel'       => 'NH Ref #',
                'clientRef'      => $jobNum,
                'accountClient'  => $accountClient,
                'nccCompliance'  => $nccCompliance,
                'jobType'        => $jobTypeLabel,
                'priority'       => $priorityText,
                'hasAttachment'  => count($attachments) > 0,
            ], function ($message) use ($toEmail, $emailSubject, $attachments) {
                $message->to($toEmail);
                $message->subject($emailSubject);
                foreach ($attachments as $att) {
                    $message->attach($att['path'], ['as' => $att['name']]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Email sent.',
        ]);
    }

    public function mailbox()
    {
        $jobs = collect();
        if (Schema::hasTable('job_nh')) {
            $q = DB::table('job_nh')
                ->whereRaw('LOWER(TRIM(status)) = ?', [strtolower('For Email Confirmation')]);
            JobCountsScope::applyJobBphAssignment($q);
            JobCountsScope::applyBranchExclusiveStatLabel($q, 'NH');
            $rows = $q
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->limit(300)
                ->get();
            $jobs = $rows->map(function ($row) {
                return (object) [
                    'job_id' => (int) $row->id,
                    'log_date' => $row->updated_at ?? $row->created_at,
                    'job_reference_no' => $row->reference,
                    'reference' => $row->reference,
                    'to_email' => $row->contact_email,
                    'upload_files' => $row->plans_files,
                    'upload_project_files' => $row->docs_files,
                ];
            });
        }

        return view('nh.mailbox', [
            'sidebar_active' => 'nh.mailbox',
            'jobs' => $jobs,
        ]);
    }

    public function emailPreview(int $id)
    {
        $job = DB::table('job_nh')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $assessorEmail = null;
        if (!empty($job->checked)) {
            $user = User::where('unique_code', $job->checked)->first();
            $assessorEmail = $user ? $user->email : null;
        }

        return response()->json([
            'status' => 'success',
            'job_reference_no' => $job->reference ?? '',
            'job_status' => $job->status ?? 'For Email Confirmation',
            'assessor' => $job->checked ?? '',
            'assessor_email' => $assessorEmail,
            'notes' => $job->notes ?? '',
        ]);
    }

    public function sendMailboxEmail(Request $request, int $id)
    {
        $job = DB::table('job_nh')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $completedDate = now('Asia/Manila')->toDateString();

        $emailConfig = EmailConfig::where('is_active', true)->first();
        if (!$emailConfig) {
            DB::table('job_nh')->where('id', $id)->update([
                'status' => 'Completed',
                'date' => $completedDate,
                'updated_at' => now('Asia/Manila'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Email sending is disabled. Status updated to Completed.',
                'email_skipped' => true,
            ]);
        }

        $toEmail = trim((string) ($job->contact_email ?? ''));
        if ($toEmail === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'No contact email on this job.',
            ], 422);
        }

        $assessorEmail = null;
        if (!empty($job->checked)) {
            $user = User::where('unique_code', $job->checked)->first();
            $assessorEmail = $user ? $user->email : '';
        }

        $jobReferenceNo = $job->reference ?? '';
        $jobStatus = $job->status ?? 'For Email Confirmation';
        $assessor = $job->checked ?? '';
        $notes = $job->notes ?? '';

        $subjectParts = array_filter([
            $job->client_name ?? null,
            $jobReferenceNo ?: null,
            $job->job_number ?? null,
        ]);
        $emailSubject = 'Job Update';
        if ($subjectParts !== []) {
            $emailSubject .= ' : ' . implode(' ', $subjectParts);
        } elseif ($jobReferenceNo !== '') {
            $emailSubject .= ' : ' . $jobReferenceNo;
        }

        $logoUrl = $this->getLogoDataUriForEmail();

        $viewData = [
            'logoUrl' => $logoUrl,
            'jobReferenceNo' => $jobReferenceNo,
            'jobStatus' => $jobStatus,
            'assessor' => $assessor,
            'assessorEmail' => $assessorEmail,
            'notes' => $notes,
        ];

        $folderName = preg_replace('/[^A-Za-z0-9\-\_]/', '_', (string) ($job->reference ?? 'job_' . $id));
        $basePath = 'nh-documents/' . $folderName . '/';

        $attachments = [];
        $planNames = json_decode($job->plans_files ?? '[]', true) ?: [];
        $docNames = json_decode($job->docs_files ?? '[]', true) ?: [];
        if (!is_array($planNames)) $planNames = [];
        if (!is_array($docNames)) $docNames = [];
        foreach (array_merge($planNames, $docNames) as $fileName) {
            $storagePath = $basePath . $fileName;
            if (Storage::disk('local')->exists($storagePath)) {
                $attachments[] = [
                    'path' => Storage::disk('local')->path($storagePath),
                    'name' => $fileName,
                ];
            }
        }

        try {
            Mail::send('emails.lbs-status-update', $viewData, function ($message) use ($toEmail, $emailSubject, $attachments) {
                $message->to($toEmail);
                $message->subject($emailSubject);
                foreach ($attachments as $att) {
                    $message->attach($att['path'], ['as' => $att['name']]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }

        DB::table('job_nh')->where('id', $id)->update([
            'status' => 'Completed',
            'date' => $completedDate,
            'updated_at' => now('Asia/Manila'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email sent. Status updated to Completed.',
        ]);
    }

    public function printComplianceSummary(int $id)
    {
        $job = DB::table('job_nh')->where('id', $id)->first();
        if (!$job) {
            abort(404);
        }

        $pdf = $this->nhComplianceSummaryPdfInstance($job);
        $filename = $this->nhComplianceSummaryPdfFilename($job);

        return $pdf->stream($filename);
    }

    private function nhComplianceSummaryPdfInstance(object $job)
    {
        $data = [
            'job' => $job,
            'branchLabel' => 'NH',
        ];

        return Pdf::loadView('bph.pdf.compliance-summary', $data)->setPaper('a4', 'portrait');
    }

    private function nhComplianceSummaryPdfFilename(object $job): string
    {
        $parts = array_filter([
            'NH',
            $job->reference ?? null,
            $job->job_number ?? null,
        ]);
        $base = implode('_', $parts) ?: 'NH_Compliance_Summary';
        return $base . '.pdf';
    }

    private function getLogoDataUriForEmail(): string
    {
        $smallPath = storage_path('app/public/logo-email.png');
        if ($smallPath && is_file($smallPath) && filesize($smallPath) <= 40000) {
            $raw = @file_get_contents($smallPath);
            if ($raw !== false && $raw !== '') {
                return 'data:image/png;base64,' . base64_encode($raw);
            }
        }

        $path = storage_path('app/public/logo-light.png');
        if (!$path || !is_file($path)) {
            return config('app.url') . '/storage/logo-light.png';
        }

        $maxEmbedBytes = 35000;
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return config('app.url') . '/storage/logo-light.png';
        }

        if (strlen($raw) > $maxEmbedBytes) {
            return config('app.url') . '/storage/logo-light.png';
        }

        return 'data:image/png;base64,' . base64_encode($raw);
    }

    public function update(Request $request, int $id)
    {
        $job = DB::table('job_nh')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $data = $request->validate([
            'job_status'    => ['nullable', 'string', 'max:50'],
            'staff_id'      => ['nullable', 'string', 'max:50'],
            'checker_id'    => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'units'         => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if (array_key_exists('job_status', $data)) {
            $candidate = trim((string) ($data['job_status'] ?? ''));
            if ($fecErr = FecUnitsValidation::jsonErrorIfFecWithoutUnits($request, $job, $candidate)) {
                return $fecErr;
            }
        }

        $update = [];
        if (array_key_exists('job_status', $data)) {
            $update['status'] = (string) ($data['job_status'] ?? $job->status);
        }
        if (array_key_exists('staff_id', $data) && $data['staff_id'] !== null && $data['staff_id'] !== '') {
            $update['assigned'] = strtoupper((string) $data['staff_id']);
        }
        if (array_key_exists('checker_id', $data) && $data['checker_id'] !== null && $data['checker_id'] !== '') {
            $update['checked'] = strtoupper((string) $data['checker_id']);
        }
        if (array_key_exists('contact_email', $data)) {
            $update['contact_email'] = $data['contact_email'];
        }
        if (array_key_exists('units', $data) && $data['units'] !== null) {
            $update['units'] = (int) $data['units'];
        }

        if ($update === []) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Nothing to update.',
            ]);
        }

        $prevStaff = SlackAssignmentNotifier::normalizeCode($job->assigned ?? null);
        $prevChecker = SlackAssignmentNotifier::normalizeCode($job->checked ?? null);
        $nextStaff = array_key_exists('assigned', $update)
            ? SlackAssignmentNotifier::normalizeCode((string) $update['assigned'])
            : $prevStaff;
        $nextChecker = array_key_exists('checked', $update)
            ? SlackAssignmentNotifier::normalizeCode((string) $update['checked'])
            : $prevChecker;
        $assignmentChanged = ($nextStaff !== $prevStaff || $nextChecker !== $prevChecker);

        $update['updated_at'] = now('Asia/Manila');
        DB::table('job_nh')->where('id', $id)->update($update);

        if ($assignmentChanged) {
            $jobStatus = (string) (array_key_exists('status', $update) ? $update['status'] : ($job->status ?? ''));
            $jobUrl = route('nh.list') . '?' . http_build_query(['job_id' => $id]);
            SlackAssignmentNotifier::notifyAssignment(
                'Natural Heating',
                $id,
                (string) ($job->reference ?? ''),
                $nextStaff !== '' ? $nextStaff : null,
                $nextChecker !== '' ? $nextChecker : null,
                $jobStatus,
                $jobUrl
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'NH job updated successfully.',
        ]);
    }
}

