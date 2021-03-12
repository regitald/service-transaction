<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Models\PaymentMethodModel;
use App\Models\ShippingMethodModel;
use App\Models\Order\OrderDetailModel;
use App\Models\Order\OrderModel;

trait GeneralServices {

    public function ResponseJson($status,$message,$data = null){
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data 
        ];
		if($status != 200){
			$response = [
				'status' => false,
				'message' => $message
			];
		}
		return response()->json($response, $status);
	}
    function ValidateRequest($params,$rules){

		$validator = Validator::make($params, $rules);

		if ($validator->fails()) {
			$response = [
				'status' => false,
				// 'message' => $validator->messages()
				'message' =>  $validator->errors()->first()
			];
			return response()->json($response, 406);
		}
	}   

	//===============================Services Get Data =============================
	public function generateOrderCode(){
		$data = OrderModel::select('*')->orderBy('order_id','DESC')->first();
		if(empty($data)){
			return 'ID-'.str_pad(1, 8, "0", STR_PAD_LEFT);
		}
		return 'ID-'.str_pad($data->id + 1, 8, "0", STR_PAD_LEFT);
	}
	public function getOrderData($member_id,$is_history=null)
	{
		$query = OrderModel::select('*')->where('member_id',$member_id)
				->with(['details','payment_method','shipping_method']);
		if($is_history == 'true'){
			return $query->whereIn('order_status',['1','2'])->get();
		}else{
			return $query->where('order_status','0')->first();
		}
	}
	public function getReceipt($order_id)
	{
		return OrderModel::select('*')->where('order_id',$order_id)
				->with(['details','payment_method','shipping_method'])->first();
	}
	public function getOrderDetailByProductid($order_id,$product_id)
	{
		return OrderDetailModel::select('*')->where('order_id',$order_id)->where('product_id',$product_id)->first();
	}
	public function GetOrderDetailsumPrice($order_id)
	{
		return OrderDetailModel::select('*')->where('order_id',$order_id)->sum('price');
	}

	public function postDetailOrder($order_id,$data){
		$postRequestDetailOrder = [
			'order_id' => $order_id,
			'product_id' => $data['product_id'],
			'qty' => $data['qty'],
			'price' => $data['qty']*$data['price'],
			'special_instructions' => $data['special_instructions']
		];
		return OrderDetailModel::create($postRequestDetailOrder);
	}
}