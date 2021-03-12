<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\GeneralServices;
use App\Models\PaymentMethodModel;
use App\Models\ShippingMethodModel;

class GeneralController extends Controller
{
    use GeneralServices;

	public function shipping(Request $request){
		$getData = ShippingMethodModel::all();

        return $this->ResponseJson(200,"Shipping List",$getData);
	}
    public function payment(Request $request){
		$getData = PaymentMethodModel::all();

        return $this->ResponseJson(200,"Payment Method List",$getData);
	}
}
