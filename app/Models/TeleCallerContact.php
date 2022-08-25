<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeleCallerContact extends Model
{
    protected $table = 'tele_caller_contacts';
	
    public function getCreatedBy()
    {
        return $this->belongsTo('App\Models\User','created_by')->select('id','name','mobileno');
    }

    public function getUserData()
    {
        return $this->belongsTo('App\Models\User','user_id')->select('id','name','mobileno');;
    }

    public function getUnlockUserData()
    {
        return $this->belongsTo('App\Models\User','unlocked_by')->select('id','name','mobileno');;
    }

    public function getModel()
    {
        return $this->belongsTo(Contacts::Class, 'contact_id', 'id');
    }
}
