<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingMethodModel extends Model
{
    use SoftDeletes;
    protected $table   = 'shipping_method';
	public $primarykey = 'id';
	public $timestamps = true;
	protected $fillable = [
		'shipping_name',
		'cost',
        'distance'
	];
		
	protected $hidden = [
		'created_at','updated_at','deleted_at'
	];
	public function order() {
		return $this->hasMany('App\Models\Order\OrderModel', 'shipping_id','id');
  	}
}
