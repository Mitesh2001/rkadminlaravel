<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends Model
{
	use SoftDeletes;
	
    protected $primaryKey = 'sms_template_id';

    protected $table = 'sms_templates';

    protected $fillable = [
        'name', 'content', 'client_id', 'company_id', 'createdBy', 'updatedBy'
    ];

    public function parseContent($data)
    {
        $parsed = preg_replace_callback('/{(.*?)}/', function ($matches) use ($data) {
            list($shortCode, $index) = $matches;
            return (isset($data[$shortCode])) ? $data[$shortCode] : $shortCode;
        }, $this->content);
        return $parsed;
    }
	
	public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
