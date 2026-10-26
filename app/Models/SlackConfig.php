<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlackConfig extends Model
{
    protected $table = 'slack_configs';

    protected $fillable = [
        'name',
        'webhook_url',
        'webhook_new_job_url',
        'webhook_assignment_url',
        'is_active',
        'new_job_slack_active',
        'assignment_slack_active',
        'new_job_branches',
        'new_job_branch_colors',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'new_job_slack_active' => 'boolean',
        'assignment_slack_active' => 'boolean',
        'new_job_branches' => 'array',
        'new_job_branch_colors' => 'array',
    ];

    /**
     * Saved Slack side-bar color for the branch, or null to use the default.
     */
    public function newJobBranchColor(string $branch): ?string
    {
        $colors = $this->new_job_branch_colors;
        $color = is_array($colors) ? ($colors[$branch] ?? null) : null;

        return is_string($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? strtoupper($color) : null;
    }

    /**
     * Whether new-job Slack messages are enabled for the given branch label (NULL list = all branches).
     */
    public function newJobBranchEnabled(string $branch): bool
    {
        $branches = $this->new_job_branches;

        return ! is_array($branches) || in_array($branch, $branches, true);
    }
}
