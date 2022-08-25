<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $primaryKey = 'country_id';

	protected $table = 'countries';

	protected $fillable = [
	  'name', 'sortname', 'phonecode', 'deleted'
	];

	public $timestamps = false;
}
