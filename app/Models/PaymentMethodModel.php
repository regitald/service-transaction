<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethodModel extends Model
{
    use SoftDeletes;
    protected $table   = 'paymentmethod';
	public $primarykey = 'id';
	public $timestamps = true;
	protected $fillable = [
		'payment_method_name',
		'payment_method_currency'
	];
		
	protected $hidden = [
		'created_at','updated_at','deleted_at'
	];
	public function order() {
		return $this->hasMany('App\Models\Order\OrderModel', 'shipping_id','id');
  	}
}
