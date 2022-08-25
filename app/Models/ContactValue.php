<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactValue extends Model
{
    use SoftDeletes;
	
	protected $table = 'contact_values';

    public function getContactValue()
    {
        return $this->belongsTo('App\Models\Contacts','contact_id','id');
    }
}
