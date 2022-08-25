<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStageHistory extends Model
{
    protected $table = 'lead_stage_history';

    // get user
    public function getUserData()
    {
        return $this->belongsTo('App\Models\User','user_id','id')->select('id','name','mobileno');
    }
}
