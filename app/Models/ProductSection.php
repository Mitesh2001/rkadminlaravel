<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSection extends Model
{
    use SoftDeletes;
	
	protected $table = 'product_sections';
    
    public function getFieldData()
    {
        return $this->hasMany('App\Models\ProductField','section_id');//->select('id','section_id','input_type','label_name','is_required','is_searchable','is_select_multiple','minlength','maxlength','minvalue','maxvalue','pattern')
    }
}
