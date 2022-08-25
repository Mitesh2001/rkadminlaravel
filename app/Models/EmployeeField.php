<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeField extends Model
{
    use SoftDeletes;
	
	protected $table = 'employee_fields';
}
