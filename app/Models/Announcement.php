<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    
    public function getCreatedBy()
    {
        return $this->belongsTo('App\Models\User','created_by')->select('id','name')->withTrashed();
    }
}
