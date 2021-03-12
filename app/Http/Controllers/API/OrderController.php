<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\GeneralServices;
use App\Models\PaymentMethodModel;
use App\Models\ShippingMethodModel;
use App\Models\Order\OrderDetailModel;
use App\Models\Order\OrderModel;

class OrderController extends Controller
{
	use GeneralServices;

	public function index(Request $request){
		$role = [
			'member_id' => 'Required|integer',
			'is_history' => 'required|in:true,false'
		];

		$validateData = $this->ValidateRequest($request->all(), $role);

		if (!empty($validateData)) {
			return $validateData;
		}

		$getData = $this->getOrderData($request->member_id,$request->is_history);

        return $this->ResponseJson(200,"Cart Data",$getData);
	}
	public function store(Request $request){
		$role = [
			'member_id' => 'Required|integer',
			'product_id' => 'Required|integer',
			'qty' => 'Required|integer|min:1',
			'price' => 'Required|numeric',
			'special_instructions' => 'nullable|string',
		];

		$validateData = $this->ValidateRequest($request->all(), $role);

		if (!empty($validateData)) {
			return $validateData;
		}

		$checkOrderIfExist = $this->getOrderData($request->member_id,false);
		if (empty($checkOrderIfExist)) {
			$orderCode = $this->generateOrderCode();
			$postRequestOrder = [
				'order_code' => $orderCode,
				'member_id' => $request->member_id,
				'order_total_price' => $request->qty*$request->price,
			];
			$saveOrder = OrderModel::create($postRequestOrder);
			if(!$saveOrder){
				return $this->ResponseJson(404,"Failed! Server Error!.");
			}
		
			$this->postDetailOrder($saveOrder->id,$request->all());
       
		}else{
			$checkOrderDetailbyProductId = $this->getOrderDetailByProductid($checkOrderIfExist->order_id,$request->product_id);
			if (empty($checkOrderDetailbyProductId)) {
				$saveOrderDetail = $this->postDetailOrder($checkOrderIfExist->order_id,$request->all());
			}else{
				$total_detail = $checkOrderDetailbyProductId->price + ($request->qty*$request->price);
				$postUpdateDetailOrder = [
					'qty' => $checkOrderDetailbyProductId->qty + $request->qty,
					'price' => $total_detail
				];
				 OrderDetailModel::where('order_detail_id',$checkOrderDetailbyProductId->id)->where('product_id',$request->product_id)->update($postUpdateDetailOrder);
			}
			$updateOrderPrice['order_total_price'] = $this->GetOrderDetailsumPrice($checkOrderIfExist->order_id);
			
			OrderModel::where('order_id',$checkOrderIfExist->order_id)->update($updateOrderPrice);
		}
		
        return $this->ResponseJson(200,"Success add item to cart",$request->all());
	}
	public function updateQty(Request $request){
		$role = [
			'order_detail_id' => 'Required|integer',
			'product_id' => 'Required|integer',
			'qty' => 'Required|integer|min:1',
			'price' => 'Required|numeric'
		];

		$validateData = $this->ValidateRequest($request->all(), $role);

		if (!empty($validateData)) {
			return $validateData;
		}
		$checkOrderDetailbyId = OrderDetailModel::select('*')->where('order_detail_id',$request->order_detail_id)->where('product_id',$request->product_id)->first();

			
		if (empty($checkOrderDetailbyId)) {
			$this->ResponseJson(404,"Invalid Order Detail Id");
	   	}
		$total_price = $request->qty*$request->price;
		$postUpdateDetailOrder = [
			'qty' => $request->qty,
			'price' => $total_price
		];
		OrderDetailModel::where('order_detail_id',$request->order_detail_id)->where('product_id',$request->product_id)->update($postUpdateDetailOrder);
		
		$updateOrderPrice['order_total_price'] = $this->GetOrderDetailsumPrice($checkOrderDetailbyId->order_id);
			
		OrderModel::where('order_id',$checkOrderDetailbyId->order_id)->update($updateOrderPrice);

        return $this->ResponseJson(200,"Success updated qty",array());
	}
	public function delete(Request $request){
		$role = [
			'order_detail_id' => 'Required|integer'
		];

		$validateData = $this->ValidateRequest($request->all(), $role);

		if (!empty($validateData)) {
			return $validateData;
		}
		$checkOrderDetailbyId = OrderDetailModel::select('*')->where('order_detail_id',$request->order_detail_id)->first();
		if (empty($checkOrderDetailbyId)) {
        	 $this->ResponseJson(404,"Invalid Order Detail Id");
		}
		OrderDetailModel::where('order_detail_id',$request->order_detail_id)->delete();
		
		$updateOrderPrice['order_total_price'] = $this->GetOrderDetailsumPrice($checkOrderDetailbyId->order_id);
		OrderModel::where('order_id',$checkOrderDetailbyId->order_id)->update($updateOrderPrice);

        return $this->ResponseJson(200,"Success deleted item",array());
	}
	public function checkout(Request $request){
		$role = [
			'member_id' => 'Required|integer',
			'member_email' => 'Required|string',
			'member_address' => 'Required|string',
			'payment_method_id' => 'Required|string',
			'shipping_id' => 'Required|string',
			'distance' => 'Required|integer',
			'attachment' => 'nullable|string',
		];

		$validateData = $this->ValidateRequest($request->all(), $role);

		if (!empty($validateData)) {
			return $validateData;
		}
		$checkOrderIfExist = $this->getOrderData($request->member_id,false);
		if (empty($checkOrderIfExist)) {
			return $this->ResponseJson(406,"Order data not found",array());
		}
		$shippingData =  ShippingMethodModel::select('*')->where('id',$request->shipping_id)->first();
		if (empty($shippingData)) {
			return $this->ResponseJson(406,"Shipping data not found",array());
		}
		$calculateShippingData = ($shippingData->cost*$request->distance)/$shippingData->distance;
		$total_price = $checkOrderIfExist->order_total_price+$calculateShippingData;

		$updateDta = [
			'member_email' => $request->member_email,
			'member_address' => $request->member_address,
			'payment_method_id' => $request->payment_method_id,
			'shipping_id' => $request->shipping_id,
			'distance' => $request->distance,
			'attachment' => $request->attachment,
			'shipping_cost' => $calculateShippingData,
			'order_total_price' => $total_price,
			'order_status' => '1',
		];
			
		$checkout = OrderModel::where('order_id',$checkOrderIfExist->order_id)->update($updateDta);

		if(!$checkout){
			return $this->ResponseJson(404,"Failed! Server Error!.");
		}
        return $this->ResponseJson(200,"Success checkout",$this->getReceipt($checkOrderIfExist->order_id));
	}
}
