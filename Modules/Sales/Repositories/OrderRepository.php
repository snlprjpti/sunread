<?php

namespace Modules\Sales\Repositories;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $order)
    {
        $this->model = $order;
        $this->model_key = "orders";
        $this->rules = [
            //website_id
            /**
             * website_id
             * store_id
             * product_id
             * shipping_method_id
             * payment_method_id
             * currency_code
             * coupon_code
             * qty
             * ///is guest 
             * customer_name
             * customer_phone
             * customer_taxVat
             * // customer details
             * 
             */
            "orders" => "required|array",
            "*orders.product_id" => "required|exists:products,id",
            "*orders.qty" => "required|decimal",
            "coupon_code" => "sometimes|exists:coupons,code"
        ];
    }

    public function store(object $request): mixed
    {
        DB::beginTransaction();
        try
        {
            dd($request);
            // validate params
            
            
            
            
        } 
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return  '';
    }



}
