<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlackConfig extends Model
{
    protected $table = 'slack_configs';

    protected $fillable = [
        'name',
        'webhook_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
