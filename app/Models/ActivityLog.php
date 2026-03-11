<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'activity_date',
        'activity_type',
        'activity_description',
        'updated_by',
    ];
}

