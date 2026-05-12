<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailBph;
use App\Models\Compliance;
use App\Models\JobRequest;
use App\Models\User;
use App\Services\JobCountsScope;
use App\Services\SlackAssignmentNotifier;
use App\Support\FecUnitsValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class LeadingEnergyJobController extends Controller
{
    private const CLIENT_CODE = 'LE01';

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

        return view('leading_energy.add', [
            'sidebar_active' => 'leading_energy.add',
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

        if (!Schema::hasTable('job_leading_energy')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database table job_leading_energy is not available. Run migrations.',
            ], 500);
        }

        $compliance = !empty($data['ncc_compliance']) ? Compliance::find($data['ncc_compliance']) : null;
        $jobRequest = !empty($data['job_type_request']) ? JobRequest::find($data['job_type_request']) : null;
        $nccText = $compliance->column ?? '2019';
        $jobTypeText = $jobRequest->job_request_type ?? '—';

        $headerRef = trim((string) $request->input('header_reference', ''));
        $reference = $headerRef !== '' ? $headerRef : ('LE-' . now('Asia/Manila')->format('YmdHis'));
        $reference = substr($reference, 0, 50);

        $now = now('Asia/Manila');
        $urgent = $request->boolean('urgent_job') ? 'YES' : 'NO';
        $jobNum = strtoupper(substr($data['job_number'], 0, 6));
        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $reference) ?: 'leading_energy_upload';

        $planNames = [];
        foreach ((array) $request->file('upload_plans', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            Storage::disk('local')->putFileAs('leading-energy-documents/' . $folderSeg, $file, $safeName);
            $planNames[] = $safeName;
        }

        $docNames = [];
        foreach ((array) $request->file('upload_document', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            Storage::disk('local')->putFileAs('leading-energy-documents/' . $folderSeg, $file, $safeName);
            $docNames[] = $safeName;
        }

        try {
            $nextId = (int) DB::table('job_leading_energy')->max('id') + 1;
            DB::table('job_leading_energy')->insert([
                'id' => $nextId,
                'reference' => $reference,
                'client_code' => self::CLIENT_CODE,
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
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Leading Energy job created successfully.',
                'job_id' => $nextId,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendSlackNotification(int $id)
    {
        $job = DB::table('job_leading_energy')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        return response()->json(['status' => 'success']);
    }

    public function sendSubmissionEmail(int $id)
    {
        $job = DB::table('job_leading_energy')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }
        return response()->json(['status' => 'success', 'message' => 'Email sent.']);
    }

    public function list()
    {
        return view('leading_energy.list', ['sidebar_active' => 'leading_energy.list']);
    }

    public function show(int $id)
    {
        $scoped = DB::table('job_leading_energy')->where('id', $id);
        JobCountsScope::applyJobBphAssignment($scoped);
        JobCountsScope::applyBranchExclusiveStatLabel($scoped, 'LEADING ENERGY');
        $job = $scoped->first();
        if (! $job) {
            abort(404);
        }

        return view('leading_energy.show', [
            'sidebar_active' => 'leading_energy.list',
            'job' => $job,
        ]);
    }

    public function completed()
    {
        return view('leading_energy.completed', ['sidebar_active' => 'leading_energy.completed']);
    }

    public function review()
    {
        return view('leading_energy.review', ['sidebar_active' => 'leading_energy.review']);
    }

    public function mailbox()
    {
        $jobs = collect();
        if (Schema::hasTable('job_leading_energy')) {
            $q = DB::table('job_leading_energy')
                ->whereRaw('LOWER(TRIM(status)) = ?', [strtolower('For Email Confirmation')]);
            JobCountsScope::applyJobBphAssignment($q);
            JobCountsScope::applyBranchExclusiveStatLabel($q, 'LEADING ENERGY');
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
                ];
            });
        }

        return view('leading_energy.mailbox', [
            'sidebar_active' => 'leading_energy.mailbox',
            'jobs' => $jobs,
        ]);
    }

    public function emailPreview(int $id)
    {
        $job = DB::table('job_leading_energy')->where('id', $id)->first();
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
        $job = DB::table('job_leading_energy')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        DB::table('job_leading_energy')->where('id', $id)->update([
            'status' => 'Completed',
            'date' => now('Asia/Manila')->toDateString(),
            'updated_at' => now('Asia/Manila'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email sent successfully. Status updated to Completed.',
        ]);
    }

    public function trash()
    {
        return view('leading_energy.trash', ['sidebar_active' => 'leading_energy.trash']);
    }

    public function update(Request $request, int $id)
    {
        $job = DB::table('job_leading_energy')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $data = $request->validate([
            'job_status' => ['nullable', 'string', 'max:50'],
            'staff_id' => ['nullable', 'string', 'max:50'],
            'checker_id' => ['nullable', 'string', 'max:50'],
            'units' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if (array_key_exists('job_status', $data) && trim((string) ($data['job_status'] ?? '')) !== '') {
            $candidate = trim((string) $data['job_status']);
            if ($fecErr = FecUnitsValidation::jsonErrorIfFecWithoutUnits($request, $job, $candidate)) {
                return $fecErr;
            }
        }

        $update = [];
        if (array_key_exists('job_status', $data) && trim((string) $data['job_status']) !== '') {
            $update['status'] = $data['job_status'];
        }
        if (array_key_exists('staff_id', $data) && trim((string) $data['staff_id']) !== '') {
            $update['assigned'] = strtoupper((string) $data['staff_id']);
        }
        if (array_key_exists('checker_id', $data) && trim((string) $data['checker_id']) !== '') {
            $update['checked'] = strtoupper((string) $data['checker_id']);
        }
        if (array_key_exists('units', $data) && $data['units'] !== null && Schema::hasColumn('job_leading_energy', 'units')) {
            $update['units'] = (int) $data['units'];
        }
        if ($update === []) {
            return response()->json(['status' => 'success', 'message' => 'No changes to update.']);
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
        DB::table('job_leading_energy')->where('id', $id)->update($update);

        if ($assignmentChanged) {
            $jobStatus = (string) (array_key_exists('status', $update) ? $update['status'] : ($job->status ?? ''));
            $jobUrl = route('leading_energy.view', ['id' => $id]);
            SlackAssignmentNotifier::notifyAssignment(
                'Leading Energy',
                $id,
                (string) ($job->reference ?? ''),
                $nextStaff !== '' ? $nextStaff : null,
                $nextChecker !== '' ? $nextChecker : null,
                $jobStatus,
                $jobUrl
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Leading Energy job updated successfully.',
        ]);
    }
}

