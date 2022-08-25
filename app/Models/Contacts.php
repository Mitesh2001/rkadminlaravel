<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacts extends Model
{
    use SoftDeletes;
	
	protected $table = 'contacts';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
	
	public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
	
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

    public function createdBy()
    {
        return $this->belongsTo(User::Class, 'created_by')->select('id','name','mobileno');
    }

    public function getAssignContact()
    {
        return $this->hasMany(TeleCallerContact::class, 'contact_id');
    }
}
