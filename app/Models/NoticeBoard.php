<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeBoard extends Model
{
    
    public function getCreatedBy()
    {
        return $this->belongsTo('App\Models\User','created_by')->select('id','name')->withTrashed();
    }

    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id')->select('id','name')->withTrashed();
    }
}
