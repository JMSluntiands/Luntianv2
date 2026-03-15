<?php

namespace App\Http\Controllers;

use App\Models\Compliance;
use App\Models\JobRequest;
use App\Models\User;
use Illuminate\Http\Request;

class BphJobController extends Controller
{
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

        $defaultCompliance = $compliances->first(fn ($c) => $c->column && stripos((string) $c->column, '2019') !== false)
            ?? $compliances->first();
        $defaultJobRequest = $jobRequests->first(fn ($jr) => $jr->job_request_type && stripos((string) $jr->job_request_type, 'query') !== false)
            ?? $jobRequests->first();

        return view('bph.add', [
            'sidebar_active' => 'bph.add',
            'compliances' => $compliances,
            'jobRequests' => $jobRequests,
            'assignmentUsers' => $assignmentUsers,
            'defaultComplianceId' => $defaultCompliance?->id,
            'defaultJobRequestId' => $defaultJobRequest?->id,
        ]);
    }
}
