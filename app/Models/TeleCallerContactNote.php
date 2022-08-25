<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeleCallerContactNote extends Model
{
    protected $table = 'tele_caller_contact_notes';
    public function get_user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name','organization_id','company_id','email','mobileno');
    }
}
