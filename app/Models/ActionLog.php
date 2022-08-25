<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $table = 'action_logs';

    protected $fillable = [
        'user_id', 'module_id', 'module', 'action', 'oldData', 'newData'
    ];

    protected $casts = [
        'oldData' => 'array',
        'newData' => 'array',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id','id');
    }
}
