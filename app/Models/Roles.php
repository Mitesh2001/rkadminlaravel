<?php

namespace App\Models;

use Spatie\Permission\Models\Role;

class Roles extends Role
{
	protected $table = 'roles';
	
    protected $fillable = [
        'name', 'guard_name', 'parent_id',  'deleted'
    ];
	
	public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = 'api';
        return static::query()->create($attributes);
    }
	
	public function reports()
    {
        return $this->whereIn('id', $this->parent_id)->get();
    }
	
	public function permissions()
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            'permission_id',
            'role_id'
        );
    }
}
