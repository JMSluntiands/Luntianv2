<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAccount extends Model
{
    protected $table = 'client_accounts';

    protected $primaryKey = 'client_account_id';

    public $timestamps = false;

    protected $fillable = ['client_account_name'];
}
