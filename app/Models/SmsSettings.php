<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsSettings extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $table = 'sms_settings';

    protected $fillable = [
        'api_url', 'parameters', 'final_url', 'updated_by', 'mobile_param', 'msg_param', 'is_tested', 'is_working',
    ];
}
