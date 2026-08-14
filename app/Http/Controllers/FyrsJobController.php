<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailBph;
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
use Illuminate\Validation\Rule;

class FyrsJobController extends Controller
{
    private const FYRS_CLIENT_CODE = 'FYRS01';

    private const STORAGE_BASE = 'fyrs-documents';

    private const JOB_TABLE = 'job_fyrs';

    private const ASSESSOR_TABLE = 'fyrs_assessor_jobs';

    private const ASSESSOR_TASK_KEYS = ['base_file', 'optimization', 'basix', 'duplicate', 'amendment'];

    private const ASSESSOR_TASK_LABELS = [
        'base_file' => 'Base file',
        'optimization' => 'Optimization',
        'basix' => 'BASIX',
        'duplicate' => 'Duplicate',
        'amendment' => 'Amendment',
    ];

    /** @var list<string> */
    public const BUILDERS = [
        'McDonald Jones (Complete Homes)',
        'Practical Homes',
        'Eden Brae Homes',
        'Masterton Homes',
    ];

    private function assessorJobForFyrsId(int $jobFyrsId): ?object
    {
        if (! Schema::hasTable(self::ASSESSOR_TABLE)) {
            return null;
        }

        return DB::table(self::ASSESSOR_TABLE)
            ->where('job_fyrs_id', $jobFyrsId)
            ->orderByDesc('id')
            ->first();
    }

    private function legacyJobNumberStub(string $jobNumber, int $nextId): string
    {
        $digits = preg_replace('/\D/', '', $jobNumber);
        if (strlen($digits) >= 5) {
            return substr($digits, -5).'B';
        }

        return str_pad((string) ($nextId % 100000), 5, '0', STR_PAD_LEFT).'B';
    }

    private function assessorWorkflowValidationRules(): array
    {
        return [
            'builder' => ['nullable', 'string', 'max:255'],
            'builder_other' => ['nullable', 'string', 'max:255'],
            'assigned' => ['nullable', 'string', 'max:50'],
            'tasks' => ['nullable', 'array'],
            'tasks.*' => ['string', Rule::in(self::ASSESSOR_TASK_KEYS)],
            'stage' => ['nullable', 'string', 'max:100'],
            'climate_zone' => ['nullable', 'string', 'max:100'],
            'basix_number' => ['nullable', 'string', 'max:100'],
            'storeys' => ['nullable', 'string', 'max:20'],
            'due_date' => ['nullable', 'date'],
            'est_completion_certification' => ['nullable', 'date'],
            'est_completion_basix' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'upload_files' => ['nullable', 'array'],
            'upload_files.*' => ['file', 'max:51200'],
        ];
    }

    /** @param  array<string, mixed>  $data */
    private function assessorWorkflowRowFromValidated(array $data): array
    {
        $tasks = array_values(array_filter(
            (array) ($data['tasks'] ?? []),
            fn ($task) => is_string($task) && in_array($task, self::ASSESSOR_TASK_KEYS, true)
        ));

        $row = [];

        foreach ([
            'stage',
            'climate_zone',
            'basix_number',
            'storeys',
        ] as $column) {
            $value = $data[$column] ?? null;
            $row[$column] = $value === null || $value === '' ? null : $value;
        }

        $builderChoice = trim((string) ($data['builder'] ?? ''));
        if ($builderChoice === '__other__') {
            $builderChoice = trim((string) ($data['builder_other'] ?? ''));
        }
        $row['builder'] = $builderChoice !== '' ? $builderChoice : null;

        $row['tasks'] = $tasks === [] ? null : json_encode($tasks);

        foreach (['due_date', 'est_completion_certification', 'est_completion_basix'] as $dateColumn) {
            $row[$dateColumn] = ! empty($data[$dateColumn]) ? $data[$dateColumn] : null;
        }

        return $row;
    }

    private function assertFyrsJob(int $id): object
    {
        $job = DB::table(self::JOB_TABLE)
            ->where('id', $id)
            ->where('client_code', self::FYRS_CLIENT_CODE)
            ->first();
        if (!$job) {
            abort(404);
        }

        return $job;
    }

    /**
     * Add Fyrs Energywise job form — NatHERS & BASIX assessor workflow (Excel columns only).
     */
    public function addForm()
    {
        $assignmentSelect = User::assignmentSelectLists('fyrs');

        return view('fyrs.add', [
            'sidebar_active' => 'fyrs.add',
            'assignmentStaffUsers' => $assignmentSelect['assignmentStaffUsers'],
        ]);
    }

    /**
     * Public FYRS add form (no login required) — same assessor fields as dashboard add.
     */
    public function publicAddForm()
    {
        $assignmentSelect = User::assignmentSelectLists('fyrs');
        $dedicatedDomain = trim((string) config('app.fyrs_public_form_domain', '')) !== '';

        return view('fyrs.add', [
            'layoutView' => 'layouts.public-form',
            'storeRoute' => route('fyrs.public.store'),
            'sendSlackBaseUrl' => $dedicatedDomain
                ? url('/job')
                : url('/fyrs/add-new/job'),
            'listUrl' => route('fyrs.public.add'),
            'cancelUrl' => route('fyrs.public.add'),
            'assignmentStaffUsers' => $assignmentSelect['assignmentStaffUsers'],
        ]);
    }

    public function list()
    {
        $excludedStatuses = [
            'completed',
            'for review',
            'for email confirmation',
            'archived',
            'archive',
        ];

        $rows = collect();
        if (Schema::hasTable(self::ASSESSOR_TABLE) && Schema::hasTable(self::JOB_TABLE)) {
            $q = DB::table(self::ASSESSOR_TABLE.' as a')
                ->join(self::JOB_TABLE.' as j', 'j.id', '=', 'a.job_fyrs_id')
                ->whereRaw('LOWER(TRIM(j.client_code)) = ?', [strtolower(self::FYRS_CLIENT_CODE)])
                ->whereRaw(
                    '(j.status IS NULL OR LOWER(TRIM(j.status)) NOT IN ('.implode(',', array_fill(0, count($excludedStatuses), '?')).'))',
                    $excludedStatuses
                )
                ->select(
                    'a.*',
                    'j.id as job_id',
                    'j.assigned as assigned',
                    'j.status as job_status',
                    'j.address as address'
                );

            JobCountsScope::applyJobBphAssignment($q);
            JobCountsScope::applyJobBphBranchVerticalScope($q);

            $rows = $q->orderByDesc('a.created_at')->limit(300)->get();
        }

        $assignmentSelect = User::assignmentSelectLists('fyrs');
        $assignmentStaffCodes = $assignmentSelect['assignmentStaffUsers']
            ->map(fn ($u) => strtoupper(trim((string) ($u->unique_code ?? ''))))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $stagePresets = ['Allocated', 'Processing', 'for BASIX', 'BASIX', 'Completed'];
        $stageOptions = collect($stagePresets)
            ->merge($rows->pluck('stage')->filter())
            ->map(fn ($s) => trim((string) $s))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $builderOptions = $rows
            ->map(fn ($r) => trim((string) ($r->builder ?? $r->estate ?? '')))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return view('fyrs.list', [
            'sidebar_active' => 'fyrs.list',
            'rows' => $rows,
            'taskLabels' => self::ASSESSOR_TASK_LABELS,
            'assignmentStaffCodes' => $assignmentStaffCodes,
            'stageOptions' => $stageOptions,
            'builderOptions' => $builderOptions,
        ]);
    }

    /**
     * Job detail for Fyrs Energywise rows (`job_fyrs`, client FYRS01). Same template as BPH with fyrs-scoped permissions.
     */
    public function show(int $id)
    {
        $job = $this->assertFyrsJob($id);
        $assessor = $this->assessorJobForFyrsId($id);

        $viewJob = (object) [
            'job_id' => (int) $job->id,
            'reference' => $job->reference,
            'log_date' => $job->created_at,
            'client_code' => $job->client_code,
            'job_reference_no' => $assessor->job_number ?? $job->job_number,
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
            'notes' => $assessor->notes ?? $job->notes,
            'upload_files' => $job->plans_files,
            'upload_project_files' => $job->docs_files,
            'client_account_id' => null,
            'client_account_name' => $job->client_name,
            'basix_number' => $assessor->basix_number ?? $job->basix_number ?? null,
            'est_completion_certification' => $assessor->est_completion_certification ?? $job->est_completion_certification ?? null,
            'est_completion_basix' => $assessor->est_completion_basix ?? $job->est_completion_basix ?? null,
        ];

        $priorityColor = !empty($viewJob->priority)
            ? Priority::where('name', $viewJob->priority)->value('color')
            : null;
        $statusColor = !empty($viewJob->job_status)
            ? Status::where('name', $viewJob->job_status)->value('color')
            : null;

        $compliances = Compliance::orderBy('column')->get(['column']);
        $jobRequests = JobRequest::whereIn('client_code', ['FYRS01', 'BPH01', 'B1001'])
            ->orderBy('job_request_type')
            ->get(['job_request_type']);
        $bphClientEmails = ClientEmailBph::orderBy('email')->get(['email']);
        $priorities = Priority::orderBy('id')->get();
        $statuses = Status::orderBy('name')->get();
        $clientAccounts = collect();

        $assignmentSelect = User::assignmentSelectLists('fyrs');

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
            'sidebar_active' => 'fyrs.list',
            'isEfficientLiving' => false,
            'isBphView' => true,
            'jobViewModuleKey' => 'fyrs',
            'jobViewCardModuleKey' => 'fyrs',
            'bphJobRow' => $job,
            'listRouteName' => 'fyrs.list',
            'trashRouteName' => 'fyrs.trash',
            'jobUpdateRouteName' => 'fyrs.update',
            'jobUploadFilesRouteName' => 'fyrs.job.uploadFiles',
            'jobDeleteFileRouteName' => 'fyrs.job.deleteFile',
            'jobArchiveRouteName' => 'fyrs.job.archive',
            'jobCheckerUploadsRouteName' => 'fyrs.job.checkerUploads',
            'jobRunCommentRouteName' => 'fyrs.job.runComment',
            'jobCommentRouteName' => 'fyrs.job.comment',
            'jobFileRouteName' => 'fyrs.job.file',
            'jobPrintComplianceRouteName' => 'fyrs.job.printCompliance',
            'jobMergeFileRouteName' => 'fyrs.job.mergeFile',
            'jobCompliancePdfFilenamePrefix' => 'FYRS',
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
            'assignmentStaffUsers' => $assignmentSelect['assignmentStaffUsers'],
            'assignmentCheckerUsers' => $assignmentSelect['assignmentCheckerUsers'],
            'assignmentUsers' => $assignmentSelect['assignmentStaffUsers'],
        ]);
    }

    public function completed()
    {
        return view('fyrs.completed', ['sidebar_active' => 'fyrs.completed']);
    }

    public function review()
    {
        return view('fyrs.review', ['sidebar_active' => 'fyrs.review']);
    }

    public function mailbox()
    {
        $jobs = collect();
        if (Schema::hasTable(self::JOB_TABLE)) {
            $q = DB::table(self::JOB_TABLE)
                ->where('client_code', self::FYRS_CLIENT_CODE)
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

        return view('fyrs.mailbox', [
            'sidebar_active' => 'fyrs.mailbox',
            'jobs' => $jobs,
        ]);
    }

    public function emailPreview(int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->emailPreview($id);
        });
    }

    public function sendMailboxEmail(Request $request, int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->sendMailboxEmail($request, $id);
        });
    }

    public function update(Request $request, int $id)
    {
        $this->assertFyrsJob($id);

        $hasAssessorPatch = $request->exists('basix_number')
            || $request->exists('est_completion_certification')
            || $request->exists('est_completion_basix')
            || $request->exists('stage')
            || $request->exists('job_reference_no');

        if ($hasAssessorPatch && Schema::hasTable(self::ASSESSOR_TABLE)) {
            $assessorData = $request->validate([
                'basix_number' => ['nullable', 'string', 'max:100'],
                'est_completion_certification' => ['nullable', 'date'],
                'est_completion_basix' => ['nullable', 'date'],
                'stage' => ['nullable', 'string', 'max:100'],
                'job_reference_no' => ['nullable', 'string', 'max:100'],
            ]);

            $assessorUpdate = [];
            if ($request->exists('basix_number')) {
                $value = $assessorData['basix_number'] ?? null;
                $assessorUpdate['basix_number'] = ($value === null || $value === '') ? null : $value;
            }
            if ($request->exists('stage')) {
                $value = $assessorData['stage'] ?? null;
                $assessorUpdate['stage'] = ($value === null || $value === '') ? null : $value;
            }
            if ($request->exists('job_reference_no')) {
                $jobNum = trim((string) ($assessorData['job_reference_no'] ?? ''));
                $assessorUpdate['job_number'] = $jobNum !== '' ? $jobNum : null;
            }
            foreach (['est_completion_certification', 'est_completion_basix'] as $dateColumn) {
                if ($request->exists($dateColumn)) {
                    $assessorUpdate[$dateColumn] = ! empty($assessorData[$dateColumn]) ? $assessorData[$dateColumn] : null;
                }
            }
            if ($assessorUpdate !== []) {
                $assessorUpdate['updated_at'] = now('Asia/Manila');
                $existing = $this->assessorJobForFyrsId($id);
                if ($existing) {
                    DB::table(self::ASSESSOR_TABLE)->where('id', $existing->id)->update($assessorUpdate);
                }
                if (Schema::hasTable(self::JOB_TABLE)) {
                    $jobPatch = [];
                    foreach ($assessorUpdate as $col => $val) {
                        if ($col === 'updated_at' || $col === 'job_number') {
                            continue;
                        }
                        if (Schema::hasColumn(self::JOB_TABLE, $col)) {
                            $jobPatch[$col] = $val;
                        }
                    }
                    if (array_key_exists('job_number', $assessorUpdate)) {
                        $jobNum = (string) ($assessorUpdate['job_number'] ?? '');
                        $jobPatch['client_name'] = $jobNum !== '' ? substr($jobNum, 0, 255) : 'Fyrs Job';
                        $jobPatch['job_number'] = $this->legacyJobNumberStub($jobNum !== '' ? $jobNum : (string) $id, $id);
                    }
                    if ($jobPatch !== []) {
                        $jobPatch['updated_at'] = now('Asia/Manila');
                        DB::table(self::JOB_TABLE)->where('id', $id)->update($jobPatch);
                    }
                }
            }

            // BPH pipeline validates job_reference_no as max:6 — FYRS Job Ref # can be longer.
            if ($request->exists('job_reference_no')) {
                $request->request->remove('job_reference_no');
                $request->request->remove('job_number');
            }
        }

        $needsPipelineUpdate = $request->exists('job_status')
            || $request->exists('status')
            || $request->exists('staff_id')
            || $request->exists('assigned')
            || $request->exists('checker_id')
            || $request->exists('checked')
            || $request->exists('job_address')
            || $request->exists('priority')
            || $request->exists('job_type')
            || $request->exists('notes')
            || $request->exists('compliance')
            || $request->exists('ncc')
            || $request->exists('client_name')
            || $request->exists('units')
            || $request->boolean('bph_additional_info_save');

        // Stage/BASIX-only list edits should not hit the BPH updater (it may redirect back to the list → 405 on PUT).
        if ($hasAssessorPatch && ! $needsPipelineUpdate) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Job updated successfully.',
                ]);
            }

            return redirect()->route('fyrs.view', $id)->with('success', 'Job updated successfully.');
        }

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->update($request, $id);
        });
    }

    public function uploadFiles(Request $request, int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->uploadFiles($request, $id);
        });
    }

    public function deleteFile(Request $request, int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->deleteFile($request, $id);
        });
    }

    public function downloadFile(int $id, string $file)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id, $file) {
            return app(BphJobController::class)->downloadFile($id, $file);
        });
    }

    public function downloadMergeFile(int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->downloadMergeFile($id);
        });
    }

    public function printComplianceSummary(int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->printComplianceSummary($id);
        });
    }

    public function uploadCheckerFiles(Request $request, int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->uploadCheckerFiles($request, $id);
        });
    }

    public function addRunComment(Request $request, int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->addRunComment($request, $id);
        });
    }

    public function addJobComment(Request $request, int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($request, $id) {
            return app(BphJobController::class)->addJobComment($request, $id);
        });
    }

    public function archiveJob(int $id)
    {
        $this->assertFyrsJob($id);

        return BphJobController::runWithPipelineContext(self::JOB_TABLE, self::STORAGE_BASE, function () use ($id) {
            return app(BphJobController::class)->archiveJob($id);
        });
    }

    public function store(Request $request)
    {
        $data = $request->validate(array_merge([
            'job_number' => ['required', 'string', 'max:100'],
            'notes'      => ['nullable', 'string'],
        ], $this->assessorWorkflowValidationRules()));

        if (($data['builder'] ?? '') === '__other__' && trim((string) ($data['builder_other'] ?? '')) === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter the other builder name.',
                'errors' => ['builder_other' => ['Please enter the other builder name.']],
            ], 422);
        }

        if (! Schema::hasTable(self::JOB_TABLE)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database table '.self::JOB_TABLE.' is not available. Run migrations.',
            ], 500);
        }

        if (! Schema::hasTable(self::ASSESSOR_TABLE)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database table '.self::ASSESSOR_TABLE.' is not available. Run: php artisan migrate',
            ], 500);
        }

        $reference = 'FYRS-'.now('Asia/Manila')->format('YmdHis');
        $reference = substr($reference, 0, 50);

        $now = now('Asia/Manila');
        $jobNum = trim($data['job_number']);
        $clientName = $jobNum !== '' ? $jobNum : 'Fyrs Job';
        $address = isset($data['address']) ? trim((string) $data['address']) : '';
        $address = $address !== '' ? substr($address, 0, 500) : null;

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $reference) ?: 'fyrs_upload';
        $fileNames = [];
        foreach ((array) $request->file('upload_files', []) as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $path = self::STORAGE_BASE.'/'.$folderSeg.'/'.$safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $fileNames[] = $safeName;
        }

        // BASIX # and estimated completion dates are set later on Job Details edit.
        unset($data['basix_number'], $data['est_completion_certification'], $data['est_completion_basix']);

        try {
            $id = DB::transaction(function () use ($data, $reference, $now, $jobNum, $clientName, $address, $fileNames) {
                $nextId = (int) DB::table(self::JOB_TABLE)->max('id') + 1;
                $legacyJobNum = $this->legacyJobNumberStub($jobNum, $nextId);

                $jobRow = [
                    'id'                  => $nextId,
                    'reference'           => $reference,
                    'client_code'         => self::FYRS_CLIENT_CODE,
                    'urgent'              => 'NO',
                    'job_type'            => 'Fyrs Energywise',
                    'ncc'                 => '2019',
                    'job_number'          => $legacyJobNum,
                    'client_name'         => substr($clientName, 0, 255),
                    'contact_email'       => 'fyrs@luntian.local',
                    'notes'               => $data['notes'] ?? null,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                    'assigned'            => isset($data['assigned']) && trim((string) $data['assigned']) !== ''
                        ? strtoupper(trim((string) $data['assigned']))
                        : null,
                    'checked'             => null,
                    'plans_files'         => json_encode([]),
                    'docs_files'          => json_encode($fileNames),
                    'status'              => 'Allocated',
                    'date'                => $now->toDateString(),
                    'address'             => $address,
                    'climate_zone'        => isset($data['climate_zone']) ? substr((string) $data['climate_zone'], 0, 100) : null,
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
                    $jobRow['spec_print_merge_file'] = null;
                }
                DB::table(self::JOB_TABLE)->insert($jobRow);

                $assessorRow = array_merge([
                    'job_fyrs_id' => $nextId,
                    'reference'   => $reference,
                    'job_date'    => $now->toDateString(),
                    'job_number'  => $jobNum,
                    'notes'       => $data['notes'] ?? null,
                    'status'      => 'Allocated',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ], $this->assessorWorkflowRowFromValidated($data));

                DB::table(self::ASSESSOR_TABLE)->insert($assessorRow);

                return $nextId;
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Fyrs Energywise job created successfully.',
            'job_id'  => $id,
        ]);
    }

    public function sendSlackNotification(int $id)
    {
        $job = $this->assertFyrsJob($id);
        $assessor = $this->assessorJobForFyrsId($id);

        $slackWebhook = SlackWebhookResolver::newJobWebhook();

        if (!$slackWebhook) {
            return response()->json(['status' => 'success', 'message' => 'Slack not configured.']);
        }

        $reference = $job->reference ?? '';
        $jobNum = $assessor->job_number ?? $job->job_number ?? '';
        $clientName = $assessor->builder ?? $job->client_name ?? '';
        $status = $job->status ?? '';
        $ncc = $job->ncc ?? '';
        $jobType = $job->job_type ?? '';
        $urgent = $job->urgent ?? '';
        $contact = $job->contact_email ?? '';
        $assigned = $job->assigned ?? '';
        $checked = $job->checked ?? '';

        try {
            $slackMessage = [
                'text' => '🆕 New Fyrs Energywise Job Submitted',
                'attachments' => [
                    [
                        'color' => '#0d9488',
                        'fields' => [
                            ['title' => 'Fyrs Ref #', 'value' => $reference, 'short' => true],
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
                        'footer' => 'Luntian Fyrs Energywise Job Management',
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
                \Log::warning('Fyrs Energywise Slack notification failed', ['error' => $slackError, 'job_id' => $id]);
            }
        } catch (\Throwable $e) {
            \Log::warning('Fyrs Energywise Slack exception', ['message' => $e->getMessage(), 'job_id' => $id]);
        }

        return response()->json(['status' => 'success']);
    }

    public function sendSubmissionEmail(int $id)
    {
        $job = $this->assertFyrsJob($id);

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

        $fyrsRef = trim($job->reference ?? '') ?: '—';
        $jobNum = trim($job->job_number ?? '') ?: '—';
        $accountClient = $job->client_name ?? '';
        $nccCompliance = $job->ncc ?? '';
        $jobTypeLabel = $job->job_type ?? '';
        $priorityText = (($job->urgent ?? '') === 'YES') ? 'Urgent' : '—';

        $jobTypeShort = strtoupper(Str::limit(str_replace('-', '_', Str::slug($jobTypeLabel)), 12, ''));
        $headerTitle = $fyrsRef . '_' . ($jobTypeShort ?: 'FYRS') . '_' . $jobNum;

        $emailSubject = 'LUNTIAN Fyrs Energywise Job Submission: '
            . trim($accountClient) . ' LUNTIAN' . $fyrsRef . '-' . $jobNum . '-' . $nccCompliance;

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $job->reference ?? '') ?: 'fyrs_upload';
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
                'lbsRef'         => $fyrsRef,
                'refLabel'       => 'Fyrs Ref #',
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
            \Log::error('Fyrs Energywise submission email failed', [
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
