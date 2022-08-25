<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;
	
	protected $table = 'products';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
	
	public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function company_name()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'category_id', 'id')->select('id','name');
    }
	
}
