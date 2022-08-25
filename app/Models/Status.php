<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	//protected $dates = ['deleted_at'];
	
	public function lead_status()
    {
        return $this->belongsToMany(Lead::Class);
    }
}
