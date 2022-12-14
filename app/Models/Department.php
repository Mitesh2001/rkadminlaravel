<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
	
	protected $table = 'departments';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
	
	public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }
	
	public function organizations()
    {
        return $this->belongsToMany(Organization::class);
    }
	
	public function industry_type()
	{
        return $this->belongsTo(IndustryType::class);
    }
}
