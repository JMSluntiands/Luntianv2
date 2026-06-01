<?php

namespace App\Http\Controllers;

use App\Models\SlackConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SlackConfigController extends Controller
{
    /**
     * Show the Slack configuration form (add or edit).
     */
    public function index()
    {
        $config = SlackConfig::first();

        return view('settings.slack-config', [
            'sidebar_active' => 'settings.slack_config',
            'config' => $config,
        ]);
    }

    /**
     * Store a new Slack config or update existing.
     */
    public function store(Request $request)
    {
        $rules = [
            'webhook_new_job_url' => ['nullable', 'string', 'max:500', Rule::when($request->filled('webhook_new_job_url'), 'url')],
            'webhook_assignment_url' => ['nullable', 'string', 'max:500', Rule::when($request->filled('webhook_assignment_url'), 'url')],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('settings.slack_config')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        foreach (['webhook_new_job_url', 'webhook_assignment_url'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === '') {
                $data[$key] = null;
            }
        }
        $data['name'] = 'LBS Notifications';

        $config = SlackConfig::first();

        if ($config) {
            $config->update($data);
            $message = 'Slack configuration updated successfully.';
        } else {
            $data['is_active'] = true;
            $data['new_job_slack_active'] = true;
            $data['assignment_slack_active'] = true;
            SlackConfig::create($data);
            $message = 'Slack configuration saved successfully.';
        }

        return redirect()
            ->route('settings.slack_config')
            ->with('success', $message);
    }

    /**
     * Toggle new-job or assignment Slack on/off (separate switches per webhook).
     */
    public function togglePurpose(Request $request)
    {
        $validated = $request->validate([
            'purpose' => ['required', 'string', Rule::in(['new_job', 'assignment'])],
        ]);

        $config = SlackConfig::first();
        if (! $config) {
            return redirect()
                ->route('settings.slack_config')
                ->withErrors(['slack_config' => 'Save Slack configuration first before changing notification switches.']);
        }

        $purpose = $validated['purpose'];
        $field = $purpose === 'new_job' ? 'new_job_slack_active' : 'assignment_slack_active';
        $next = ! (bool) $config->getAttribute($field);

        $config->update([$field => $next]);
        $config->refresh();
        $config->update([
            'is_active' => $config->new_job_slack_active || $config->assignment_slack_active,
        ]);

        $label = $purpose === 'new_job' ? 'New job Slack' : 'Assignment Slack';
        $state = $next ? 'on' : 'off';

        return redirect()
            ->back()
            ->with('success', "{$label} notifications turned {$state}.");
    }
}
