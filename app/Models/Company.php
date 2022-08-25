<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
	use SoftDeletes;
	
    protected $table = 'company';

    protected $guarded = [];

    public function plan_data()
    {
        return $this->belongsTo('App\Models\Plan', 'plan_id', 'id');
    }

    public function client_data()
    {
        return $this->belongsTo('App\Models\Client', 'client_id', 'id');
    }

    public function state_data()
    {
        return $this->belongsTo('App\Models\State', 'state_id','state_id');
    }

    public function country_data()
    {
        return $this->belongsTo('App\Models\Country', 'country_id','country_id');
    }
}
