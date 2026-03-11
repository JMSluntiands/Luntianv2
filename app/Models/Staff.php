<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'staff_id',
        'name',
        'username',
        'password',
    ];
}

