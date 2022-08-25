<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadsComments extends Model
{
    use SoftDeletes;
	
	protected $table = 'leads_comments';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
	
	public function lead()
    {
        return $this->hasOne(Lead::class, 'id' , 'lead_id');
    }
	
	public function user()
    {
        return $this->hasOne(User::class, 'id' , 'user_id');
    }
}
