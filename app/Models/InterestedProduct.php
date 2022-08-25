<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterestedProduct extends Model
{
    
    use SoftDeletes;
    
    public function getProductData(){
        return $this->belongsTo('App\Models\Products','product_id','id')->select('id','skucode','name','description','product_type','description');
    }
}
