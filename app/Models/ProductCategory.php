<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductCategory extends Model
{
    use SoftDeletes;
	
	protected $table = 'product_categories';
    
    public function getCreatedBy()
    {
        return $this->belongsTo('App\Models\User','created_by')->withTrashed();
    }
}
