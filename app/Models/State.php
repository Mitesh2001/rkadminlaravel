<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $primaryKey = 'state_id';

	protected $table = 'states';

	protected $fillable = [
	  'name', 'country_id', 'deleted',
	];

	//public $timestamps = false;

	public function country(){
	  return $this->hasOne('App\Models\Country','country_id', 'country_id')->where('countries.deleted', '0');
	}
}
