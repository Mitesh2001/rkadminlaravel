<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpAssign extends Model
{
    public function getAssignToData()
    {
        return $this->belongsTo('App\Models\User','user_id')->select('id','name','email','mobileno');
    }

    public function getCreatedBy()
    {
        return $this->belongsTo('App\Models\User','created_by')->select('id','name','email','mobileno');
    }
	public function getFollowUp()
    {
        return $this->belongsTo(FollowUp::class,'follow_up_id','id');
        //return $this->belongsTo(FollowUp::class,'id','follow_up_id');
    }
}
