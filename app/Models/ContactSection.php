<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactSection extends Model
{
    use SoftDeletes;
	
	protected $table = 'contact_sections';

    public function getFieldData()
    {
        return $this->hasMany('App\Models\ContactField','section_id');//->select('id','section_id','is_pre_field','input_type','label_name','is_required','is_searchable','is_select_multiple','minlength','maxlength','minvalue','maxvalue','pattern')
    }
}
