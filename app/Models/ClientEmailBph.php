<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEmailBph extends Model
{
    protected $table = 'client_email_bph';

    public $timestamps = false;

    protected $fillable = [
        'email',
    ];
}
