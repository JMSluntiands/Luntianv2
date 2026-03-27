<?php

namespace App\Http\Controllers;

use App\Models\ClientEmailBph;
use App\Models\Compliance;
use App\Models\EmailConfig;
use App\Models\JobRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BphJobController extends Controller
{
    private const BPH_CLIENT_CODE = 'BPH01';

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

            DB::table('job_bph')->insert([
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
            ]);
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

    public function show(int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            abort(404);
        }

        return view('bph.view', [
            'sidebar_active' => 'bph.list',
            'job' => $job,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $job = DB::table('job_bph')->where('id', $id)->first();
        if (!$job) {
            return redirect()->route('bph.list');
        }

        $data = $request->validate([
            'urgent'        => ['required', 'in:YES,NO'],
            'job_type'      => ['required', 'string', 'max:100'],
            'ncc'           => ['required', 'string', 'max:255'],
            'job_number'    => ['required', 'string', 'max:6', 'regex:/^\d{5}B$/i'],
            'client_name'   => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'notes'         => ['nullable', 'string'],
            'assigned'      => ['required', 'string', 'max:50'],
            'checked'       => ['required', 'string', 'max:50'],
            'status'        => ['required', 'string', 'max:50'],
        ]);

        DB::table('job_bph')->where('id', $id)->update([
            'urgent'        => $data['urgent'],
            'job_type'      => $data['job_type'],
            'ncc'           => $data['ncc'],
            'job_number'    => strtoupper($data['job_number']),
            'client_name'   => $data['client_name'],
            'contact_email' => $data['contact_email'],
            'notes'         => $data['notes'] ?? null,
            'assigned'      => $data['assigned'],
            'checked'       => $data['checked'],
            'status'        => $data['status'],
            'updated_at'    => now('Asia/Manila'),
        ]);

        return redirect()
            ->route('bph.view', $id)
            ->with('success', 'BPH job updated successfully.');
    }
}
