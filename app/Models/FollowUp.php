<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class FollowUp extends Model
{
    protected $table = 'follow_up';
	protected $with = ['getLead','getContact'];

    public function getAssignToData()
    {
        return $this->belongsTo('App\Models\User','user_id')->select('id','name','email','mobileno');
    }

    public function getCreatedBy()
    {
        return $this->belongsTo('App\Models\User','created_by')->select('id','name','email','mobileno');
    }

    public function getRole()
    {
        return $this->belongsTo(Role::class,'role_id')->select('id','name');
    }
	
	public function getLead()
    {
        return $this->belongsTo('App\Models\Lead','follow_up_id','id')->join('follow_up', function($join){$join->on('follow_up.follow_up_id','=','leads.id');})->where('follow_up.type','=',1)->select('leads.*');
    }
	
	public function getContact()
    {
        return $this->belongsTo('App\Models\Contacts','follow_up_id','id')->join('follow_up', function($join){$join->on('follow_up.follow_up_id','=','contacts.id');})->where('follow_up.type','=',2)->select('contacts.*');
    }

    public function getFollowUpAssign()
    {
        return $this->hasMany(FollowUpAssign::class,'follow_up_id');
    }
	
}
