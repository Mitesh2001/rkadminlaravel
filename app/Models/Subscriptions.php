<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriptions extends Model
{
	use SoftDeletes;
	
	protected $table = 'subscriptions';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
		
	public function company()
    {
        return $this->hasOne('App\Models\Company','id','company_id');
    }
	
	public function client()
    {
        return $this->hasOne('App\Models\Client','id','client_id');
    }
}
