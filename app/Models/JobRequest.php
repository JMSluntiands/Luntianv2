<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /** Luntian add/list: LT01, Luntian (any case), or EA_LT_* request IDs. */
    public function scopeForLuntianVertical(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereRaw('LOWER(TRIM(client_code)) IN (?, ?)', ['lt01', 'luntian'])
                ->orWhereRaw("job_request_id LIKE 'EA\\_LT\\_%'");
        });
    }
}
