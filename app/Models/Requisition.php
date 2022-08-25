<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
	protected $table = 'requisitions';
	
    public function client()
    {
        return $this->belongsTo('App\Models\Clients', 'client_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }
}
