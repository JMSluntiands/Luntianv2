<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailAmt;
use App\Models\Compliance;
use App\Models\EmailConfig;
use App\Models\JobRequest;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use App\Services\JobCountsScope;
use App\Services\SlackWebhookResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AmtJobController extends Controller
{
    private const AMT_CLIENT_CODE = 'AMT01';

    private const STORAGE_BASE = 'amt-documents';

    private const JOB_TABLE = 'job_amt';

    private function assertAmtJob(int $id): object
    {
        $job = DB::table(self::JOB_TABLE)
            ->where('id', $id)
            ->where('client_code', self::AMT_CLIENT_CODE)
            ->first();
        if (! $job) {
            abort(404);
        }

        return $job;
    }

    /**
     * Add A&M job form — same dropdown sources as BPH / Bluinq.
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

        $amtClientEmails = ClientEmailAmt::orderBy('email')->get(['id', 'email']);

        $defaultCompliance = $compliances->first(fn ($c) => $c->column && stripos((string) $c->column, '2019') !== false)
            ?? $compliances->first();
        $defaultJobRequest = $jobRequests->first(fn ($jr) => $jr->job_request_type && stripos((string) $jr->job_request_type, 'amt') !== false)
            ?? $jobRequests->first(fn ($jr) => $jr->job_request_type && stripos((string) $jr->job_request_type, 'query') !== false)
            ?? $jobRequests->first();

        return view('amt.add', [
            'sidebar_active' => 'amt.add',
            'compliances' => $compliances,
            'jobRequests' => $jobRequests,
            'assignmentUsers' => $assignmentUsers,
            'amtClientEmails' => $amtClientEmails,
            'defaultComplianceId' => $defaultCompliance?->id,
            'defaultJobRequestId' => $defaultJobRequest?->id,
        ]);
    }

    public function list()
    {
        return view('amt.list', ['sidebar_active' => 'amt.list']);
    }

    /** Request for quotation — placeholder before job list workflow. */
    public function rfq()
    {
        return view('amt.rfq', ['sidebar_active' => 'amt.rfq']);
    }

    /** Purchase — placeholder after RFQ, before job list. */
    public function purchase()
    {
        return view('amt.purchase', ['sidebar_active' => 'amt.purchase']);
    }

    /**
     * Job detail page for A&M rows (`job_amt`, client AMT01). Same template as BPH with A&M-scoped permissions.
     */
    public function show(int $id)
    {
        $job = $this->assertAmtJob($id);

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
        $jobRequests = JobRequest::orderBy('job_request_type')->get(['job_request_type']);
        $amtClientEmails = ClientEmailAmt::orderBy('email')->get(['email']);
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
            'sidebar_active' => 'amt.list',
            'isEfficientLiving' => false,
            'isBphView' => true,
            'jobViewModuleKey' => 'amt',
            'jobViewCardModuleKey' => 'amt',
            'bphJobRow' => $job,
            'listRouteName' => 'amt.list',
            'trashRouteName' => 'amt.trash',
            'jobUpdateRouteName' => 'amt.update',
            'jobUploadFilesRouteName' => 'amt.job.uploadFiles',
            'jobDeleteFileRouteName' => 'amt.job.deleteFile',
            'jobArchiveRouteName' => 'amt.job.archive',
            'jobCheckerUploadsRouteName' => 'amt.job.checkerUploads',
            'jobRunCommentRouteName' => 'amt.job.runComment',
            'jobCommentRouteName' => 'amt.job.comment',
            'jobFileRouteName' => 'amt.job.file',
            'jobPrintComplianceRouteName' => 'amt.job.printCompliance',
            'jobMergeFileRouteName' => 'amt.job.mergeFile',
            'jobCompliancePdfFilenamePrefix' => 'A&M',
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
            'bphClientEmails' => $amtClientEmails,
            'assignmentUsers' => $assignmentUsers,
        ]);
    }

    public function completed()
    {
        return view('amt.completed', ['sidebar_active' => 'amt.completed']);
    }

    public function review()
    {
        return view('amt.review', ['sidebar_active' => 'amt.review']);
    }

    /**
     * A&M jobs in For Email Confirmation — same UX as {@see BphJobController::mailbox}.
     */
    public function mailbox()
    {
        $jobs = collect();
        if (Schema::hasTable(self::JOB_TABLE)) {
            $q = DB::table(self::JOB_TABLE)
                ->where('client_code', self::AMT_CLIENT_CODE)
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

        return view('amt.mailbox', [
            'sidebar_active' => 'amt.mailbox',
            'jobs' => $jobs,
        ]);
    }

    public function emailPreview(int $id)
    {
        if (DB::table(self::JOB_TABLE)->where('id', $id)->where('client_code', self::AMT_CLIENT_CODE)->doesntExist()) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->emailPreview($id);
        });
    }

    public function sendMailboxEmail(Request $request, int $id)
    {
        if (DB::table(self::JOB_TABLE)->where('id', $id)->where('client_code', self::AMT_CLIENT_CODE)->doesntExist()) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->sendMailboxEmail($request, $id);
        });
    }

    /**
     * Same rules as {@see BphJobController::update}; restricted to A&M jobs (client_code).
     */
    public function update(Request $request, int $id)
    {
        $job = DB::table(self::JOB_TABLE)
            ->where('id', $id)
            ->where('client_code', self::AMT_CLIENT_CODE)
            ->first();
        if (! $job) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
            }

            return redirect()->route('amt.list');
        }

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->update($request, $id);
        });
    }

    public function uploadFiles(Request $request, int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->uploadFiles($request, $id);
        });
    }

    public function deleteFile(Request $request, int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->deleteFile($request, $id);
        });
    }

    public function downloadFile(int $id, string $file)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id, $file) {
            return app(BphJobController::class)->downloadFile($id, $file);
        });
    }

    public function downloadMergeFile(int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->downloadMergeFile($id);
        });
    }

    public function printComplianceSummary(int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->printComplianceSummary($id);
        });
    }

    public function uploadCheckerFiles(Request $request, int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->uploadCheckerFiles($request, $id);
        });
    }

    public function addRunComment(Request $request, int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->addRunComment($request, $id);
        });
    }

    public function addJobComment(Request $request, int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->addJobComment($request, $id);
        });
    }

    public function archiveJob(int $id)
    {
        $this->assertAmtJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->archiveJob($id);
        });
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ncc_compliance'   => ['nullable', 'integer'],
            'job_type_request' => ['nullable', 'integer'],
            'contact_email'    => ['required', 'email', 'max:255'],
            'notes'            => ['nullable', 'string'],
            'assigned_to'      => ['required', 'string', 'max:50'],
            'checked_by'       => ['required', 'string', 'max:50'],
            'urgent_job'       => ['nullable'],
        ]);

        if (! Schema::hasTable(self::JOB_TABLE)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database table '.self::JOB_TABLE.' is not available. Run migrations.',
            ], 500);
        }

        $compliance = !empty($data['ncc_compliance']) ? Compliance::find($data['ncc_compliance']) : null;
        $jobRequest = !empty($data['job_type_request']) ? JobRequest::find($data['job_type_request']) : null;

        $nccText = $compliance->column ?? '2019';
        $jobTypeText = $jobRequest->job_request_type ?? '—';

        $headerRef = trim((string) $request->input('header_reference', ''));
        $reference = $headerRef !== '' ? $headerRef : ('A&M-' . now('Asia/Manila')->format('YmdHis'));
        $reference = substr($reference, 0, 50);

        $now = now('Asia/Manila');
        $urgent = $request->boolean('urgent_job') ? 'YES' : 'NO';

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $reference) ?: 'amt_upload';

        $base = self::STORAGE_BASE . '/' . $folderSeg;

        $planNames = [];
        foreach ((array) $request->file('upload_plans', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $path = $base . '/' . $safeName;
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
            $path = $base . '/' . $safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $docNames[] = $safeName;
        }

        try {
            $nextId = (int) DB::table(self::JOB_TABLE)->max('id') + 1;

            // Job number / client name not on form; keep DB shape (5 digits + B, max 6 chars).
            $jobNum = strtoupper(sprintf('%05dB', (($nextId - 1) % 99999) + 1));
            $clientName = '';
            $contactEmail = $data['contact_email'];

            $row = [
                'id'                  => $nextId,
                'reference'           => $reference,
                'client_code'         => self::AMT_CLIENT_CODE,
                'urgent'              => $urgent,
                'job_type'            => substr($jobTypeText, 0, 100),
                'ncc'                 => substr((string) $nccText, 0, 255),
                'job_number'          => $jobNum,
                'client_name'         => $clientName,
                'contact_email'       => $contactEmail,
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
            if (Schema::hasColumn(self::JOB_TABLE, 'spec_print_merge_file')) {
                $row['spec_print_merge_file'] = null;
            }
            DB::table(self::JOB_TABLE)->insert($row);
            $id = $nextId;
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'A&M job created successfully.',
            'job_id'  => $id,
        ]);
    }

    public function sendSlackNotification(int $id)
    {
        $job = DB::table(self::JOB_TABLE)
            ->where('id', $id)
            ->where('client_code', self::AMT_CLIENT_CODE)
            ->first();
        if (! $job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $slackWebhook = SlackWebhookResolver::newJobWebhook();

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
                'text' => '🆕 New A&M Job Submitted',
                'attachments' => [
                    [
                        'color' => '#0d9488',
                        'fields' => [
                            ['title' => 'A&M Ref #', 'value' => $reference, 'short' => true],
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
                        'footer' => 'Luntian A&M Job Management',
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
                \Log::warning('A&M Slack notification failed', ['error' => $slackError, 'job_id' => $id]);
            }
        } catch (\Throwable $e) {
            \Log::warning('A&M Slack exception', ['message' => $e->getMessage(), 'job_id' => $id]);
        }

        return response()->json(['status' => 'success']);
    }

    public function sendSubmissionEmail(int $id)
    {
        $job = DB::table(self::JOB_TABLE)
            ->where('id', $id)
            ->where('client_code', self::AMT_CLIENT_CODE)
            ->first();
        if (! $job) {
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

        $amtRef = trim($job->reference ?? '') ?: '—';
        $jobNum = trim($job->job_number ?? '') ?: '—';
        $accountClient = $job->client_name ?? '';
        $nccCompliance = $job->ncc ?? '';
        $jobTypeLabel = $job->job_type ?? '';
        $priorityText = (($job->urgent ?? '') === 'YES') ? 'Urgent' : '—';

        $jobTypeShort = strtoupper(Str::limit(str_replace('-', '_', Str::slug($jobTypeLabel)), 12, ''));
        $headerTitle = $amtRef . '_' . ($jobTypeShort ?: 'AM') . '_' . $jobNum;

        $emailSubject = 'LUNTIAN A&M Job Submission: '
            . trim($accountClient) . ' LUNTIAN' . $amtRef . '-' . $jobNum . '-' . $nccCompliance;

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $job->reference ?? '') ?: 'amt_upload';
        $basePath = self::STORAGE_BASE . '/' . $folderSeg . '/';
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
                'lbsRef'         => $amtRef,
                'refLabel'       => 'A&M Ref #',
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
            \Log::error('A&M submission email failed', [
                'job_id' => $id,
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
}
