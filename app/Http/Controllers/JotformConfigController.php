<?php

namespace App\Http\Controllers;

use App\Models\JotformConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JotformConfigController extends Controller
{
    public function index()
    {
        $config = JotformConfig::current();
        if ($config) {
            $config = JotformConfig::ensureSecret($config);
        }

        return view('settings.jotform-config', [
            'sidebar_active' => 'settings.jotform_config',
            'config' => $config,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'is_active' => ['nullable', 'boolean'],
            'jotform_form_id' => ['nullable', 'string', 'max:50'],
            'queue_in_forms_submitted' => ['nullable', 'boolean'],
            'map_reference_no' => ['nullable', 'string', 'max:120'],
            'map_client_reference' => ['nullable', 'string', 'max:120'],
            'map_compliance' => ['nullable', 'string', 'max:120'],
            'map_client' => ['nullable', 'string', 'max:120'],
            'map_job_address' => ['nullable', 'string', 'max:120'],
            'map_job_type' => ['nullable', 'string', 'max:120'],
            'map_priority' => ['nullable', 'string', 'max:120'],
            'map_assigned_to' => ['nullable', 'string', 'max:120'],
            'map_checked_by' => ['nullable', 'string', 'max:120'],
            'map_job_status' => ['nullable', 'string', 'max:120'],
            'map_notes' => ['nullable', 'string', 'max:120'],
            'map_upload_plans' => ['nullable', 'string', 'max:120'],
            'map_upload_documents' => ['nullable', 'string', 'max:120'],
            'regenerate_secret' => ['nullable', 'boolean'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                ->route('settings.jotform_config')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['queue_in_forms_submitted'] = $request->boolean('queue_in_forms_submitted', true);

        $config = JotformConfig::current();
        if (! $config) {
            $config = new JotformConfig;
            $config->webhook_secret = Str::random(40);
        }

        if ($request->boolean('regenerate_secret')) {
            $data['webhook_secret'] = Str::random(40);
        }

        unset($data['regenerate_secret']);
        $config->fill($data);
        $config->save();

        return redirect()
            ->route('settings.jotform_config')
            ->with('success', 'Jot Form configuration saved successfully.');
    }

    public function toggleActive(Request $request)
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $config = JotformConfig::current();
        if (! $config) {
            return redirect()
                ->route('settings.jotform_config')
                ->withErrors(['jotform_config' => 'Please save Jot Form Configuration first before turning it on or off.']);
        }

        JotformConfig::ensureSecret($config);
        $config->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->back()
            ->with('success', $request->boolean('is_active')
                ? 'JotForm integration turned on.'
                : 'JotForm integration turned off.');
    }
}
