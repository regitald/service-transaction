<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderModel extends Model
{
    use SoftDeletes;
    protected $table   = 'order';
	public $primarykey = 'order_id';
	public $timestamps = true;
	protected $fillable = [
		'order_code',
		'member_id',
        'member_email',
        'member_address',
        'payment_method_id',
        'shipping_id',
        'distance',
        'shipping_cost',
        'attachment',
        'order_total_price',
        'status'
	];
		
	protected $hidden = [
		'created_at','updated_at','deleted_at'
	];
	public function details() {
		return $this->hasMany('App\Models\Order\OrderDetailModel', 'order_id','order_id');
  	}
	public function payment_method() {
		return $this->belongsTo('App\Models\PaymentMethodModel', 'payment_method_id','id');
  	}
    public function shipping_method() {
		return $this->belongsTo('App\Models\ShippingMethodModel', 'shipping_id','id');
  	}
}
