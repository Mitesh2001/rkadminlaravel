<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyType extends Model
{
    //use SoftDeletes;
	
	protected $table = 'company_type';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	//protected $dates = ['deleted_at'];
	
	public function organizations()
    {
        return $this->hasMany(Organization::class, 'ctype_id' , 'id');
    }
}
