<?php

namespace App\Http\Controllers;

use App\Models\SlackConfig;
use App\Services\SlackNewJobNotifier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SlackConfigController extends Controller
{
    /**
     * Show the Slack configuration form (add or edit).
     */
    public function index()
    {
        $this->ensureBranchColumns();
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
            'new_job_branches' => ['nullable', 'array'],
            'new_job_branches.*' => ['string', Rule::in(SlackNewJobNotifier::BRANCHES)],
            'new_job_branch_colors' => ['nullable', 'array'],
            'new_job_branch_colors.*' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
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
        $this->ensureBranchColumns();
        unset($data['new_job_branches']);
        if ($request->boolean('new_job_branches_submitted') && Schema::hasColumn('slack_configs', 'new_job_branches')) {
            $checked = (array) $request->input('new_job_branches', []);
            $data['new_job_branches'] = array_values(array_filter(
                SlackNewJobNotifier::BRANCHES,
                fn (string $branch) => in_array($branch, $checked, true)
            ));
        }
        unset($data['new_job_branch_colors']);
        if ($request->boolean('new_job_branches_submitted') && Schema::hasColumn('slack_configs', 'new_job_branch_colors')) {
            $submitted = (array) $request->input('new_job_branch_colors', []);
            $colors = [];
            foreach (SlackNewJobNotifier::BRANCHES as $branch) {
                $color = $submitted[$branch] ?? null;
                if (is_string($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    $colors[$branch] = strtoupper($color);
                }
            }
            $data['new_job_branch_colors'] = $colors;
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

        if (! Schema::hasColumn('slack_configs', $field)
            || ! Schema::hasColumn('slack_configs', 'assignment_slack_active')
            || ! Schema::hasColumn('slack_configs', 'new_job_slack_active')) {
            return redirect()
                ->route('settings.slack_config')
                ->withErrors([
                    'slack_config' => 'Database is missing Slack switch columns. Run migrations (or ALTER TABLE slack_configs to add new_job_slack_active and assignment_slack_active).',
                ]);
        }

        $next = ! (bool) $config->getAttribute($field);

        try {
            $config->update([$field => $next]);
            $config->refresh();
            $config->update([
                'is_active' => (bool) $config->new_job_slack_active || (bool) $config->assignment_slack_active,
            ]);
        } catch (QueryException $e) {
            return redirect()
                ->route('settings.slack_config')
                ->withErrors([
                    'slack_config' => 'Could not update Slack switch. Ensure slack_configs has new_job_slack_active and assignment_slack_active columns.',
                ]);
        }

        $label = $purpose === 'new_job' ? 'New job Slack' : 'Assignment Slack';
        $state = $next ? 'on' : 'off';

        return redirect()
            ->back()
            ->with('success', "{$label} notifications turned {$state}.");
    }

    /**
     * Servers deployed without running migrations still get the per-branch columns.
     */
    private function ensureBranchColumns(): void
    {
        foreach (['new_job_branches', 'new_job_branch_colors'] as $column) {
            try {
                if (Schema::hasTable('slack_configs') && ! Schema::hasColumn('slack_configs', $column)) {
                    Schema::table('slack_configs', function ($table) use ($column) {
                        $table->text($column)->nullable();
                    });
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
