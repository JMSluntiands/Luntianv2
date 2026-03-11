<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRequest extends Model
{
    protected $table = 'job_requests';

    public $timestamps = false;

    protected $fillable = ['client_code', 'job_request_id', 'job_request_type'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code');
    }
}
