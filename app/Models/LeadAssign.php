<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAssign extends Model
{
    
    public function getCreatedBy()
    {
        return $this->belongsTo('App\Models\User','created_by')->select('id','name','mobileno');;
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
        return $this->belongsTo(Lead::Class, 'lead_id', 'id');
    }
}
