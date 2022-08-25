<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;
	
	protected $table = 'organizations';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
	
	public function employees()
    {
        return $this->hasMany(Employee::class);
    }
	
	public function departments()
    {
        return $this->hasMany(Department::class);
    }
	
	public function industry_types()
    {
        return $this->belongsTo(IndustryType::class);//,'industry_id','id'
    }
	
	public function users()
    {
        return $this->belongsTo(User::class);//,'assigned_to','id'
    }
}
