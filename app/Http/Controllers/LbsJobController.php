<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use App\Models\Compliance;
use App\Models\JobRequest;
use App\Models\Priority;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'ca.client_account_name'
            )
            ->where('j.job_id', $id)
            ->first();

        if (!$job) {
            abort(404);
        }

        // Resolve colors for this job's priority & status from lookup tables
        $priorityColor = null;
        $statusColor = null;

        if (!empty($job->priority)) {
            $priorityColor = Priority::where('name', $job->priority)->value('color');
        }

        if (!empty($job->job_status)) {
            $statusColor = Status::where('name', $job->job_status)->value('color');
        }

        return view('lbs.view', [
            'sidebar_active' => 'lbs.list',
            'jobId'          => $job->job_id,
            'job'            => $job,
            'priorityColor'  => $priorityColor,
            'statusColor'    => $statusColor,
        ]);
    }

    public function index()
    {
        $jobs = DB::table('jobs as j')
            ->leftJoin('client_accounts as ca', 'ca.client_account_id', '=', 'j.client_account_id')
            // Only show new LBS jobs created via this app (reference starting with "JOBS")
            ->where('j.reference', 'like', 'JOBS%')
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

        return view('lbs.list', [
            'sidebar_active' => 'lbs.list',
            'jobs' => $jobs,
            'priorityColors' => $priorityColors,
            'statusColors' => $statusColors,
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
            // Handle file uploads (plans & docs) similar to legacy flow
            $folderName = $data['reference_no']
                ?: ($data['client_reference'] ?: 'AUTO_' . $now->format('YmdHis'));
            $uploadDir = public_path('document/' . $folderName);
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }

            $planNames = [];
            foreach ((array) $request->file('plans', []) as $file) {
                if (!$file) {
                    continue;
                }
                $original = $file->getClientOriginalName() ?: $file->hashName();
                $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
                $file->move($uploadDir, $safeName);
                $planNames[] = $safeName;
            }

            $docNames = [];
            foreach ((array) $request->file('docs', []) as $file) {
                if (!$file) {
                    continue;
                }
                $original = $file->getClientOriginalName() ?: $file->hashName();
                $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
                $file->move($uploadDir, $safeName);
                $docNames[] = $safeName;
            }

            $jobId = DB::table('jobs')->insertGetId([
                'reference'           => $headerRef ?: ($data['reference_no'] ?? ''),
                'log_date'            => $now->format('Y-m-d H:i:s'),
                'client_code'         => $client->client_code ?? '',
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
}

