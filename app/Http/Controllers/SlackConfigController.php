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
            'webhook_url' => ['nullable', 'string', 'max:500', Rule::when($request->filled('webhook_url'), 'url')],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('settings.slack_config')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['name'] = 'LBS Notifications';

        $config = SlackConfig::first();

        if ($config) {
            $config->update($data);
            $message = 'Slack configuration updated successfully.';
        } else {
            SlackConfig::create($data);
            $message = 'Slack configuration saved successfully.';
        }

        return redirect()
            ->route('settings.slack_config')
            ->with('success', $message);
    }

    /**
     * Toggle slack notifications on/off from sidebar quick controls.
     */
    public function toggleActive(Request $request)
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $config = SlackConfig::first();
        if (! $config) {
            return redirect()
                ->route('settings.slack_config')
                ->withErrors(['slack_config' => 'Please save Slack Configuration first before turning it on or off.']);
        }

        $config->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->back()
            ->with('success', $request->boolean('is_active')
                ? 'Slack notifications turned on.'
                : 'Slack notifications turned off.');
    }
}
