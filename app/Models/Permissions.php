<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;

class Permissions extends Permission
{
	protected $table = 'permissions';
    protected $fillable = [
        'deleted'
    ];
}
