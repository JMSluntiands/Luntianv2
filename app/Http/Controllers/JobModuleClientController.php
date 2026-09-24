<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\JobModuleClient;
use App\Models\JobRequest;
use App\Support\AddJobModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobModuleClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('client_code')->get(['client_code', 'client_name']);
        $assignments = JobModuleClient::codesByModule();
        $counts = JobRequest::query()
            ->selectRaw('UPPER(TRIM(client_code)) as code, COUNT(*) as total')
            ->groupBy(DB::raw('UPPER(TRIM(client_code))'))
            ->pluck('total', 'code');

        $rows = [];
        foreach (AddJobModules::options() as $module => $label) {
            $code = $assignments[$module] ?? '';
            $rows[] = [
                'module' => $module,
                'label' => $label,
                'client_code' => $code,
                'job_request_count' => (int) ($counts[strtoupper($code)] ?? 0),
            ];
        }

        return view('job_module_client.index', [
            'sidebar_active' => 'job_module_client.index',
            'clients' => $clients,
            'rows' => $rows,
        ]);
    }

    public function update(Request $request)
    {
        $modules = AddJobModules::keys();
        $validator = Validator::make($request->all(), [
            'assignments' => ['required', 'array'],
            'assignments.*' => ['nullable', 'string', 'max:10', 'exists:clients,client_code'],
        ], [
            'assignments.*.exists' => 'Choose a client that already exists in Client Accounts.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('job_module_client.index')
                ->withErrors($validator)
                ->withInput();
        }

        $assignments = $validator->validated()['assignments'];
        $retag = $request->boolean('retag_job_requests', true);
        $moved = 0;

        DB::transaction(function () use ($modules, $assignments, $retag, &$moved) {
            foreach ($modules as $module) {
                $newCode = trim((string) ($assignments[$module] ?? ''));
                if ($newCode === '') {
                    continue;
                }

                $previousCode = JobModuleClient::codeFor($module);
                JobModuleClient::query()->updateOrCreate(
                    ['module' => $module],
                    ['client_code' => $newCode]
                );

                if (! $retag || strcasecmp($previousCode, $newCode) === 0) {
                    continue;
                }

                $fromCodes = array_values(array_unique(array_filter(
                    array_merge([$previousCode], JobModuleClient::aliasCodes($module)),
                    fn ($code) => strcasecmp((string) $code, $newCode) !== 0
                )));

                if ($fromCodes === []) {
                    continue;
                }

                $moved += JobRequest::query()
                    ->where(function ($query) use ($fromCodes) {
                        foreach (array_values($fromCodes) as $index => $code) {
                            $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                            $query->{$method}('UPPER(TRIM(client_code)) = ?', [strtoupper((string) $code)]);
                        }
                    })
                    ->update(['client_code' => $newCode]);
            }

            JobModuleClient::flushCodeCache();
        });

        $message = 'Job client assignments saved.';
        if ($retag) {
            $message .= $moved > 0
                ? " Updated {$moved} job request".($moved === 1 ? '' : 's').' to the selected client codes.'
                : ' Existing job requests already matched the selected codes.';
        }

        return redirect()
            ->route('job_module_client.index')
            ->with('success', $message);
    }
}
