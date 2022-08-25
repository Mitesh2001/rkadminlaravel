<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryPincode extends Model
{
    protected $table = 'country_pincode';
	
	public $timestamps = false;
	
	protected $primaryKey = null;
	
    public $incrementing = false;
	
	protected $fillable = [
	  'country', 'pincode', 'district', 'state', 'pincode_name',
	];
	
	public function country(){
		return $this->hasOne(Country::Class, 'country_id', 'country_id');
	}
}
