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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'new_job_slack_active' => 'boolean',
        'assignment_slack_active' => 'boolean',
    ];
}
