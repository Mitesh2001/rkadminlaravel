<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactField extends Model
{
    use SoftDeletes;
	
	protected $table = 'contact_fields';
}
