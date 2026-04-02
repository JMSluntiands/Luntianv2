<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailBph;
use App\Models\Compliance;
use App\Models\EmailConfig;
use App\Models\JobRequest;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use App\Models\RolePermission;
use App\Services\JobCountsScope;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BphJobController extends Controller
{
    private const BPH_CLIENT_CODE = 'BPH01';

    public function list()
    {
        return view('bph.list', ['sidebar_active' => 'bph.list']);
    }

    public function completed()
    {
        return view('bph.completed', ['sidebar_active' => 'bph.completed']);
    }

    public function review()
    {
        return view('bph.review', ['sidebar_active' => 'bph.review']);
    }

    public function mailbox()
    {
        $jobs = collect();
        if (Schema::hasTable('job_bph')) {
            $q = DB::table('job_bph')
                ->whereRaw('LOWER(TRIM(status)) = ?', [strtolower('For Email Confirmation')]);
            JobCountsScope::applyJobBphAssignment($q);
            JobCountsScope::applyJobBphBranchVerticalScope($q);
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

        return view('bph.mailbox', [
            'sidebar_active' => 'bph.mailbox',
            'jobs' => $jobs,
        ]);
    }

    /**
     * Mailbox email preview (same JSON shape as LBS for shared modal UI).
     */
    public function emailPreview(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
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

    /**
     * Send mailbox confirmation email; attachments from latest BPH checker upload batch.
     */
    public function sendMailboxEmail(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
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
        $basePath = 'bph-documents/' . $folderName . '/';

        $attachments = [];
        $latestCheckerUpload = DB::table('bph_staff_uploaded_files')
            ->where('job_id', (int) $id)
            ->orderByDesc('uploaded_at')
            ->orderByDesc('file_id')
            ->first();

        if ($latestCheckerUpload && !empty($latestCheckerUpload->files_json)) {
            $files = json_decode($latestCheckerUpload->files_json, true);
            if (is_array($files)) {
                foreach ($files as $fileName) {
                    $storagePath = $basePath . $fileName;
                    if (Storage::disk('local')->exists($storagePath)) {
                        $attachments[] = [
                            'path' => Storage::disk('local')->path($storagePath),
                            'name' => $fileName,
                        ];
                    }
                }
            }
        }

        try {
            $compliancePdf = $this->bphComplianceSummaryPdfInstance($job);
            $compliancePdfName = $this->bphComplianceSummaryPdfFilename($job);
            $compliancePdfBinary = $compliancePdf->output();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not generate compliance PDF: ' . $e->getMessage(),
            ], 500);
        }

        try {
            Mail::send('emails.lbs-status-update', $viewData, function ($message) use ($toEmail, $emailSubject, $attachments, $compliancePdfBinary, $compliancePdfName) {
                $message->to($toEmail);
                $message->subject($emailSubject);
                $message->attachData($compliancePdfBinary, $compliancePdfName, ['mime' => 'application/pdf']);
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

        DB::table('job_bph')->where('id', $id)->update([
            'status' => 'Completed',
            'updated_at' => now('Asia/Manila'),
        ]);
        $this->createBphActivityLog(
            (int) $id,
            'Email',
            'Mailbox confirmation email sent; status set to Completed.',
            session('user_name') ?? 'LUNTIAN'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Email sent successfully. Status updated to Completed.',
        ]);
    }

    public function trash()
    {
        return view('bph.trash', ['sidebar_active' => 'bph.trash']);
    }

    /**
     * Show the Add New BPH Job form with dropdown data from database.
     */
    public function addForm()
    {
        $compliances = Compliance::orderBy('column')->get();
        $jobRequests = JobRequest::orderBy('job_request_type')->get();
        $assignmentUsers = User::whereIn('role', ['staff', 'checker'])
            ->orderBy('unique_code')
            ->get(['id', 'unique_code'])
            ->unique('unique_code')
            ->values();

        $bphClientEmails = ClientEmailBph::orderBy('email')->get(['id', 'email']);

        $defaultCompliance = $compliances->first(fn ($c) => $c->column && stripos((string) $c->column, '2019') !== false)
            ?? $compliances->first();
        $defaultJobRequest = $jobRequests->first(fn ($jr) => $jr->job_request_type && stripos((string) $jr->job_request_type, 'query') !== false)
            ?? $jobRequests->first();

        return view('bph.add', [
            'sidebar_active' => 'bph.add',
            'compliances' => $compliances,
            'jobRequests' => $jobRequests,
            'assignmentUsers' => $assignmentUsers,
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

        if (!Schema::hasTable('job_bph')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database table job_bph is not available. Run migrations.',
            ], 500);
        }

        $compliance = !empty($data['ncc_compliance']) ? Compliance::find($data['ncc_compliance']) : null;
        $jobRequest = !empty($data['job_type_request']) ? JobRequest::find($data['job_type_request']) : null;

        $nccText = $compliance->column ?? '2019';
        $jobTypeText = $jobRequest->job_request_type ?? '—';

        $headerRef = trim((string) $request->input('header_reference', ''));
        $reference = $headerRef !== '' ? $headerRef : ('BPH-' . now('Asia/Manila')->format('YmdHis'));
        $reference = substr($reference, 0, 50);

        $now = now('Asia/Manila');
        $urgent = $request->boolean('urgent_job') ? 'YES' : 'NO';
        $jobNum = strtoupper(substr($data['job_number'], 0, 6));

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $reference) ?: 'bph_upload';

        $planNames = [];
        foreach ((array) $request->file('upload_plans', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $path = 'bph-documents/' . $folderSeg . '/' . $safeName;
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
            $path = 'bph-documents/' . $folderSeg . '/' . $safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $docNames[] = $safeName;
        }

        try {
            // Some legacy environments do not have AUTO_INCREMENT on job_bph.id,
            // so compute the next id manually to avoid SQLSTATE[HY000] 1364.
            $nextId = (int) DB::table('job_bph')->max('id') + 1;

            $row = [
                'id'                  => $nextId,
                'reference'           => $reference,
                'client_code'         => self::BPH_CLIENT_CODE,
                'urgent'              => $urgent,
                'job_type'            => substr($jobTypeText, 0, 100),
                'ncc'                 => substr((string) $nccText, 0, 255),
                'job_number'          => $jobNum,
                'client_name'         => $data['client_name'],
                'contact_email'       => $data['contact_email'],
                'notes'               => $data['notes'] ?? null,
                'created_at'          => $now,
                'updated_at'          => $now,
                'assigned'            => $data['assigned_to'],
                'checked'             => $data['checked_by'],
                'plans_files'         => json_encode($planNames),
                'docs_files'          => json_encode($docNames),
                'status'              => 'Allocated',
                'date'                => $now->toDateString(),
                'address'             => null,
                'climate_zone'        => null,
                'compliance_summary_description' => null,
                'spec_client_no'      => null,
                'spec_lbs_no'         => null,
                'spec_plans'          => null,
                'spec_insulation'     => null,
                'spec_glazing'        => null,
                'spec_sealing'        => null,
                'spec_services'       => null,
                'spec_additional'     => null,
                'units'               => 0,
            ];
            if (Schema::hasColumn('job_bph', 'spec_print_merge_file')) {
                $row['spec_print_merge_file'] = null;
            }
            DB::table('job_bph')->insert($row);
            $id = $nextId;
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'BPH job created successfully.',
            'job_id'  => $id,
        ]);
    }

    public function sendSlackNotification(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $slackConfig = \App\Models\SlackConfig::first();
        $slackWebhook = ($slackConfig && $slackConfig->is_active && !empty($slackConfig->webhook_url))
            ? $slackConfig->webhook_url
            : config('services.slack.lbs_webhook');

        if (!$slackWebhook) {
            return response()->json(['status' => 'success', 'message' => 'Slack not configured.']);
        }

        $reference = $job->reference ?? '';
        $jobNum = $job->job_number ?? '';
        $clientName = $job->client_name ?? '';
        $status = $job->status ?? '';
        $ncc = $job->ncc ?? '';
        $jobType = $job->job_type ?? '';
        $urgent = $job->urgent ?? '';
        $contact = $job->contact_email ?? '';
        $assigned = $job->assigned ?? '';
        $checked = $job->checked ?? '';

        try {
            $slackMessage = [
                'text' => '🆕 New BPH Job Submitted',
                'attachments' => [
                    [
                        'color' => '#0d9488',
                        'fields' => [
                            ['title' => 'BPH Ref #', 'value' => $reference, 'short' => true],
                            ['title' => 'Job Number', 'value' => $jobNum, 'short' => true],
                            ['title' => 'Client Name', 'value' => $clientName, 'short' => true],
                            ['title' => 'Status', 'value' => $status, 'short' => true],
                            ['title' => 'Urgent', 'value' => $urgent, 'short' => true],
                            ['title' => 'NCC', 'value' => $ncc, 'short' => true],
                            ['title' => 'Job Type', 'value' => $jobType, 'short' => false],
                            ['title' => 'Contact Email', 'value' => $contact, 'short' => false],
                            ['title' => 'Assigned To', 'value' => $assigned, 'short' => true],
                            ['title' => 'Checked By', 'value' => $checked, 'short' => true],
                        ],
                        'footer' => 'Luntian BPH Job Management',
                        'ts' => time(),
                    ],
                ],
            ];

            $ch = curl_init($slackWebhook);
            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($slackMessage),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
            ]);
            curl_exec($ch);
            $slackError = curl_error($ch);
            curl_close($ch);

            if ($slackError) {
                \Log::warning('BPH Slack notification failed', ['error' => $slackError, 'job_bph_id' => $id]);
            }
        } catch (\Throwable $e) {
            \Log::warning('BPH Slack exception', ['message' => $e->getMessage(), 'job_bph_id' => $id]);
        }

        return response()->json(['status' => 'success']);
    }

    public function sendSubmissionEmail(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
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
                'status'  => 'error',
                'message' => 'No contact email on this job.',
            ], 422);
        }

        $bphRef = trim($job->reference ?? '') ?: '—';
        $jobNum = trim($job->job_number ?? '') ?: '—';
        $accountClient = $job->client_name ?? '';
        $nccCompliance = $job->ncc ?? '';
        $jobTypeLabel = $job->job_type ?? '';
        $priorityText = (($job->urgent ?? '') === 'YES') ? 'Urgent' : '—';

        $jobTypeShort = strtoupper(\Illuminate\Support\Str::limit(str_replace('-', '_', \Illuminate\Support\Str::slug($jobTypeLabel)), 12, ''));
        $headerTitle = $bphRef . '_' . ($jobTypeShort ?: 'BPH') . '_' . $jobNum;

        $emailSubject = 'LUNTIAN BPH Job Submission: '
            . trim($accountClient) . ' LUNTIAN' . $bphRef . '-' . $jobNum . '-' . $nccCompliance;

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $job->reference ?? '') ?: 'bph_upload';
        $basePath = 'bph-documents/' . $folderSeg . '/';
        $planNames = json_decode($job->plans_files ?? '[]', true) ?: [];
        $docNames = json_decode($job->docs_files ?? '[]', true) ?: [];
        if (!is_array($planNames)) {
            $planNames = [];
        }
        if (!is_array($docNames)) {
            $docNames = [];
        }
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
                'lbsRef'         => $bphRef,
                'refLabel'       => 'BPH Ref #',
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
            \Log::error('BPH submission email failed', [
                'job_bph_id' => $id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Email sent.',
        ]);
    }

    /**
     * Compliance summary as PDF (inline in browser / new tab). Branded header + specs; image merge embedded, PDF merge noted on page 2.
     */
    public function printComplianceSummary(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            abort(404);
        }

        $pdf = $this->bphComplianceSummaryPdfInstance($job);

        return $pdf->stream($this->bphComplianceSummaryPdfFilename($job));
    }

    /**
     * DomPDF instance for BPH compliance summary (browser stream or email attachData).
     */
    private function bphComplianceSummaryPdfInstance(object $job)
    {
        $mergeImageDataUri = $this->specMergeImageDataUri($job);
        $mergePdfName = null;
        if ($mergeImageDataUri === null && filled((string) ($job->spec_print_merge_file ?? ''))) {
            $ext = strtolower(pathinfo((string) $job->spec_print_merge_file, PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                $mergePdfName = (string) $job->spec_print_merge_file;
            }
        }

        $pdf = Pdf::loadView('bph.pdf.compliance-summary', [
            'job' => $job,
            'mergeImageDataUri' => $mergeImageDataUri,
            'mergePdfName' => $mergePdfName,
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    private function bphComplianceSummaryPdfFilename(object $job): string
    {
        $id = (int) ($job->id ?? 0);
        $safeNum = preg_replace('/[^A-Za-z0-9\-_]/', '-', (string) ($job->job_number ?? '')) ?: 'job-' . $id;

        return 'BPH-Compliance-' . $safeNum . '.pdf';
    }

    public function show(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            abort(404);
        }

        $viewJob = (object) [
            'job_id' => (int) $job->id,
            'reference' => $job->reference,
            'log_date' => $job->created_at,
            'client_code' => $job->client_code,
            'job_reference_no' => $job->job_number,
            'client_reference_no' => null,
            'staff_id' => $job->assigned,
            'checker_id' => $job->checked,
            'ncc_compliance' => $job->ncc,
            'job_request_id' => null,
            'address_client' => $job->address,
            'job_type' => $job->job_type,
            'priority' => (($job->urgent ?? 'NO') === 'YES') ? 'Urgent' : null,
            'plan_complexity' => (int) ($job->units ?? 0),
            'job_status' => $job->status,
            'completion_date' => $job->date,
            'notes' => $job->notes,
            'upload_files' => $job->plans_files,
            'upload_project_files' => $job->docs_files,
            'client_account_id' => null,
            'client_account_name' => $job->client_name,
        ];

        $priorityColor = !empty($viewJob->priority)
            ? Priority::where('name', $viewJob->priority)->value('color')
            : null;
        $statusColor = !empty($viewJob->job_status)
            ? Status::where('name', $viewJob->job_status)->value('color')
            : null;

        $compliances = Compliance::orderBy('column')->get(['column']);
        $jobRequests = JobRequest::whereIn('client_code', ['BPH01', 'B1001'])
            ->orderBy('job_request_type')
            ->get(['job_request_type']);
        $bphClientEmails = ClientEmailBph::orderBy('email')->get(['email']);
        $priorities = Priority::orderBy('id')->get();
        $statuses = Status::orderBy('name')->get();
        $clientAccounts = collect();

        $assignmentUsers = User::whereIn('role', ['staff', 'checker'])
            ->orderBy('unique_code')
            ->get(['unique_code'])
            ->pluck('unique_code')
            ->filter()
            ->map(fn ($v) => strtoupper((string) $v))
            ->unique()
            ->values();
        if (!$assignmentUsers->contains('GM')) {
            $assignmentUsers->prepend('GM');
        }

        $activityLogs = DB::table('bph_activity_logs')
            ->where('job_id', (int) $viewJob->job_id)
            ->orderByDesc('activity_date')
            ->limit(50)
            ->get();

        $userRoleMap = [];
        $updatedByNames = $activityLogs->pluck('updated_by')->unique()->filter();
        if ($updatedByNames->isNotEmpty()) {
            $users = User::whereIn('fullname', $updatedByNames)
                ->orWhereIn('unique_code', $updatedByNames)
                ->get(['fullname', 'unique_code', 'role']);
            foreach ($users as $u) {
                $role = ucfirst((string) ($u->role ?? ''));
                if ($u->fullname) {
                    $userRoleMap[$u->fullname] = $role;
                }
                if ($u->unique_code) {
                    $userRoleMap[$u->unique_code] = $role;
                }
            }
        }

        $checkerUploads = DB::table('bph_staff_uploaded_files')
            ->where('job_id', (int) $viewJob->job_id)
            ->orderByDesc('uploaded_at')
            ->get();
        $runComments = DB::table('bph_run_comments')
            ->where('job_id', (int) $viewJob->job_id)
            ->orderByDesc('run_comment_id')
            ->limit(50)
            ->get();
        $jobComments = DB::table('bph_comments')
            ->where('job_id', (int) $viewJob->job_id)
            ->orderByDesc('comment_id')
            ->limit(50)
            ->get();

        return view('lbs.view', [
            'sidebar_active' => 'bph.list',
            'isEfficientLiving' => false,
            'isBphView' => true,
            'bphJobRow' => $job,
            'listRouteName' => 'bph.list',
            'trashRouteName' => 'bph.list',
            'jobUpdateRouteName' => 'bph.update',
            'jobUploadFilesRouteName' => 'bph.job.uploadFiles',
            'jobDeleteFileRouteName' => 'bph.job.deleteFile',
            'jobArchiveRouteName' => 'bph.job.archive',
            'jobCheckerUploadsRouteName' => 'bph.job.checkerUploads',
            'jobRunCommentRouteName' => 'bph.job.runComment',
            'jobCommentRouteName' => 'bph.job.comment',
            'jobFileRouteName' => 'bph.job.file',
            'jobId' => $viewJob->job_id,
            'job' => $viewJob,
            'priorityColor' => $priorityColor,
            'statusColor' => $statusColor,
            'priorities' => $priorities,
            'statuses' => $statuses,
            'clientAccounts' => $clientAccounts,
            'activityLogs' => $activityLogs,
            'userRoleMap' => $userRoleMap,
            'checkerUploads' => $checkerUploads,
            'runComments' => $runComments,
            'jobComments' => $jobComments,
            'compliances' => $compliances,
            'jobRequests' => $jobRequests,
            'bphClientEmails' => $bphClientEmails,
            'assignmentUsers' => $assignmentUsers,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return redirect()->route('bph.list');
        }

        $data = $request->validate([
            'job_status'       => ['nullable', 'string', 'max:50'],
            'job_address'      => ['nullable', 'string', 'max:65535'],
            'priority'         => ['nullable', 'string', 'max:255'],
            'job_type'         => ['nullable', 'string', 'max:100'],
            'notes'            => ['nullable', 'string'],
            'compliance'       => ['nullable', 'string', 'max:255'],
            'job_reference_no' => ['nullable', 'string', 'max:6'],
            'client_name'      => ['nullable', 'string', 'max:255'],
            'staff_id'         => ['nullable', 'string', 'max:50'],
            'checker_id'       => ['nullable', 'string', 'max:50'],
            'contact_email'    => ['nullable', 'email', 'max:255'],
            // fallback for custom form route
            'urgent'           => ['nullable', 'in:YES,NO'],
            'ncc'              => ['nullable', 'string', 'max:255'],
            'job_number'       => ['nullable', 'string', 'max:6'],
            'assigned'         => ['nullable', 'string', 'max:50'],
            'checked'          => ['nullable', 'string', 'max:50'],
            'status'           => ['nullable', 'string', 'max:50'],
            'bph_additional_info_save' => ['nullable'],
        ]);

        if (! RolePermission::userMayAccessRoute('job_view.edit.assigned')) {
            unset($data['staff_id'], $data['assigned'], $data['checker_id'], $data['checked']);
        }
        if (! RolePermission::userMayAccessRoute('job_view.button.edit.job_details')) {
            unset(
                $data['job_status'],
                $data['status'],
                $data['job_address'],
                $data['priority'],
                $data['job_type'],
                $data['compliance'],
                $data['ncc'],
                $data['job_reference_no'],
                $data['job_number'],
                $data['client_name'],
                $data['contact_email'],
                $data['urgent']
            );
        }
        if (! RolePermission::userMayAccessRoute('job_view.button.edit.notes')) {
            unset($data['notes']);
        }

        $allowedUsers = User::whereIn('role', ['staff', 'checker'])
            ->pluck('unique_code')
            ->filter()
            ->map(fn ($v) => strtoupper((string) $v))
            ->unique()
            ->values()
            ->all();
        if (!in_array('GM', $allowedUsers, true)) {
            $allowedUsers[] = 'GM';
        }
        $allowedUsers = array_values(array_filter($allowedUsers, fn ($v) => $v !== ''));
        $assignedCandidate = strtoupper((string) ($data['staff_id'] ?? $data['assigned'] ?? $job->assigned ?? ''));
        $checkedCandidate = strtoupper((string) ($data['checker_id'] ?? $data['checked'] ?? $job->checked ?? ''));
        if (($assignedCandidate !== '' && !in_array($assignedCandidate, $allowedUsers, true))
            || ($checkedCandidate !== '' && !in_array($checkedCandidate, $allowedUsers, true))) {
            return redirect()
                ->back()
                ->withErrors(['assigned' => 'Assigned/Checked must be a valid user code.'])
                ->withInput();
        }

        $update = [];
        if (array_key_exists('job_status', $data) || array_key_exists('status', $data)) {
            $update['status'] = (string) ($data['job_status'] ?? $data['status'] ?? $job->status);
        }
        if (array_key_exists('job_address', $data)) {
            $update['address'] = $data['job_address'];
        }
        if (array_key_exists('job_type', $data)) {
            $update['job_type'] = $data['job_type'];
        }
        if (array_key_exists('notes', $data)) {
            $update['notes'] = $data['notes'];
        }
        if (array_key_exists('compliance', $data) || array_key_exists('ncc', $data)) {
            $update['ncc'] = (string) ($data['compliance'] ?? $data['ncc'] ?? $job->ncc);
        }
        if (array_key_exists('job_reference_no', $data) || array_key_exists('job_number', $data)) {
            $num = strtoupper((string) ($data['job_reference_no'] ?? $data['job_number'] ?? $job->job_number));
            if ($num !== '') {
                $update['job_number'] = substr($num, 0, 6);
            }
        }
        if (array_key_exists('client_name', $data)) {
            $update['client_name'] = $data['client_name'];
        }
        if (array_key_exists('contact_email', $data)) {
            $update['contact_email'] = $data['contact_email'];
        }
        if ($assignedCandidate !== '') {
            $update['assigned'] = $assignedCandidate;
        }
        if ($checkedCandidate !== '') {
            $update['checked'] = $checkedCandidate;
        }
        if (array_key_exists('priority', $data) || array_key_exists('urgent', $data)) {
            $priority = strtolower((string) ($data['priority'] ?? ''));
            $urgent = (string) ($data['urgent'] ?? '');
            if ($urgent === 'YES' || $urgent === 'NO') {
                $update['urgent'] = $urgent;
            } elseif ($priority !== '') {
                $update['urgent'] = str_contains($priority, 'urgent') ? 'YES' : 'NO';
            }
        }

        $logDescription = 'Updated BPH job details.';
        if ($request->boolean('bph_additional_info_save')) {
            $extra = $request->validate([
                'job_address'      => ['nullable', 'string', 'max:65535'],
                'climate_zone'     => ['nullable', 'string', 'max:100'],
                'compliance_summary_description' => ['nullable', 'string', 'max:65535'],
                'spec_client_no'   => ['nullable', 'string', 'max:100'],
                'spec_lbs_no'      => ['nullable', 'string', 'max:100'],
                'spec_plans'       => ['nullable', 'string', 'max:65535'],
                'spec_insulation'  => ['nullable', 'string', 'max:65535'],
                'spec_glazing'     => ['nullable', 'string', 'max:65535'],
                'spec_sealing'     => ['nullable', 'string', 'max:65535'],
                'spec_services'    => ['nullable', 'string', 'max:65535'],
                'spec_additional'  => ['nullable', 'string', 'max:65535'],
                'spec_print_merge_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,gif,bmp,webp', 'max:20480'],
            ]);

            $update['date'] = now('Asia/Manila')->toDateString();
            $update['address'] = $extra['job_address'] ?? null;
            $update['climate_zone'] = $extra['climate_zone'] ?? null;
            $update['compliance_summary_description'] = $extra['compliance_summary_description'] ?? null;
            $update['spec_client_no'] = $extra['spec_client_no'] ?? null;
            $update['spec_lbs_no'] = $extra['spec_lbs_no'] ?? null;
            $update['spec_plans'] = $extra['spec_plans'] ?? null;
            $update['spec_insulation'] = $extra['spec_insulation'] ?? null;
            $update['spec_glazing'] = $extra['spec_glazing'] ?? null;
            $update['spec_sealing'] = $extra['spec_sealing'] ?? null;
            $update['spec_services'] = $extra['spec_services'] ?? null;
            $update['spec_additional'] = $extra['spec_additional'] ?? null;

            $folderSeg = $this->bphDocumentFolderSegment($job);
            if ($request->hasFile('spec_print_merge_file') && Schema::hasColumn('job_bph', 'spec_print_merge_file')) {
                $prev = (string) ($job->spec_print_merge_file ?? '');
                if ($prev !== '') {
                    $oldPath = 'bph-documents/' . $folderSeg . '/merge/' . $prev;
                    if (Storage::disk('local')->exists($oldPath)) {
                        Storage::disk('local')->delete($oldPath);
                    }
                }
                $newName = $this->storeSpecPrintMergeFile($request, $folderSeg);
                if ($newName !== null) {
                    $update['spec_print_merge_file'] = $newName;
                }
            }
            $logDescription = 'Updated additional / specification information.';
        }

        $update['updated_at'] = now('Asia/Manila');

        DB::table('job_bph')->where('id', $id)->update($update);

        $log = $this->createBphActivityLog(
            (int) $id,
            'Job updated',
            $logDescription,
            session('user_name') ?? 'LUNTIAN'
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Job updated successfully.',
                'logs' => [[
                    'activity_date' => $log->activity_date,
                    'activity_type' => $log->activity_type,
                    'activity_description' => $log->activity_description,
                    'updated_by' => $log->updated_by,
                ]],
            ]);
        }

        return redirect()->route('bph.view', $id)->with('success', 'BPH job updated successfully.');
    }

    public function addRunComment(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        $data = $request->validate(['message' => ['required', 'string']]);
        $now = now('Asia/Manila');
        $createdAt = $now->format('M d, Y h:i A');
        $name = session('user_name') ?? 'LUNTIAN';
        $nextId = (int) DB::table('bph_run_comments')->max('run_comment_id') + 1;
        DB::table('bph_run_comments')->insert([
            'run_comment_id' => $nextId,
            'job_id' => (int) $id,
            'name' => $name,
            'message' => $data['message'],
            'created_at' => $createdAt,
        ]);
        $this->createBphActivityLog((int) $id, 'Run comment', $data['message'], $name);
        return response()->json([
            'status' => 'success',
            'message' => 'Run comment added.',
            'comment' => [
                'run_comment_id' => $nextId,
                'job_id' => (int) $id,
                'name' => $name,
                'message' => $data['message'],
                'created_at' => $createdAt,
                'profile_image_url' => session('user_profile_image') ? route('account.settings.image') : null,
            ],
        ]);
    }

    public function addJobComment(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        $data = $request->validate(['message' => ['required', 'string']]);
        $now = now('Asia/Manila');
        $createdAt = $now->format('M d, Y h:i A');
        $name = session('user_name') ?? 'LUNTIAN';
        $nextId = (int) DB::table('bph_comments')->max('comment_id') + 1;
        DB::table('bph_comments')->insert([
            'comment_id' => $nextId,
            'job_id' => (int) $id,
            'username' => $name,
            'message' => $data['message'],
            'created_at' => $createdAt,
        ]);
        $this->createBphActivityLog((int) $id, 'Comment', $data['message'], $name);
        return response()->json([
            'status' => 'success',
            'message' => 'Comment added.',
            'comment' => [
                'comment_id' => $nextId,
                'job_id' => (int) $id,
                'username' => $name,
                'message' => $data['message'],
                'created_at' => $createdAt,
                'profile_image_url' => session('user_profile_image') ? route('account.settings.image') : null,
            ],
        ]);
    }

    public function uploadFiles(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        $request->validate([
            'section' => ['required', 'string', 'in:plans,documents'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:51200'],
            'existing_files_json' => ['nullable', 'string'],
        ]);
        $section = $request->input('section');
        $column = $section === 'plans' ? 'plans_files' : 'docs_files';
        $existing = [];
        $existingRaw = $request->input('existing_files_json');
        if (is_string($existingRaw) && $existingRaw !== '') {
            $decoded = json_decode($existingRaw, true);
            if (is_array($decoded)) {
                $existing = array_values(array_filter(array_map('strval', $decoded)));
            }
        } else {
            $decoded = json_decode((string) ($job->{$column} ?? '[]'), true);
            if (is_array($decoded)) {
                $existing = array_values(array_filter(array_map('strval', $decoded)));
            }
        }
        $folderName = preg_replace('/[^A-Za-z0-9\-\_]/', '_', (string) ($job->reference ?? 'job_' . $id));
        $saved = $existing;
        foreach ((array) $request->file('files', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            Storage::disk('local')->putFileAs('bph-documents/' . $folderName, $file, $safeName);
            $saved[] = $safeName;
        }
        $saved = array_values(array_unique($saved));
        DB::table('job_bph')->where('id', $id)->update([$column => json_encode($saved), 'updated_at' => now('Asia/Manila')]);
        $type = $section === 'plans' ? 'Plans uploaded' : 'Documents uploaded';
        $log = $this->createBphActivityLog(
            (int) $id,
            $type,
            implode(', ', $saved),
            session('user_name') ?? 'LUNTIAN'
        );
        return response()->json([
            'status' => 'success',
            'message' => 'Files uploaded successfully.',
            'files' => $saved,
            'log' => [
                'activity_date' => $log->activity_date,
                'activity_type' => $log->activity_type,
                'activity_description' => $log->activity_description,
                'updated_by' => $log->updated_by,
            ],
        ]);
    }

    public function deleteFile(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        $request->validate([
            'section' => ['required', 'string', 'in:plans,documents'],
            'file_name' => ['required', 'string', 'max:500'],
        ]);
        $section = $request->input('section');
        $column = $section === 'plans' ? 'plans_files' : 'docs_files';
        $list = json_decode((string) ($job->{$column} ?? '[]'), true);
        if (!is_array($list)) $list = [];
        $fileName = (string) $request->input('file_name');
        $list = array_values(array_filter($list, fn ($n) => (string) $n !== $fileName));
        DB::table('job_bph')->where('id', $id)->update([$column => json_encode($list), 'updated_at' => now('Asia/Manila')]);
        $folderName = preg_replace('/[^A-Za-z0-9\-\_]/', '_', (string) ($job->reference ?? 'job_' . $id));
        Storage::disk('local')->delete('bph-documents/' . $folderName . '/' . $fileName);
        $log = $this->createBphActivityLog(
            (int) $id,
            'File deleted',
            (($section === 'plans') ? 'Plans: ' : 'Documents: ') . $fileName,
            session('user_name') ?? 'LUNTIAN'
        );
        return response()->json([
            'status' => 'success',
            'message' => 'File removed.',
            'files' => $list,
            'log' => [
                'activity_date' => $log->activity_date,
                'activity_type' => $log->activity_type,
                'activity_description' => $log->activity_description,
                'updated_by' => $log->updated_by,
            ],
        ]);
    }

    public function downloadFile(int $id, string $file)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) abort(404);
        $folderName = preg_replace('/[^A-Za-z0-9\-\_]/', '_', (string) ($job->reference ?? 'job_' . $id));
        $path = 'bph-documents/' . $folderName . '/' . $file;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return Storage::disk('local')->download($path, $file);
    }

    public function downloadMergeFile(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            abort(404);
        }
        $name = (string) ($job->spec_print_merge_file ?? '');
        if ($name === '') {
            abort(404);
        }
        $folderSeg = $this->bphDocumentFolderSegment($job);
        $path = 'bph-documents/' . $folderSeg . '/merge/' . $name;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return Storage::disk('local')->download($path, $name);
    }

    public function uploadCheckerFiles(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['file', 'max:51200'],
            'notes' => ['nullable', 'string'],
        ]);
        $folderName = preg_replace('/[^A-Za-z0-9\-\_]/', '_', (string) ($job->reference ?? 'job_' . $id));
        $fileNames = [];
        foreach ((array) $request->file('files', []) as $file) {
            if (!$file || !$file->isValid()) continue;
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            Storage::disk('local')->putFileAs('bph-documents/' . $folderName, $file, $safeName);
            $fileNames[] = $safeName;
        }
        if (empty($fileNames)) {
            return response()->json(['status' => 'error', 'message' => 'No valid files to upload.'], 422);
        }
        $now = now('Asia/Manila');
        $nextId = (int) DB::table('bph_staff_uploaded_files')->max('file_id') + 1;
        DB::table('bph_staff_uploaded_files')->insert([
            'file_id' => $nextId,
            'job_id' => (int) $id,
            'files_json' => json_encode($fileNames),
            'comment' => $request->input('notes') ?? '',
            'uploaded_at' => $now->format('Y-m-d H:i:s'),
            'uploaded_by' => session('user_name') ?? 'LUNTIAN',
        ]);
        $this->createBphActivityLog(
            (int) $id,
            'Checker upload',
            'Checker upload files: ' . implode(', ', $fileNames),
            session('user_name') ?? 'LUNTIAN'
        );
        return response()->json(['status' => 'success', 'message' => 'Checker files uploaded.']);
    }

    public function archiveJob(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        DB::table('job_bph')->where('id', $id)->update([
            'status' => 'Archived',
            'updated_at' => now('Asia/Manila'),
        ]);
        $this->createBphActivityLog(
            (int) $id,
            'Archive',
            'Job archived.',
            session('user_name') ?? 'LUNTIAN'
        );
        return response()->json([
            'status' => 'success',
            'message' => 'Job archived successfully.',
            'redirect' => route('bph.list'),
        ]);
    }

    private function bphDocumentFolderSegment(object $job): string
    {
        $ref = (string) ($job->reference ?? '');
        $seg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $ref);

        return $seg !== '' ? $seg : ('job_' . (int) ($job->id ?? 0));
    }

    private function specMergeImageDataUri(object $job): ?string
    {
        $name = (string) ($job->spec_print_merge_file ?? '');
        if ($name === '') {
            return null;
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true)) {
            return null;
        }
        $path = Storage::disk('local')->path('bph-documents/'.$this->bphDocumentFolderSegment($job).'/merge/'.$name);
        if (! is_readable($path)) {
            return null;
        }
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            default => 'application/octet-stream',
        };
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($raw);
    }

    private function storeSpecPrintMergeFile(Request $request, string $folderSeg): ?string
    {
        if (!$request->hasFile('spec_print_merge_file')) {
            return null;
        }
        $file = $request->file('spec_print_merge_file');
        if (!$file || !$file->isValid()) {
            return null;
        }
        Storage::disk('local')->makeDirectory('bph-documents/' . $folderSeg . '/merge');
        $original = $file->getClientOriginalName() ?: $file->hashName();
        $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
        Storage::disk('local')->putFileAs('bph-documents/' . $folderSeg . '/merge', $file, $safeName);

        return $safeName;
    }

    private function createBphActivityLog(int $jobId, string $type, string $description, string $updatedBy): object
    {
        $now = now('Asia/Manila');
        $id = DB::table('bph_activity_logs')->insertGetId([
            'job_id' => $jobId,
            'activity_date' => $now->format('Y-m-d H:i:s'),
            'activity_type' => $type,
            'activity_description' => $description,
            'updated_by' => $updatedBy,
        ]);

        return (object) [
            'id' => $id,
            'job_id' => $jobId,
            'activity_date' => $now->format('Y-m-d H:i:s'),
            'activity_type' => $type,
            'activity_description' => $description,
            'updated_by' => $updatedBy,
        ];
    }

    /**
     * Logo for email HTML (same approach as LBS).
     */
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

        if (function_exists('imagecreatefromstring') && function_exists('imagepng') && function_exists('imagesx')) {
            $img = @imagecreatefromstring($raw);
            if ($img) {
                $w = imagesx($img);
                $h = imagesy($img);
                $maxW = 180;
                $newW = min($w, $maxW);
                $newH = (int) round($h * ($newW / $w));
                $out = @imagecreatetruecolor($newW, $newH);
                if ($out) {
                    imagealphablending($out, false);
                    imagesavealpha($out, true);
                    $trans = imagecolorallocatealpha($out, 255, 255, 255, 127);
                    imagefill($out, 0, 0, $trans);
                    imagecopyresampled($out, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                    imagedestroy($img);
                    ob_start();
                    imagepng($out, null, 6);
                    $bin = ob_get_clean();
                    imagedestroy($out);
                    if ($bin !== false && $bin !== '') {
                        return 'data:image/png;base64,' . base64_encode($bin);
                    }
                } else {
                    imagedestroy($img);
                }
            }
        }

        if (strlen($raw) <= $maxEmbedBytes) {
            return 'data:image/png;base64,' . base64_encode($raw);
        }

        return config('app.url') . '/storage/logo-light.png';
    }
}
