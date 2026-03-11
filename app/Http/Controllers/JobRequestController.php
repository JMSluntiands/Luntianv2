<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\JobRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobRequestController extends Controller
{
    public function index()
    {
        $jobRequests = JobRequest::with('client')
            ->orderByDesc('id')
            ->paginate(15);

        return view('job_request.index', [
            'sidebar_active' => 'job_request.index',
            'jobRequests' => $jobRequests,
        ]);
    }

    public function create()
    {
        $clients = Client::orderBy('client_code')->get();

        return view('job_request.create', [
            'sidebar_active' => 'job_request.create',
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_code' => ['required', 'string', 'max:10', 'exists:clients,client_code'],
            'job_request_id' => ['required', 'string', 'max:50', 'unique:job_requests,job_request_id'],
            'job_request_type' => ['required', 'string', 'max:255'],
        ], [
            'client_code.exists' => 'The selected client code does not exist.',
            'job_request_id.unique' => 'This job request ID is already in use.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('job_request.create')
                ->withErrors($validator)
                ->withInput();
        }

        JobRequest::create($validator->validated());

        return redirect()
            ->route('job_request.index')
            ->with('success', 'Job request created successfully.');
    }

    public function edit(JobRequest $job_request)
    {
        $clients = Client::orderBy('client_code')->get();

        return view('job_request.edit', [
            'sidebar_active' => 'job_request.edit',
            'jobRequest' => $job_request,
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, JobRequest $job_request)
    {
        $validator = Validator::make($request->all(), [
            'client_code' => ['required', 'string', 'max:10', 'exists:clients,client_code'],
            'job_request_id' => ['required', 'string', 'max:50', 'unique:job_requests,job_request_id,' . $job_request->id],
            'job_request_type' => ['required', 'string', 'max:255'],
        ], [
            'client_code.exists' => 'The selected client code does not exist.',
            'job_request_id.unique' => 'This job request ID is already in use.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('job_request.edit', $job_request)
                ->withErrors($validator)
                ->withInput();
        }

        $job_request->update($validator->validated());

        return redirect()
            ->route('job_request.index')
            ->with('success', 'Job request updated successfully.');
    }

    public function destroy(JobRequest $job_request)
    {
        $job_request->delete();

        return redirect()
            ->route('job_request.index')
            ->with('success', 'Job request deleted successfully.');
    }
}
