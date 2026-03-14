<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use App\Models\Compliance;
use App\Models\JobRequest;
use App\Models\ActivityLog;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class LbsJobController extends Controller
{
    public function show(int $id)
    {
        $job = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->select(
                'j.job_id',
                'j.reference',
                'j.log_date',
                'j.client_code',
                'j.job_reference_no',
                'j.client_reference_no',
                'j.staff_id',
                'j.checker_id',
                'j.ncc_compliance',
                'j.job_request_id',
                'j.address_client',
                'j.job_type',
                'j.priority',
                'j.plan_complexity',
                'j.job_status',
                'j.completion_date',
                'j.notes',
                'j.upload_files',
                'j.upload_project_files',
                'j.client_account_id',
                'ca.client_account_name'
            )
            ->where('j.job_id', $id)
            ->first();

        if (!$job) {
            abort(404);
        }

        // Resolve colors for this job's priority & status from lookup tables
        $priorityColor = !empty($job->priority)
            ? Priority::where('name', $job->priority)->value('color')
            : null;

        $statusColor = !empty($job->job_status)
            ? Status::where('name', $job->job_status)->value('color')
            : null;

        // Dropdown data for editing
        $priorities    = Priority::orderBy('id')->get();
        $statuses      = Status::orderBy('name')->get();
        $compliances   = Compliance::orderBy('column')->get();
        $clientAccounts = ClientAccount::orderBy('client_account_name')->get();
        $jobRequests   = JobRequest::orderBy('job_request_type')->get();

        $activityLogs = ActivityLog::where('job_id', (int) $job->job_id)
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

        $checkerUploads = DB::table('staff_uploaded_files')
            ->where('job_id', (int) $job->job_id)
            ->orderByDesc('uploaded_at')
            ->get();

        $assignmentUsers = User::whereIn('role', ['staff', 'checker'])
            ->orderBy('unique_code')
            ->get(['id', 'unique_code'])
            ->unique('unique_code')
            ->values();

        $runComments = DB::table('run_comments')
            ->where('job_id', (int) $job->job_id)
            ->orderByDesc('run_comment_id')
            ->limit(50)
            ->get();

        $jobComments = DB::table('comments')
            ->where('job_id', (int) $job->job_id)
            ->orderByDesc('comment_id')
            ->limit(50)
            ->get();

        return view('lbs.view', [
            'sidebar_active'   => 'lbs.list',
            'jobId'            => $job->job_id,
            'job'              => $job,
            'priorityColor'    => $priorityColor,
            'statusColor'      => $statusColor,
            'priorities'       => $priorities,
            'statuses'         => $statuses,
            'compliances'      => $compliances,
            'clientAccounts'   => $clientAccounts,
            'jobRequests'      => $jobRequests,
            'activityLogs'     => $activityLogs,
            'userRoleMap'      => $userRoleMap,
            'assignmentUsers'  => $assignmentUsers,
            'checkerUploads'   => $checkerUploads,
            'runComments'      => $runComments,
            'jobComments'      => $jobComments,
        ]);
    }

    public function addRunComment(Request $request, int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $now = now('Asia/Manila');
        $createdAt = $now->format('M d, Y h:i A');
        $name = session('user_name') ?? 'LUNTIAN';

        // Handle environments where AUTO_INCREMENT might not be configured correctly
        $nextId = (int) DB::table('run_comments')->max('run_comment_id') + 1;

        DB::table('run_comments')->insert([
            'run_comment_id' => $nextId,
            'job_id'         => (int) $id,
            'name'           => $name,
            'message'        => $data['message'],
            'created_at'     => $createdAt,
        ]);

        ActivityLog::create([
            'job_id'               => (int) $id,
            'activity_date'        => $now->format('Y-m-d H:i:s'),
            'activity_type'        => 'Run comment',
            'activity_description' => $data['message'],
            'updated_by'           => $name,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Run comment added.',
            'comment' => [
                'run_comment_id' => $nextId,
                'job_id'         => (int) $id,
                'name'           => $name,
                'message'        => $data['message'],
                'created_at'     => $createdAt,
            ],
        ]);
    }

    public function addJobComment(Request $request, int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $now = now('Asia/Manila');
        $createdAt = $now->format('M d, Y h:i A');
        $name = session('user_name') ?? 'LUNTIAN';
        $nextId = (int) DB::table('comments')->max('comment_id') + 1;

        DB::table('comments')->insert([
            'comment_id' => $nextId,
            'job_id'     => (int) $id,
            'username'   => $name,
            'message'    => $data['message'],
            'created_at' => $createdAt,
        ]);

        ActivityLog::create([
            'job_id'               => (int) $id,
            'activity_date'        => $now->format('Y-m-d H:i:s'),
            'activity_type'        => 'Comment',
            'activity_description' => $data['message'],
            'updated_by'           => $name,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Comment added.',
            'comment' => [
                'comment_id' => $nextId,
                'job_id'     => (int) $id,
                'username'   => $name,
                'message'    => $data['message'],
                'created_at' => $createdAt,
            ],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found.',
            ], 404);
        }

        $data = $request->validate([
            'job_status'        => ['nullable', 'string', 'max:50'],
            'job_address'       => ['nullable', 'string', 'max:1000'],
            'priority'          => ['nullable', 'string', 'max:255'],
            'job_type'          => ['nullable', 'string', 'max:255'],
            'notes'             => ['nullable', 'string', 'max:65535'],
            'client_reference'  => ['nullable', 'string', 'max:255'],
            'job_reference_no'  => ['nullable', 'string', 'max:255'],
            'compliance'        => ['nullable', 'string', 'max:255'],
            'client_id'         => ['nullable', 'integer'],
            'staff_id'          => ['nullable', 'string', 'max:50'],
            'checker_id'        => ['nullable', 'string', 'max:50'],
            'plan_complexity'   => ['nullable', 'integer', 'between:1,5'],
        ]);

        $update = [];
        $changes = [];
        $oldClient = null;
        if (!empty($job->client_account_id)) {
            $oldClient = ClientAccount::find($job->client_account_id);
        }

        if (array_key_exists('job_status', $data) && $data['job_status'] !== null) {
            $new = ucfirst($data['job_status']);
            if ($new !== $job->job_status) {
                $update['job_status'] = $new;
                $changes[] = [
                    'field' => 'Job Status',
                    'old'   => $job->job_status,
                    'new'   => $new,
                ];
            }
        }
        if (array_key_exists('job_address', $data)) {
            $new = $data['job_address'];
            if ($new !== $job->address_client) {
                $update['address_client'] = $new;
                $changes[] = [
                    'field' => 'Job Address',
                    'old'   => $job->address_client,
                    'new'   => $new,
                ];
            }
        }
        if (array_key_exists('priority', $data)) {
            $new = $data['priority'];
            if ($new !== $job->priority) {
                $update['priority'] = $new;
                $changes[] = [
                    'field' => 'Priority',
                    'old'   => $job->priority,
                    'new'   => $new,
                ];
            }
        }
        if (array_key_exists('job_type', $data)) {
            $new = $data['job_type'];
            if ($new !== $job->job_type) {
                $update['job_type'] = $new;
                $changes[] = [
                    'field' => 'Job Type',
                    'old'   => $job->job_type,
                    'new'   => $new,
                ];
            }
        }
        if (array_key_exists('notes', $data)) {
            $new = $data['notes'] ?? '';
            $old = $job->notes ?? '';
            if ((string) $new !== (string) $old) {
                $update['notes'] = $new;
                $oldPreview = trim(preg_replace('/\s+/', ' ', strip_tags($old ?? '')));
                $newPreview = trim(preg_replace('/\s+/', ' ', strip_tags($new ?? '')));
                $maxLen = 120;
                if (mb_strlen($oldPreview) > $maxLen) {
                    $oldPreview = mb_substr($oldPreview, 0, $maxLen) . '…';
                }
                if (mb_strlen($newPreview) > $maxLen) {
                    $newPreview = mb_substr($newPreview, 0, $maxLen) . '…';
                }
                $changes[] = [
                    'field' => 'Notes',
                    'old'   => $oldPreview !== '' ? $oldPreview : '(empty)',
                    'new'   => $newPreview !== '' ? $newPreview : '(empty)',
                ];
            }
        }
        if (array_key_exists('client_reference', $data)) {
            $new = $data['client_reference'];
            if ($new !== $job->client_reference_no) {
                $update['client_reference_no'] = $new;
                $changes[] = [
                    'field' => 'Client Reference',
                    'old'   => $job->client_reference_no,
                    'new'   => $new,
                ];
            }
        }
        if (array_key_exists('job_reference_no', $data)) {
            $new = $data['job_reference_no'];
            if ($new !== $job->job_reference_no) {
                $update['job_reference_no'] = $new;
                $changes[] = [
                    'field' => 'Job Number',
                    'old'   => $job->job_reference_no,
                    'new'   => $new,
                ];
            }
        }
        if (array_key_exists('compliance', $data)) {
            $new = $data['compliance'];
            if ($new !== $job->ncc_compliance) {
                $update['ncc_compliance'] = $new;
                $changes[] = [
                    'field' => 'Compliance',
                    'old'   => $job->ncc_compliance,
                    'new'   => $new,
                ];
            }
        }
        if (array_key_exists('client_id', $data) && $data['client_id']) {
            $newId = (int) $data['client_id'];
            if ($newId !== (int) $job->client_account_id) {
                $newClient = ClientAccount::find($newId);
                if ($newClient) {
                    $update['client_account_id'] = $newId;
                    $update['client_code'] = $newClient->client_code ?? $job->client_code;

                    $oldName = $oldClient?->client_account_name ?? $job->client_code;
                    $newName = $newClient->client_account_name ?? $newClient->client_code ?? ('ID ' . $newId);

                    $changes[] = [
                        'field' => 'Client',
                        'old'   => $oldName,
                        'new'   => $newName,
                    ];
                }
            }
        }
        if (array_key_exists('staff_id', $data)) {
            $new = $data['staff_id'] ? trim($data['staff_id']) : null;
            $old = $job->staff_id ? trim($job->staff_id) : null;
            if ((string) $new !== (string) $old) {
                $update['staff_id'] = $new;
                $changes[] = [
                    'field' => 'Staff',
                    'old'   => $old ?? '—',
                    'new'   => $new ?? '—',
                ];
            }
        }
        if (array_key_exists('checker_id', $data)) {
            $new = $data['checker_id'] ? trim($data['checker_id']) : null;
            $old = $job->checker_id ? trim($job->checker_id) : null;
            if ((string) $new !== (string) $old) {
                $update['checker_id'] = $new;
                $changes[] = [
                    'field' => 'Checker',
                    'old'   => $old ?? '—',
                    'new'   => $new ?? '—',
                ];
            }
        }
        if (array_key_exists('plan_complexity', $data) && $data['plan_complexity'] !== null) {
            $new = (int) $data['plan_complexity'];
            $old = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
            if ($new !== $old) {
                $update['plan_complexity'] = $new;
                $changes[] = [
                    'field' => 'Complexity',
                    'old'   => $old ?: 0,
                    'new'   => $new,
                ];
            }
        }

        if (empty($update)) {
            return response()->json([
                'status' => 'success',
                'message' => 'No changes to update.',
            ]);
        }

        try {
            DB::table('jobs')->where('job_id', $id)->update($update);

            // Create a single activity log entry summarising all field changes
            $now = now('Asia/Manila');
            $lines = [];
            foreach ($changes as $change) {
                $old = $change['old'] ?? '—';
                $new = $change['new'] ?? '—';
                $lines[] = sprintf('%s: %s → %s', $change['field'], (string) $old, (string) $new);
            }

            $description = implode("\n", $lines);
            if ($description === '') {
                $description = 'Details updated';
            }

            $log = ActivityLog::create([
                'job_id'               => (int) $id,
                'activity_date'        => $now->format('Y-m-d H:i:s'),
                'activity_type'        => 'Job updated',
                'activity_description' => $description,
                'updated_by'           => session('user_name') ?? 'LBS Account',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Job updated successfully.',
                'logs'    => [$log],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function archiveJob(int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $currentStatus = $job->job_status ?? '';
        if (strtolower($currentStatus) === 'archived') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Job is already archived.',
                'redirect' => route('lbs.trash'),
            ]);
        }

        try {
            DB::table('jobs')->where('job_id', $id)->update(['job_status' => 'Archived']);

            $now = now('Asia/Manila');
            ActivityLog::create([
                'job_id'               => (int) $id,
                'activity_date'        => $now->format('Y-m-d H:i:s'),
                'activity_type'        => 'Job archived',
                'activity_description' => 'Job status changed to Archived.',
                'updated_by'           => session('user_name') ?? 'LBS Account',
            ]);

            return response()->json([
                'status'   => 'success',
                'message'  => 'Job archived successfully.',
                'redirect' => route('lbs.trash'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to archive: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadFiles(Request $request, int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $request->validate([
            'section' => ['required', 'string', 'in:plans,documents'],
            'files'   => ['required', 'array'],
            'files.*' => ['file', 'max:51200'],
        ]);

        $section = $request->input('section');
        $column = $section === 'plans' ? 'upload_files' : 'upload_project_files';
        $current = $job->{$column};
        $list = is_string($current) ? (json_decode($current, true) ?? []) : [];
        if (!is_array($list)) {
            $list = [];
        }

        $uploaded = [];
        foreach ($request->file('files', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $folderName = $job->job_reference_no ?? $job->client_reference_no ?? $job->reference ?? ('job_' . $id);
            $path = 'lbs-documents/' . $folderName . '/' . $safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $list[] = $safeName;
            $uploaded[] = $safeName;
        }

        if (empty($uploaded)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No valid files to upload.',
            ], 422);
        }

        DB::table('jobs')->where('job_id', $id)->update([$column => json_encode($list)]);

        $sectionLabel = $section === 'plans' ? 'Plans' : 'Documents';
        $description = $sectionLabel . ': ' . implode(', ', $uploaded);
        ActivityLog::create([
            'job_id'               => (int) $id,
            'activity_date'        => now('Asia/Manila')->format('Y-m-d H:i:s'),
            'activity_type'        => 'Files uploaded',
            'activity_description' => $description,
            'updated_by'           => session('user_name') ?? 'LBS Account',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Files added successfully.',
            'files'   => $list,
        ]);
    }

    public function deleteFile(Request $request, int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $request->validate([
            'section'   => ['required', 'string', 'in:plans,documents'],
            'file_name' => ['required', 'string', 'max:500'],
        ]);

        $section = $request->input('section');
        $column = $section === 'plans' ? 'upload_files' : 'upload_project_files';
        $current = $job->{$column};
        $list = is_string($current) ? (json_decode($current, true) ?? []) : [];
        if (!is_array($list)) {
            $list = [];
        }

        $fileName = $request->input('file_name');
        $list = array_values(array_filter($list, function ($name) use ($fileName) {
            return (string) $name !== (string) $fileName;
        }));

        DB::table('jobs')->where('job_id', $id)->update([$column => json_encode($list)]);

        $folderName = $job->job_reference_no ?? $job->client_reference_no ?? $job->reference ?? ('job_' . $id);
        $storagePath = 'lbs-documents/' . $folderName . '/' . $fileName;
        Storage::disk('local')->delete($storagePath);

        $sectionLabel = $section === 'plans' ? 'Plans' : 'Documents';
        $now = now('Asia/Manila');
        $log = ActivityLog::create([
            'job_id'               => (int) $id,
            'activity_date'        => $now->format('Y-m-d H:i:s'),
            'activity_type'        => 'File deleted',
            'activity_description' => $sectionLabel . ': ' . $fileName,
            'updated_by'           => session('user_name') ?? 'LBS Account',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'File removed.',
            'files'   => $list,
            'log'     => [
                'activity_date'        => $log->activity_date,
                'activity_type'        => $log->activity_type,
                'activity_description' => $log->activity_description,
                'updated_by'           => $log->updated_by,
            ],
        ]);
    }

    public function uploadCheckerFiles(Request $request, int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $request->validate([
            'files'   => ['required', 'array'],
            'files.*' => ['file', 'max:51200'],
            'notes'   => ['nullable', 'string'],
        ]);

        $now = now('Asia/Manila');
        $fileNames = [];
        foreach ($request->file('files', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $folderName = $job->job_reference_no ?? $job->client_reference_no ?? $job->reference ?? ('job_' . $id);
            $path = 'lbs-documents/' . $folderName . '/' . $safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $fileNames[] = $safeName;
        }

        if (empty($fileNames)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No valid files to upload.',
            ], 422);
        }

        $notesHtml = $request->input('notes') ?? '';

        // Some environments may not have AUTO_INCREMENT correctly configured for file_id,
        // so we compute the next ID manually to avoid SQL errors.
        $nextId = (int) DB::table('staff_uploaded_files')->max('file_id') + 1;

        DB::table('staff_uploaded_files')->insert([
            'file_id'     => $nextId,
            'job_id'      => (int) $id,
            'files_json'  => json_encode($fileNames),
            'comment'     => $notesHtml,
            'uploaded_at' => $now->format('Y-m-d H:i:s'),
            'uploaded_by' => session('user_name') ?? 'LUNTIAN',
        ]);

        $descriptionLines = [];
        $descriptionLines[] = 'Checker upload files: ' . implode(', ', $fileNames);
        if (trim(strip_tags($notesHtml)) !== '') {
            $preview = trim(preg_replace('/\s+/', ' ', strip_tags($notesHtml)));
            if (mb_strlen($preview) > 160) {
                $preview = mb_substr($preview, 0, 160) . '…';
            }
            $descriptionLines[] = 'Notes: ' . $preview;
        }

        ActivityLog::create([
            'job_id'               => (int) $id,
            'activity_date'        => $now->format('Y-m-d H:i:s'),
            'activity_type'        => 'Checker upload',
            'activity_description' => implode("\n", $descriptionLines),
            'updated_by'           => session('user_name') ?? 'LBS Account',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Checker upload saved.',
        ]);
    }

    public function index()
    {
        $jobs = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            // Only show new LBS jobs created via this app (reference starting with "JOBS")
            ->where('j.reference', 'like', 'JOBS%')
            // Exclude jobs that should only appear in dedicated views, and Completed
            ->whereNotIn('j.job_status', ['For Review', 'For Email Confirmation', 'Completed', 'Archived'])
            ->select(
                'j.job_id',
                'j.reference',
                'j.log_date',
                'j.client_code',
                'j.job_reference_no',
                'j.client_reference_no',
                'j.staff_id',
                'j.checker_id',
                'j.ncc_compliance',
                'j.job_request_id',
                'j.address_client',
                'j.job_type',
                'j.priority',
                'j.plan_complexity',
                'j.job_status',
                'j.completion_date',
                'ca.client_account_name'
            )
            ->orderByDesc('j.log_date')
            ->limit(200)
            ->get();

        // Map priority/status name -> color (hex) for badges
        $priorityColors = Priority::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        $statusColors = Status::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        $statuses = Status::orderBy('name')->get();

        return view('lbs.list', [
            'sidebar_active' => 'lbs.list',
            'jobs' => $jobs,
            'priorityColors' => $priorityColors,
            'statusColors' => $statusColors,
            'statuses' => $statuses,
        ]);
    }

    public function trash()
    {
        $jobs = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->where('j.reference', 'like', 'JOBS%')
            ->where('j.job_status', '=', 'Archived')
            ->select(
                'j.job_id',
                'j.reference',
                'j.log_date',
                'j.client_code',
                'j.job_reference_no',
                'j.client_reference_no',
                'j.staff_id',
                'j.checker_id',
                'j.ncc_compliance',
                'j.job_request_id',
                'j.address_client',
                'j.job_type',
                'j.priority',
                'j.plan_complexity',
                'j.job_status',
                'j.completion_date',
                'ca.client_account_name'
            )
            ->orderByDesc('j.log_date')
            ->limit(500)
            ->get();

        $priorityColors = Priority::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        $statusColors = Status::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        return view('lbs.trash', [
            'sidebar_active' => 'lbs.trash',
            'jobs' => $jobs,
            'priorityColors' => $priorityColors,
            'statusColors' => $statusColors,
        ]);
    }

    public function restoreJob(int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return redirect()->route('lbs.trash')->with('error', 'Job not found.');
        }
        if (($job->job_status ?? '') !== 'Archived') {
            return redirect()->route('lbs.trash')->with('message', 'Job is not archived.');
        }
        $now = now('Asia/Manila');
        DB::table('jobs')->where('job_id', $id)->update([
            'job_status' => 'Allocated',
            'log_date'   => $now->format('Y-m-d H:i:s'),
        ]);
        ActivityLog::create([
            'job_id'               => (int) $id,
            'activity_date'        => $now->format('Y-m-d H:i:s'),
            'activity_type'        => 'Job restored',
            'activity_description' => 'Job restored from archive to Allocated. Log date updated to restore time.',
            'updated_by'           => session('user_name') ?? 'LBS Account',
        ]);
        return redirect()->route('lbs.trash')->with('success', 'Job restored to list.');
    }

    public function review()
    {
        $jobs = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->where('j.reference', 'like', 'JOBS%')
            ->where('j.job_status', '=', 'For Review')
            ->select(
                'j.job_id',
                'j.reference',
                'j.log_date',
                'j.client_code',
                'j.job_reference_no',
                'j.client_reference_no',
                'j.staff_id',
                'j.checker_id',
                'j.ncc_compliance',
                'j.job_request_id',
                'j.address_client',
                'j.job_type',
                'j.priority',
                'j.plan_complexity',
                'j.job_status',
                'j.completion_date',
                'ca.client_account_name'
            )
            ->orderByDesc('j.log_date')
            ->limit(200)
            ->get();

        $priorityColors = Priority::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        $statusColors = Status::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        return view('lbs.review', [
            'sidebar_active' => 'lbs.review',
            'jobs'           => $jobs,
            'priorityColors' => $priorityColors,
            'statusColors'   => $statusColors,
        ]);
    }

    public function mailbox()
    {
        $jobs = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            ->leftJoin('clients as cl', 'cl.client_code', '=', 'j.client_code')
            ->where('j.reference', 'like', 'JOBS%')
            ->where('j.job_status', '=', 'For Email Confirmation')
            ->select(
                'j.job_id',
                'j.reference',
                'j.log_date',
                'j.client_code',
                'j.job_reference_no',
                'j.upload_files',
                'j.upload_project_files',
                'ca.client_account_name',
                'cl.client_email as to_email'
            )
            ->orderByDesc('j.log_date')
            ->limit(200)
            ->get();

        $priorityColors = Priority::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        $statusColors = Status::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();

        return view('lbs.mailbox', [
            'sidebar_active' => 'lbs.mailbox',
            'jobs'           => $jobs,
            'priorityColors' => $priorityColors,
            'statusColors'   => $statusColors,
        ]);
    }

    /**
     * Get email preview data for a job (for mailbox Preview modal).
     */
    public function emailPreview(int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $assessorEmail = null;
        if (!empty($job->staff_id)) {
            $user = User::where('unique_code', $job->staff_id)->first();
            $assessorEmail = $user ? $user->email : null;
        }

        return response()->json([
            'status'          => 'success',
            'job_reference_no' => $job->job_reference_no ?? $job->reference ?? '',
            'job_status'      => $job->job_status ?? 'For Email Confirmation',
            'assessor'        => $job->staff_id ?? '',
            'assessor_email'  => $assessorEmail,
            'notes'           => $job->notes ?? '',
        ]);
    }

    /**
     * Send mailbox email: same design as preview, attachments = latest checker upload files.
     * Uses SMTP config from database (e.g. SMTP2Go).
     */
    public function sendMailboxEmail(Request $request, int $id)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $toEmail = DB::table('clients')->where('client_code', $job->client_code)->value('client_email');
        if (empty($toEmail)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No recipient email found for this job (client not found or no client_email).',
            ], 422);
        }

        $assessorEmail = null;
        if (!empty($job->staff_id)) {
            $user = User::where('unique_code', $job->staff_id)->first();
            $assessorEmail = $user ? $user->email : '';
        }

        $clientAccountName = null;
        if (!empty($job->client_account_id)) {
            $clientAccount = ClientAccount::find($job->client_account_id);
            $clientAccountName = $clientAccount?->client_account_name;
        }

        $jobReferenceNo = $job->job_reference_no ?? $job->reference ?? '';
        $jobStatus = $job->job_status ?? 'For Email Confirmation';
        $assessor = $job->staff_id ?? '';
        $notes = $job->notes ?? '';

        $reference = $job->reference ?? '';
        $clientReferenceNo = $job->client_reference_no ?? '';
        $subjectParts = [];
        if (!empty($clientAccountName)) {
            $subjectParts[] = $clientAccountName;
        }
        if (!empty($reference)) {
            $subjectParts[] = $reference;
        }
        if (!empty($clientReferenceNo)) {
            $subjectParts[] = $clientReferenceNo;
        }
        $emailSubject = 'Job Update';
        if (!empty($subjectParts)) {
            $emailSubject .= ' : ' . implode(' ', $subjectParts);
        } elseif (!empty($jobReferenceNo)) {
            $emailSubject .= ' : ' . $jobReferenceNo;
        }

        $logoUrl = $this->getLogoDataUriForEmail();

        $viewData = [
            'logoUrl'         => $logoUrl,
            'jobReferenceNo'  => $jobReferenceNo,
            'jobStatus'       => $jobStatus,
            'assessor'        => $assessor,
            'assessorEmail'   => $assessorEmail,
            'notes'           => $notes,
        ];

        $folderName = $job->job_reference_no ?? $job->client_reference_no ?? $job->reference ?? ('job_' . $id);
        $basePath = 'lbs-documents/' . $folderName . '/';

        $attachments = [];
        $latestCheckerUpload = DB::table('staff_uploaded_files')
            ->where('job_id', (int) $id)
            ->orderByDesc('uploaded_at')
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
            Mail::send('emails.lbs-status-update', $viewData, function ($message) use ($toEmail, $emailSubject, $attachments) {
                $message->to($toEmail);
                $message->subject($emailSubject);
                foreach ($attachments as $att) {
                    $message->attach($att['path'], ['as' => $att['name']]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }

        DB::table('jobs')->where('job_id', $id)->update(['job_status' => 'Completed']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Email sent successfully. Status updated to Completed.',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference_no'     => ['nullable', 'string', 'max:255'],
            'client_reference' => ['nullable', 'string', 'max:255'],
            'compliance'       => ['required', 'integer'],
            'client'           => ['required', 'integer'],
            'job_address'      => ['required', 'string', 'max:1000'],
            'priority'         => ['required', 'integer'],
            'job_type'         => ['required', 'integer'],
            'job_status'       => ['required', 'string', 'max:50'],
            'assigned_to'      => ['required', 'string', 'max:10'],
            'checked_by'       => ['required', 'string', 'max:10'],
            'notes'            => ['nullable', 'string'],
        ]);

        $compliance = Compliance::find($data['compliance']);
        $jobRequest = JobRequest::find($data['job_type']);
        $client     = ClientAccount::find($data['client']);

        if (!$compliance || !$jobRequest || !$client) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid compliance, job type, or client.',
            ], 422);
        }

        $headerRef = $request->input('header_reference');
        $now = now('Asia/Manila');

        // Ensure reference column starts with JOBS so the job appears in LBS list; append -1
        $referenceValue = $headerRef ?: ($data['reference_no'] ?? '');
        if ($referenceValue !== '' && stripos($referenceValue, 'JOBS') !== 0) {
            $referenceValue = 'JOBS-' . $referenceValue;
        }
        if ($referenceValue !== '') {
            $referenceValue = $referenceValue . '-1';
        }

        // Client Name in table = unique_code of user who added the job (stored in client_code)
        $currentUser = session('user_id') ? User::find(session('user_id')) : null;
        $clientCodeForJob = $currentUser && !empty($currentUser->unique_code) ? $currentUser->unique_code : ($client->client_code ?? '');

        // Map priority ID -> name string (e.g. "High 1 day") if available
        $priorityText = (string) $data['priority'];
        try {
            $priorityModel = \App\Models\Priority::find($data['priority']);
            if ($priorityModel && $priorityModel->name) {
                $priorityText = $priorityModel->name;
            }
        } catch (\Throwable) {
        }

        try {
            // Handle file uploads (plans & docs) similar to legacy flow, but store securely in storage
            $planNames = [];
            foreach ((array) $request->file('plans', []) as $file) {
                if (!$file) {
                    continue;
                }
                $original = $file->getClientOriginalName() ?: $file->hashName();
                $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
                $folderName = $data['reference_no']
                    ?: ($data['client_reference'] ?: 'AUTO_' . $now->format('YmdHis'));
                $path = 'lbs-documents/' . $folderName . '/' . $safeName;
                Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
                $planNames[] = $safeName;
            }

            $docNames = [];
            foreach ((array) $request->file('docs', []) as $file) {
                if (!$file) {
                    continue;
                }
                $original = $file->getClientOriginalName() ?: $file->hashName();
                $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
                $folderName = $data['reference_no']
                    ?: ($data['client_reference'] ?: 'AUTO_' . $now->format('YmdHis'));
                $path = 'lbs-documents/' . $folderName . '/' . $safeName;
                Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
                $docNames[] = $safeName;
            }

            $jobId = DB::table('jobs')->insertGetId([
                'reference'           => $referenceValue,
                'log_date'            => $now->format('Y-m-d H:i:s'),
                'client_code'         => $clientCodeForJob,
                'job_reference_no'    => $data['reference_no'] ?? '',
                'client_reference_no' => $data['client_reference'] ?? null,
                'staff_id'            => $data['assigned_to'] ?? null,
                'checker_id'          => $data['checked_by'] ?? null,
                'ncc_compliance'      => $compliance->column ?? null,
                'job_request_id'      => $jobRequest->job_request_id ?? (string) $data['job_type'],
                'address_client'      => $data['job_address'] ?? null,
                'job_type'            => $jobRequest->job_request_type ?? null,
                'priority'            => $priorityText,
                'plan_complexity'     => null,
                'notes'               => $data['notes'] ?? null,
                'upload_files'        => json_encode($planNames),
                'upload_project_files'=> json_encode($docNames),
                // last_update has default CURRENT_TIMESTAMP
                'updated_by'          => null,
                'job_status'          => ucfirst($data['job_status']),
                'dwelling'            => '',
                'client_account_id'   => $client->client_account_id,
                'completion_date'     => null,
                'units'               => 0,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'LBS job created successfully.',
                'job_id'  => $jobId,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadFile(int $id, string $file)
    {
        $job = DB::table('jobs')->where('job_id', $id)->first();
        if (!$job) {
            abort(404);
        }

        $folderName = $job->job_reference_no ?? $job->client_reference_no ?? $job->reference ?? ('job_' . $id);
        $fileName = $file;

        $planFiles = [];
        if (is_string($job->upload_files)) {
            $planFiles = json_decode($job->upload_files, true) ?: [];
        }

        $docFiles = [];
        if (is_string($job->upload_project_files)) {
            $docFiles = json_decode($job->upload_project_files, true) ?: [];
        }

        $checkerUploads = DB::table('staff_uploaded_files')
            ->where('job_id', (int) $job->job_id)
            ->get();
        $checkerFiles = [];
        foreach ($checkerUploads as $upload) {
            $files = json_decode($upload->files_json ?? '[]', true) ?: [];
            foreach ($files as $name) {
                $checkerFiles[] = (string) $name;
            }
        }

        $allowed = in_array($fileName, $planFiles, true)
            || in_array($fileName, $docFiles, true)
            || in_array($fileName, $checkerFiles, true);

        if (!$allowed) {
            abort(404);
        }

        $storagePath = 'lbs-documents/' . $folderName . '/' . $fileName;
        if (!Storage::disk('local')->exists($storagePath)) {
            abort(404);
        }

        return Storage::disk('local')->download($storagePath, $fileName);
    }

    /**
     * Show the Add New Job form, optionally pre-filled from a job to duplicate.
     * When duplicating, reference_no and client_reference get suffix -1, -2, etc.
     */
    public function addForm(Request $request)
    {
        $compliances = Compliance::orderBy('column')->get();
        $defaultCompliance = $compliances->first(fn ($c) => $c->column && stripos($c->column, '2022') !== false)
            ?? $compliances->first(fn ($c) => $c->column && stripos($c->column, 'WOH') !== false)
            ?? $compliances->first();

        $clientAccounts = ClientAccount::orderBy('client_account_name')->get();
        $defaultClient = ClientAccount::where('client_account_name', 'like', '%Summit Homes Group%')->first()
            ?? ClientAccount::where('client_account_name', 'like', '%Summit%')
                ->where('client_account_name', 'like', '%Homes%')
                ->first()
            ?? ClientAccount::where('client_account_name', 'like', '%Summit%')->first();
        if ($defaultClient) {
            $clientAccounts = $clientAccounts
                ->reject(fn ($c) => (int) $c->client_account_id === (int) $defaultClient->client_account_id)
                ->prepend($defaultClient)
                ->values();
        }

        $priorities = Priority::orderBy('id')->get();
        $defaultPriority = Priority::where('name', 'like', '%Top (COB)%')->first()
            ?? $priorities->first();

        $jobRequests = JobRequest::orderBy('job_request_type')->get();
        $defaultJobRequest = JobRequest::where('job_request_type', 'like', '%1S DB Base Model- 1S Design Builder Model%')->first()
            ?? $jobRequests->first();

        $assignmentUsers = User::whereIn('role', ['staff', 'checker'])
            ->orderBy('unique_code')
            ->get(['id', 'unique_code'])
            ->unique('unique_code')
            ->values();

        $duplicateJob = null;
        $duplicateId = $request->query('duplicate');
        if ($duplicateId && is_numeric($duplicateId)) {
            $job = DB::table('jobs')->where('job_id', (int) $duplicateId)->first();
            if ($job) {
                $nextSuffix = $this->getNextDuplicateSuffix(
                    (string) ($job->job_reference_no ?? $job->reference ?? '')
                );
                $baseRef = trim((string) ($job->job_reference_no ?? $job->reference ?? ''));
                $baseClientRef = trim((string) ($job->client_reference_no ?? ''));
                $suggestedRef = $baseRef !== '' ? $baseRef . '-' . $nextSuffix : '';
                $suggestedClientRef = $baseClientRef !== '' ? $baseClientRef . '-' . $nextSuffix : '';

                $complianceId = null;
                if (!empty($job->ncc_compliance)) {
                    $comp = Compliance::where('column', $job->ncc_compliance)->first();
                    $complianceId = $comp?->id;
                }
                $priorityId = null;
                if (!empty($job->priority)) {
                    $pri = Priority::where('name', $job->priority)->first();
                    $priorityId = $pri?->id;
                }
                $jobRequestId = null;
                if (!empty($job->job_request_id)) {
                    $jr = JobRequest::where('job_request_id', $job->job_request_id)
                        ->orWhere('job_request_type', $job->job_type)
                        ->first();
                    $jobRequestId = $jr?->id ?? $job->job_request_id;
                }

                $duplicateJob = (object) [
                    'reference_no'      => $suggestedRef,
                    'client_reference'   => $suggestedClientRef,
                    'compliance_id'      => $complianceId,
                    'client_account_id'  => $job->client_account_id ?? null,
                    'job_address'       => $job->address_client ?? '',
                    'priority_id'       => $priorityId,
                    'job_request_id'    => $jobRequestId,
                    'notes'              => $job->notes ?? '',
                    'staff_id'           => $job->staff_id ?? '',
                    'checker_id'        => $job->checker_id ?? '',
                ];
            }
        }

        return view('lbs.add', [
            'sidebar_active'       => 'lbs.add',
            'compliances'          => $compliances,
            'defaultComplianceId'  => $defaultCompliance?->id,
            'clientAccounts'        => $clientAccounts,
            'defaultClientAccountId' => $defaultClient?->client_account_id,
            'priorities'           => $priorities,
            'defaultPriorityId'     => $defaultPriority?->id,
            'jobRequests'          => $jobRequests,
            'defaultJobRequestId'   => $defaultJobRequest?->id,
            'assignmentUsers'      => $assignmentUsers,
            'duplicateJob'         => $duplicateJob,
        ]);
    }

    /**
     * Get next duplicate suffix (1, 2, 3...) for a base reference.
     * Counts existing job_reference_no that are baseRef or baseRef-N.
     */
    private function getNextDuplicateSuffix(string $baseRef): int
    {
        if ($baseRef === '') {
            return 1;
        }
        $pattern = $baseRef . '-%';
        $refs = DB::table('jobs')
            ->where('job_reference_no', 'like', $pattern)
            ->pluck('job_reference_no');
        $max = 0;
        foreach ($refs as $ref) {
            $suffix = substr((string) $ref, strlen($baseRef) + 1);
            if (preg_match('/^\d+$/', $suffix)) {
                $n = (int) $suffix;
                if ($n > $max) {
                    $max = $n;
                }
            }
        }
        return $max + 1;
    }

    /**
     * Logo for email: embed as base64 in HTML so it displays in body and never as attachment.
     * Prefers logo-email.png (small, under 40KB) if present; else resizes logo-light.png via GD or embeds raw if small.
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

        $maxEmbedBytes = 35000; // ~47KB base64; keep email from being clipped
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

