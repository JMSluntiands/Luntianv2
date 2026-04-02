<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EmailConfig;
use App\Models\User;
use App\Services\JobCountsScope;

class CspJobController extends Controller
{
    private const CSP_CLIENT_CODE = 'CSP01';

    public function store(Request $request)
    {
        $data = $request->validate([
            'ncc_compliance'   => ['nullable', 'string', 'max:255'],
            'job_type_request' => ['nullable', 'string', 'max:100'],
            'job_number'       => ['required', 'string', 'max:6', 'regex:/^\d{5}B$/i'],
            'client_name'      => ['required', 'string', 'max:255'],
            'contact_email'    => ['required', 'email', 'max:255'],
            'notes'            => ['nullable', 'string'],
            'assigned_to'      => ['required', 'string', 'max:50'],
            'checked_by'       => ['required', 'string', 'max:50'],
            'urgent_job'       => ['nullable'],
        ]);

        if (!Schema::hasTable('job_csp')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database table job_csp is not available. Run migrations.',
            ], 500);
        }

        $nccText = $data['ncc_compliance'] !== null && $data['ncc_compliance'] !== ''
            ? (string) $data['ncc_compliance']
            : '2019';
        $jobTypeText = $data['job_type_request'] !== null && $data['job_type_request'] !== ''
            ? (string) $data['job_type_request']
            : '—';

        $headerRef = trim((string) $request->input('header_reference', ''));
        $reference = $headerRef !== '' ? $headerRef : ('CSP-' . now('Asia/Manila')->format('YmdHis'));
        $reference = substr($reference, 0, 50);

        $now = now('Asia/Manila');
        $urgent = $request->boolean('urgent_job') ? 'YES' : 'NO';
        $jobNum = strtoupper(substr($data['job_number'], 0, 6));

        $folderSeg = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $reference) ?: 'csp_upload';

        $planNames = [];
        foreach ((array) $request->file('upload_plans', []) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $original = $file->getClientOriginalName() ?: $file->hashName();
            $safeName = preg_replace('/[^A-Za-z0-9\-\_\.\(\) ]/', '_', $original);
            $path = 'csp-documents/' . $folderSeg . '/' . $safeName;
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
            $path = 'csp-documents/' . $folderSeg . '/' . $safeName;
            Storage::disk('local')->putFileAs(dirname($path), $file, $safeName);
            $docNames[] = $safeName;
        }

        try {
            $nextId = (int) DB::table('job_csp')->max('id') + 1;

            $row = [
                'id'                  => $nextId,
                'reference'           => $reference,
                'client_code'         => self::CSP_CLIENT_CODE,
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

            DB::table('job_csp')->insert($row);
            $id = $nextId;
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'CSP job created successfully.',
            'job_id'  => $id,
        ]);
    }

    /**
     * CSP job detail page (same list/Gate scoping as CSP lists).
     */
    public function show(int $id)
    {
        $job = DB::table('job_csp')
            ->where('id', $id)
            ->where('client_code', self::CSP_CLIENT_CODE)
            ->first();
        if (! $job) {
            abort(404);
        }

        $scoped = DB::table('job_csp')->where('id', $id);
        JobCountsScope::applyJobBphAssignment($scoped);
        JobCountsScope::applyBranchExclusiveStatLabel($scoped, 'CSP');
        if (! $scoped->exists()) {
            abort(403);
        }

        return view('csp.show', [
            'sidebar_active' => 'csp.view',
            'job' => $job,
        ]);
    }

    /**
     * Lightweight mailbox list for CSP jobs with status "For Email Confirmation".
     */
    public function mailbox()
    {
        $jobs = collect();
        if (Schema::hasTable('job_csp')) {
            $q = DB::table('job_csp')
                ->whereRaw('LOWER(TRIM(status)) = ?', [strtolower('For Email Confirmation')]);
            JobCountsScope::applyJobBphAssignment($q);
            JobCountsScope::applyBranchExclusiveStatLabel($q, 'CSP');
            $rows = $q
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->limit(300)
                ->get();

            $jobs = $rows->map(function ($row) {
                return (object) [
                    'job_id'            => (int) $row->id,
                    'log_date'          => $row->updated_at ?? $row->created_at,
                    'job_reference_no'  => $row->reference,
                    'reference'         => $row->reference,
                    'to_email'          => $row->contact_email,
                    'upload_files'      => $row->plans_files,
                    'upload_project_files' => $row->docs_files,
                ];
            });
        }

        return view('csp.mailbox', [
            'sidebar_active' => 'csp.mailbox',
            'jobs' => $jobs,
        ]);
    }

    /**
     * Mailbox email preview JSON for CSP (same shape as BPH).
     */
    public function emailPreview(int $id)
    {
        $job = DB::table('job_csp')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $assessorEmail = null;
        if (!empty($job->checked)) {
            $user = User::where('unique_code', $job->checked)->first();
            $assessorEmail = $user ? $user->email : null;
        }

        return response()->json([
            'status'           => 'success',
            'job_reference_no' => $job->reference ?? '',
            'job_status'       => $job->status ?? 'For Email Confirmation',
            'assessor'         => $job->checked ?? '',
            'assessor_email'   => $assessorEmail,
            'notes'            => $job->notes ?? '',
        ]);
    }

    /**
     * Send CSP mailbox confirmation email; attach CSP compliance PDF and job files.
     */
    public function sendMailboxEmail(Request $request, int $id)
    {
        $job = DB::table('job_csp')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $emailConfig = EmailConfig::where('is_active', true)->first();
        if (!$emailConfig) {
            return response()->json([
                'status'  => 'disabled',
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
            'logoUrl'        => $logoUrl,
            'jobReferenceNo' => $jobReferenceNo,
            'jobStatus'      => $jobStatus,
            'assessor'       => $assessor,
            'assessorEmail'  => $assessorEmail,
            'notes'          => $notes,
        ];

        $folderName = preg_replace('/[^A-Za-z0-9\-\_]/', '_', (string) ($job->reference ?? 'job_' . $id));
        $basePath = 'csp-documents/' . $folderName . '/';

        $attachments = [];
        $planNames = json_decode($job->plans_files ?? '[]', true) ?: [];
        $docNames = json_decode($job->docs_files ?? '[]', true) ?: [];
        if (!is_array($planNames)) {
            $planNames = [];
        }
        if (!is_array($docNames)) {
            $docNames = [];
        }
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
            $compliancePdf = $this->cspComplianceSummaryPdfInstance($job);
            $compliancePdfName = $this->cspComplianceSummaryPdfFilename($job);
            $compliancePdfBinary = $compliancePdf->output();
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
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
                'status'  => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }

        DB::table('job_csp')->where('id', $id)->update([
            'status'     => 'Completed',
            'updated_at' => now('Asia/Manila'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Email sent successfully. Status updated to Completed.',
        ]);
    }

    /**
     * CSP compliance summary as PDF (inline in browser / new tab).
     */
    public function printComplianceSummary(int $id)
    {
        $job = DB::table('job_csp')->where('id', $id)->first();
        if (!$job) {
            abort(404);
        }

        $pdf = $this->cspComplianceSummaryPdfInstance($job);

        return $pdf->stream($this->cspComplianceSummaryPdfFilename($job));
    }

    public function update(Request $request, int $id)
    {
        $job = DB::table('job_csp')->where('id', $id)->first();
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.'], 404);
        }

        $data = $request->validate([
            'job_status'    => ['nullable', 'string', 'max:50'],
            'staff_id'      => ['nullable', 'string', 'max:50'],
            'checker_id'    => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

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

        if ($update === []) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Nothing to update.',
            ]);
        }

        $update['updated_at'] = now('Asia/Manila');
        DB::table('job_csp')->where('id', $id)->update($update);

        return response()->json([
            'status'  => 'success',
            'message' => 'CSP job updated successfully.',
        ]);
    }

    /**
     * DomPDF instance for CSP compliance summary (reuse BPH template).
     */
    private function cspComplianceSummaryPdfInstance(object $job)
    {
        $pdf = Pdf::loadView('bph.pdf.compliance-summary', [
            'job' => $job,
            'mergeImageDataUri' => null,
            'mergePdfName' => null,
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    private function cspComplianceSummaryPdfFilename(object $job): string
    {
        $id = (int) ($job->id ?? 0);
        $safeNum = preg_replace('/[^A-Za-z0-9\-_]/', '-', (string) ($job->job_number ?? '')) ?: 'job-' . $id;

        return 'CSP-Compliance-' . $safeNum . '.pdf';
    }

    /**
     * Logo for email HTML (copied from BPH controller for consistency).
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

