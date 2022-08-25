<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeValue extends Model
{
    use SoftDeletes;
	
	protected $table = 'employee_values';

    public function getEmployeeValue()
    {
        return $this->belongsTo('App\Models\Employee','employee_id','id');
    }
}
