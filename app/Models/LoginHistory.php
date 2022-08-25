<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $primaryKey = 'lh_id';

    protected $table = 'login_history';

    protected $fillable = [
        'user_id', 'ipaddress'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
