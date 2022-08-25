<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clients extends Model
{
    use SoftDeletes;
	
	protected $table = 'clients';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
	
	public function industry_type()
    {
        return $this->hasOne(IndustryType::Class, 'id', 'industry_id');
    }
	
	public function company_type()
    {
        return $this->hasOne(CompanyType::Class, 'id', 'company_type_id');
    }
	
	public function state()
    {
        return $this->hasOne(State::Class, 'state_id', 'state_id');
    }
	
	public function country()
    {
        return $this->hasOne(Country::Class, 'country_id', 'country_id');
    }
	
	public function company()
    {
        return $this->hasMany(Company::Class, 'client_id');
    }
}
