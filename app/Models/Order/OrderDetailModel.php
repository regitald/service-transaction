<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetailModel extends Model
{
    use SoftDeletes;
    protected $table   = 'order_detail';
	public $primarykey = 'order_detail_id';
	public $timestamps = true;
	protected $fillable = [
		'order_id',
		'product_id',
        'qty',
        'price',
        'special_instructions'
	];
		
	protected $hidden = [
		'created_at','updated_at','deleted_at'
	];
	public function order() {
		return $this->hasMany('App\Models\Order\OrderModel', 'order_id','id');
  	}
}
