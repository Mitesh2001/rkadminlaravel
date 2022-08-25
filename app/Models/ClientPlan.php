<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPlan extends Model
{
    protected $table = 'clients_plan';
	
	public function plan()
    {
        return $this->hasOne('App\Models\Plan','id','plan_id');
    }
}
