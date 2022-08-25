<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductValue extends Model
{
    use SoftDeletes;
	
	protected $table = 'product_values';

    public function getProductValue()
    {
        return $this->belongsTo('App\Models\Products','product_id','id');
    }
}
