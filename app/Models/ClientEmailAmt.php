<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEmailAmt extends Model
{
    protected $table = 'client_email_amt';

    public $timestamps = false;

    protected $fillable = [
        'email',
    ];
}
