<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['client_code', 'client_name', 'client_email'];

    public function jobRequests()
    {
        return $this->hasMany(JobRequest::class, 'client_code', 'client_code');
    }
}
