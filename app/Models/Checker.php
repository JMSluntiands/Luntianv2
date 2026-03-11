<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checker extends Model
{
    protected $table = 'checker';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'checker_id',
        'name',
        'username',
        'password',
    ];
}

