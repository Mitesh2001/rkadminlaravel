<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TeleCallerContact;

class ConstructionContacts extends Model
{
    use SoftDeletes;
	
	protected $table = 'construction_contacts';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
	
	public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function getAssignContact()
    {
        return $this->hasMany(TeleCallerContact::class, 'contact_id');
    }
    
    public function ca()
    {
        return $this->belongsTo(TeleCallerContact::class, 'contact_id');
    }
}
