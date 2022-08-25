<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;
use App\Models\Client;
use Auth;

use App\Notifications\AdminResetPasswordNotification; // Or the location that you store your notifications (this is default).

class User extends Authenticatable implements JWTSubject
{
	use SoftDeletes;
    use HasRoles;
    use Notifiable;

    //protected $guard = 'web';
	protected $guard_name = 'api';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobileno', 'alt_mobileno', 'designation', 'gender', 'dob', 'picture', 'address_line_1', 'address_line_2', 'country_id', 'state_id', 'city', 'pincode', 'facebook', 'twitter' ,'instagram', 'website', 'isLocked', 'lockedDate', 'parent_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','token','forgetPasswordToken','noOfLoginAttempts',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
	
	public function lHistory()
    {
        return $this->hasMany('App\Models\LoginHistory', 'user_id', 'user_id')->orderBy('login_history.created_at', 'DESC');
    }
	
	public function organizations()
    {
        return $this->belongsTo(Client::class,'organization_id','id');
    }

    public function organizationName()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
	
	/* public function roles()
    {
        return $this->belongsToMany(Role::class);
    } */

    // public function sendPasswordResetNotification($token)
    // {   
    //     $this->notify(new AdminResetPasswordNotification($token));
    // }

}
