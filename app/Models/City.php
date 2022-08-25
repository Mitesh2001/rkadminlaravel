<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
	
	protected $primaryKey = 'id';
	
	protected $fillable = [
	  'name', 'state_id', 'country_id', 'deleted',
	];
	
	//public $timestamps = false;
	
	public function state(){
	  return $this->hasOne('App\Models\State','state_id', 'state_id')->where('states.deleted', '0');
	}
	
	public function country(){
	  return $this->hasOne('App\Models\Country','country_id', 'country_id')->where('countries.deleted', '0');
	}
}
