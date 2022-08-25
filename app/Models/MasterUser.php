<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterUser extends Model
{
    use SoftDeletes;

    protected $table = 'master_users';
}
