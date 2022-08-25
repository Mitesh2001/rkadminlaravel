<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSmsLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $table = 'email_sms_logs';

    protected $fillable = [
        'user_id', 'type', 'template_id', 'client_id', 'client_number', 'client_email'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /* public function email()
    {
        return $this->hasOne('App\Models\EmailTemplate', 'email_template_id', 'template_id');
    }

    public function sms()
    {
        return $this->hasOne('App\Models\SmsTemplate', 'sms_template_id', 'template_id');
    } */
}
